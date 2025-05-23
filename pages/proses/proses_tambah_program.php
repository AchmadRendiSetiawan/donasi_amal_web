<?php
session_start(); // Pastikan ini ada di paling atas jika Anda menggunakan sesi
include '../../config.php'; // Verifikasi path ini

// Pengecekan apakah form disubmit dengan method POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama_program = filter_input(INPUT_POST, 'nama_program', FILTER_SANITIZE_STRING);
    $deskripsi = filter_input(INPUT_POST, 'deskripsi', FILTER_SANITIZE_STRING);
    // Menggunakan FILTER_VALIDATE_FLOAT untuk target_donasi, tambahkan FILTER_FLAG_ALLOW_FRACTION jika perlu
    $target_donasi = filter_input(INPUT_POST, 'target_donasi', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    // Pastikan nilai status yang diterima sesuai dengan yang diharapkan
    $status = isset($_POST['status']) && in_array($_POST['status'], ['Aktif', 'Tidak Aktif']) ? $_POST['status'] : 'Aktif';

    // Validasi input
    if (empty($nama_program) || empty($deskripsi) || $target_donasi === false || $target_donasi < 0) {
        // Redirect kembali dengan pesan error
        header("Location: ../program.php?error=Data+tidak+valid.+Pastikan+semua+kolom+terisi+dengan+benar.");
        exit();
    }

    try {
        // Query INSERT untuk program_amal
        $stmt = $conn->prepare("INSERT INTO program_amal (nama_program, deskripsi, target_donasi, status) VALUES (?, ?, ?, ?)");

        // Periksa apakah prepare berhasil
        if ($stmt === false) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("ssds", $nama_program, $deskripsi, $target_donasi, $status);

        // Periksa apakah execute berhasil
        if ($stmt->execute()) {
            header("Location: ../program.php?success=Program+berhasil+ditambahkan");
            exit();
        } else {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Redirect kembali dengan pesan error
        header("Location: ../program.php?error=Terjadi+kesalahan:+ " . urlencode($e->getMessage()));
        exit();
    } finally {
        // Tutup statement jika berhasil dibuka
        if (isset($stmt)) {
            $stmt->close();
        }
        // Tutup koneksi jika berhasil dibuka
        if (isset($conn)) {
            $conn->close();
        }
    }
} else {
    // Jika tidak diakses melalui POST, redirect kembali
    header("Location: ../program.php");
    exit();
}
?>