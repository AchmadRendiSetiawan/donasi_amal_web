<?php
session_start();
include '../../config.php'; // Pastikan path ke config.php benar, biasanya dua level di atas.

// Cek jika request bukan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../donasi.php?status=error&pesan=Metode+request+tidak+valid");
    exit();
}

// 1. Verifikasi CSRF Token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    // Hapus token setelah digunakan untuk mencegah replay attacks
    unset($_SESSION['csrf_token']);
    header("Location: ../donasi.php?status=csrf_invalid");
    exit();
}

// Hapus token CSRF setelah verifikasi berhasil untuk sekali pakai
unset($_SESSION['csrf_token']);

// 2. Sanitasi dan Validasi Input
$id_donatur = filter_input(INPUT_POST, 'id_donatur', FILTER_VALIDATE_INT);
$id_program = filter_input(INPUT_POST, 'id_program', FILTER_VALIDATE_INT);
// Menggunakan 'jumlah_donasi' sesuai dengan nama input di form dan kolom di DB
$jumlah_donasi = filter_input(INPUT_POST, 'jumlah_donasi', FILTER_VALIDATE_FLOAT);

// Validasi tambahan
if (!$id_donatur || $id_donatur <= 0) {
    header("Location: ../donasi.php?status=input_tidak_valid&pesan=ID+Donatur+tidak+valid");
    exit();
}
if (!$id_program || $id_program <= 0) {
    header("Location: ../donasi.php?status=input_tidak_valid&pesan=ID+Program+tidak+valid");
    exit();
}
if (!$jumlah_donasi || $jumlah_donasi < 1000) { // Minimal donasi Rp 1.000
    header("Location: ../donasi.php?status=input_tidak_valid&pesan=Jumlah+donasi+minimal+Rp+1.000");
    exit();
}

// 3. Panggil Stored Procedure
try {
    // Pastikan koneksi database ($conn) sudah terbuka dari config.php
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Koneksi database tidak tersedia.");
    }

    // Panggil stored procedure dengan 3 parameter
    $stmt = $conn->prepare("CALL tambah_donasi(?, ?, ?)");
    if ($stmt === false) {
        throw new Exception("Gagal menyiapkan statement: " . $conn->error);
    }

    // "iid" => integer, integer, decimal (converted to double in bind_param)
    $stmt->bind_param("iid", $id_donatur, $id_program, $jumlah_donasi);

    // Eksekusi stored procedure
    $stmt->execute();

    // Cek apakah ada error dari stored procedure (misal: SIGNAL SQLSTATE)
    if ($stmt->errno) {
        // Tangani error khusus dari stored procedure jika ada
        if ($stmt->errno == 1644) { // MySQL error code for SIGNAL SQLSTATE
            header("Location: ../donasi.php?status=program_tidak_aktif");
        } else {
            header("Location: ../donasi.php?status=error&pesan=" . urlencode($stmt->error));
        }
        $stmt->close();
        $conn->close();
        exit();
    }

    // Jika berhasil
    header("Location: ../donasi.php?status=sukses");
    exit();

} catch (Exception $e) {
    // Tangani error umum atau error koneksi/prepare statement
    error_log("Error di proses_donasi.php: " . $e->getMessage()); // Log error untuk debugging
    header("Location: ../donasi.php?status=error&pesan=" . urlencode($e->getMessage()));
    exit();
} finally {
    // Pastikan statement dan koneksi ditutup
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
?>