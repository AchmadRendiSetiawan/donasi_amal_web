<?php
session_start();
include '../../../config.php';

$id_donatur = filter_input(INPUT_POST, 'id_donatur', FILTER_VALIDATE_INT);

if (!$id_donatur) {
    header("Location: ../dashboard_admin.php?error=Donatur tidak ditemukan");
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM donatur WHERE id_donatur = ?");
    $stmt->bind_param("i", $id_donatur);
    $stmt->execute();

    header("Location: ../dashboard_admin.php?success=Donatur berhasil dihapus");
    exit();
} catch (Exception $e) {
    error_log("Error menghapus donatur: " . $e->getMessage());
    header("Location: ../dashboard_admin.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>