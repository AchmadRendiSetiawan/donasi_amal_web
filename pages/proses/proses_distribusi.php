<?php
session_start();
include '../../config.php'; // Pastikan jalur ini benar relatif terhadap proses_distribusi.php

// Validasi CSRF Token
// hash_equals() adalah cara yang lebih aman untuk membandingkan string daripada '==='
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    // Hentikan eksekusi dan berikan pesan error yang jelas
    die('Invalid CSRF token. Request blocked.');
}
// Hapus token dari session setelah digunakan untuk mencegah replay attacks
unset($_SESSION['csrf_token']);

// Ambil dan validasi input menggunakan filter_input yang lebih aman
$id_program = filter_input(INPUT_POST, 'id_program', FILTER_VALIDATE_INT);
$id_penerima = filter_input(INPUT_POST, 'id_penerima', FILTER_VALIDATE_INT);
$jumlah_total = filter_input(INPUT_POST, 'jumlah_total', FILTER_VALIDATE_FLOAT);

// Validasi dasar input
if ($id_program === false || $id_program === null ||
    $id_penerima === false || $id_penerima === null ||
    $jumlah_total === false || $jumlah_total === null || $jumlah_total <= 0) {
    // Redirect dengan pesan error yang lebih spesifik
    $_SESSION['error_message'] = "Data tidak valid. Pastikan semua kolom terisi dengan benar dan jumlah lebih dari 0.";
    header("Location: ../penyaluran.php?error=" . urlencode($_SESSION['error_message']));
    exit();
}

try {
    // MODIFIED: Dapatkan status program dan hitung dana yang tersedia dari riwayat_donasi
    // Query ini menjumlahkan semua donasi untuk program yang diberikan dari tabel 'riwayat_donasi'.
    $stmt = $conn->prepare("
        SELECT
            pa.status,
            COALESCE(SUM(rd.jumlah_donasi), 0) AS available_funds
        FROM
            program_amal pa
        LEFT JOIN
            riwayat_donasi rd ON pa.id_program = rd.id_program
        WHERE
            pa.id_program = ?
        GROUP BY
            pa.id_program, pa.status -- Group by id_program juga jika ada kolom lain di SELECT
    ");
    $stmt->bind_param("i", $id_program);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        $_SESSION['error_message'] = "Program tidak ditemukan.";
        header("Location: ../penyaluran.php?error=" . urlencode($_SESSION['error_message']));
        exit();
    }

    $program_status = $row['status'];
    $available_funds = $row['available_funds'];

    // MODIFIED: Izinkan distribusi dari program 'Aktif' atau 'Selesai'
    // Dan periksa ketersediaan dana
    if (!in_array($program_status, ['Aktif', 'Selesai'])) {
        $_SESSION['error_message'] = "Program tidak dalam status yang bisa disalurkan (Status: " . htmlspecialchars($program_status) . ")";
        header("Location: ../penyaluran.php?error=" . urlencode($_SESSION['error_message']));
        exit();
    }

    // Hitung total dana yang sudah disalurkan untuk program ini
    $stmt_disbursed = $conn->prepare("SELECT COALESCE(SUM(jumlah_disalurkan), 0) FROM penyaluran_donasi WHERE id_program = ?");
    $stmt_disbursed->bind_param("i", $id_program);
    $stmt_disbursed->execute();
    $stmt_disbursed->bind_result($total_disbursed);
    $stmt_disbursed->fetch();
    $stmt_disbursed->close();

    // Periksa apakah dana mencukupi setelah memperhitungkan yang sudah disalurkan
    if (($available_funds - $total_disbursed) < $jumlah_total) {
        $_SESSION['error_message'] = "Dana pada program tidak mencukupi. Sisa dana tersedia: Rp " . number_format($available_funds - $total_disbursed, 0, ',', '.');
        header("Location: ../penyaluran.php?error=" . urlencode($_SESSION['error_message']));
        exit();
    }


    // Mulai transaksi untuk atomicity
    $conn->begin_transaction();

    // *******************************************************************
    // PENTING: BARIS BERIKUT INI DIHAPUS KARENA KEMUNGKINAN BESAR
    // STORED PROCEDURE 'distribusi_donasi' SUDAH MENCATAT KE TABEL INI.
    // Jika Anda yakin 'distribusi_donasi' TIDAK melakukan INSERT ke penyaluran_donasi,
    // maka Anda bisa mengembalikan baris ini, tetapi Anda harus memastikan
    // bahwa skrip PHP ini tidak dijalankan dua kali (misal dengan unique constraint).
    // *******************************************************************
    // $stmt = $conn->prepare("INSERT INTO penyaluran_donasi (id_program, id_penerima, jumlah_disalurkan, status_distribusi) VALUES (?, ?, ?, 'Selesai')");
    // $stmt->bind_param("iid", $id_program, $id_penerima, $jumlah_total);
    // $stmt->execute();

    // Panggil stored procedure untuk logika distribusi
    // Asumsi: Stored procedure 'distribusi_donasi' akan melakukan INSERT ke tabel 'penyaluran_donasi'
    // dan mungkin juga update data lain yang relevan.
    // MODIFIED: Mengubah panggilan stored procedure sesuai dengan pesan error
    // Sekarang hanya mengirim 2 parameter: id_program dan jumlah_total
    $stmt_distribusi = $conn->prepare("CALL distribusi_donasi(?, ?)");
    $stmt_distribusi->bind_param("id", $id_program, $jumlah_total); // Sesuaikan tipe parameter
    $stmt_distribusi->execute();
    $stmt_distribusi->close(); // Tutup statement setelah eksekusi

    // Update status program setelah distribusi (jika stored procedure tidak menanganinya)
    // Stored procedure 'update_status_program' ini harus memeriksa apakah sisa saldo
    // (berdasarkan riwayat_donasi dikurangi penyaluran_donasi) adalah 0 dan kemudian
    // mengatur status ke 'Selesai_Distribusi' atau yang serupa.
    $stmt_update_program = $conn->prepare("CALL update_status_program(?)");
    $stmt_update_program->bind_param("i", $id_program);
    $stmt_update_program->execute();
    $stmt_update_program->close(); // Tutup statement setelah eksekusi

    // Commit transaksi
    $conn->commit();

    $_SESSION['success_message'] = "Distribusi berhasil dilakukan.";
    header("Location: ../penyaluran.php?status=sukses");
    exit();

} catch (mysqli_sql_exception $e) { // Tangkap exception spesifik dari MySQLi
    // Rollback transaksi jika terjadi error
    $conn->rollback();
    error_log("Error distribusi (SQL): " . $e->getMessage()); // Log error untuk debugging
    $_SESSION['error_message'] = "Gagal melakukan distribusi: " . urlencode($e->getMessage()); // Tampilkan error yang lebih spesifik
    header("Location: ../penyaluran.php?error=" . urlencode($_SESSION['error_message']));
    exit();
} catch (Exception $e) { // Tangkap exception umum lainnya
    $conn->rollback();
    error_log("Error distribusi (Umum): " . $e->getMessage());
    $_SESSION['error_message'] = "Terjadi kesalahan tak terduga: " . urlencode($e->getMessage());
    header("Location: ../penyaluran.php?error=" . urlencode($_SESSION['error_message']));
    exit();
} finally {
    // Tutup koneksi jika masih terbuka
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
