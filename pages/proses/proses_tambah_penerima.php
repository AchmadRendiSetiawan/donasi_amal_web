<?php
session_start();
include '../../config.php';

// Validasi input
$nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
$jenis_penerima = in_array($_POST['jenis_penerima'], ['Anak Yatim', 'Lansia', 'Fakir Miskin', 'Lainnya']) 
    ? $_POST['jenis_penerima'] : 'Lainnya';
$kontak = filter_input(INPUT_POST, 'kontak', FILTER_SANITIZE_STRING);
$alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);

if (!$nama || !$jenis_penerima || !$alamat) {
    die('Data tidak valid');
}

try {
    // Panggil stored procedure
    $stmt = $conn->prepare("CALL tambah_penerima(?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $jenis_penerima, $kontak, $alamat);
    $stmt->execute();

    header("Location: ../penerima.php?status=sukses");
    exit();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>