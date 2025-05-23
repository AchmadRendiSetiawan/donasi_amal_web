<?php
session_start();
include '../config.php';

try {
    // Ambil semua program aktif
    $result = $conn->query("SELECT id_program FROM program_amal WHERE status = 'Aktif'");
    while ($row = $result->fetch_assoc()) {
        $stmt = $conn->prepare("CALL update_status_program(?)");
        $stmt->bind_param("i", $row['id_program']);
        $stmt->execute();
    }

    echo "Status program berhasil diperbarui";
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>