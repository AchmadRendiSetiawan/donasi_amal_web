<?php
session_start();
include '../../../config.php';



$id_program = filter_input(INPUT_POST, 'id_program', FILTER_VALIDATE_INT);
$nama_program = filter_input(INPUT_POST, 'nama_program', FILTER_SANITIZE_STRING);
$deskripsi = filter_input(INPUT_POST, 'deskripsi', FILTER_SANITIZE_STRING);
$target_donasi = filter_input(INPUT_POST, 'target_donasi', FILTER_VALIDATE_FLOAT);
$status = in_array($_POST['status'], ['Aktif', 'Tidak Aktif', 'Selesai']) ? $_POST['status'] : 'Aktif';

if (!$id_program || !$nama_program || !$deskripsi || !$target_donasi) {
    header("Location: ../dashboard_admin.php?error=Data tidak valid");
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE program_amal SET nama_program = ?, deskripsi = ?, target_donasi = ?, status = ? WHERE id_program = ?");
    $stmt->bind_param("ssdii", $nama_program, $deskripsi, $target_donasi, $status, $id_program);
    $stmt->execute();

    $stmt_update = $conn->prepare("CALL update_status_program(?)");
    $stmt_update->bind_param("i", $id_program);
    $stmt_update->execute();

    header("Location: ../dashboard_admin.php?success=Program berhasil diperbarui");
    exit();
} catch (Exception $e) {
    error_log("Error memperbarui program: " . $e->getMessage());
    header("Location: ../dashboard_admin.php?error=Gagal memperbarui program");
    exit();
}
?>