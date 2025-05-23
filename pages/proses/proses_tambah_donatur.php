<?php
session_start(); // Pastikan ini ada di paling atas jika Anda menggunakan sesi
include '../../config.php'; // Verifikasi path ini

// Pengecekan apakah form disubmit dengan method POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $no_hp = filter_input(INPUT_POST, 'no_hp', FILTER_SANITIZE_STRING); // Sanitasi string untuk nomor HP
    $alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);

    // Validasi input
    if (empty($nama) || $email === false || empty($no_hp) || empty($alamat)) {
        // Redirect kembali dengan pesan error
        header("Location: ../donatur.php?error=Data+tidak+valid.+Pastikan+semua+kolom+terisi+dengan+benar+dan+email+valid.");
        exit();
    }

    try {
        // Persiapkan query prepared statement
        $stmt = $conn->prepare("INSERT INTO donatur (nama, email, no_hp, alamat) VALUES (?, ?, ?, ?)");

        // Periksa apakah prepare berhasil
        if ($stmt === false) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param("ssss", $nama, $email, $no_hp, $alamat);

        // Periksa apakah execute berhasil
        if ($stmt->execute()) {
            header("Location: ../donatur.php?success=Donatur+berhasil+ditambahkan");
            exit();
        } else {
            throw new Exception("Execute statement failed: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Redirect kembali dengan pesan error
        header("Location: ../donatur.php?error=Terjadi+kesalahan:+ " . urlencode($e->getMessage()));
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
    header("Location: ../donatur.php");
    exit();
}
?>