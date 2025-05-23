<?php
session_start();
include '../../../config.php';



$id_donatur = filter_input(INPUT_POST, 'id_donatur', FILTER_VALIDATE_INT);
$nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$no_hp = filter_input(INPUT_POST, 'no_hp', FILTER_SANITIZE_STRING);
$alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);

if (!$id_donatur || !$nama || !$email || !$no_hp || !$alamat) {
    header("Location: ../dashboard_admin.php?error=Data tidak valid");
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE donatur SET nama = ?, email = ?, no_hp = ?, alamat = ? WHERE id_donatur = ?");
    $stmt->bind_param("ssssi", $nama, $email, $no_hp, $alamat, $id_donatur);
    $stmt->execute();

    header("Location: ../dashboard_admin.php?success=Donatur berhasil diperbarui");
    exit();
} catch (Exception $e) {
    error_log("Error memperbarui donatur: " . $e->getMessage());
    header("Location: ../dashboard_admin.php?error=Gagal memperbarui donatur");
    exit();
}
?>