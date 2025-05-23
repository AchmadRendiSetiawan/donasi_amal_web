<?php
session_start();
include '../../../config.php'; // Sesuaikan path jika berbeda

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_program = $_POST['id_program'];

    if (empty($id_program)) {
        $_SESSION['alert_message'] = "ID Program tidak valid.";
        $_SESSION['alert_type'] = "danger";
        header("Location: ../dashboard_admin.php");
        exit();
    }

    // Start a transaction for atomicity
    $conn->begin_transaction();

    try {
        // First, delete related records from 'riwayat_donasi'
        $stmt_riwayat_donasi = $conn->prepare("DELETE FROM riwayat_donasi WHERE id_program = ?");
        $stmt_riwayat_donasi->bind_param("i", $id_program);
        $stmt_riwayat_donasi->execute();
        $stmt_riwayat_donasi->close();

        // Next, delete related records from 'penyaluran_donasi'
        $stmt_penyaluran_donasi = $conn->prepare("DELETE FROM penyaluran_donasi WHERE id_program = ?");
        $stmt_penyaluran_donasi->bind_param("i", $id_program);
        $stmt_penyaluran_donasi->execute();
        $stmt_penyaluran_donasi->close();

        // Finally, delete the program itself
        $stmt_program = $conn->prepare("DELETE FROM program_amal WHERE id_program = ?");
        $stmt_program->bind_param("i", $id_program);

        if ($stmt_program->execute()) {
            $conn->commit(); // Commit transaction if all deletions are successful
            $_SESSION['alert_message'] = "Program amal dan data terkait berhasil dihapus!";
            $_SESSION['alert_type'] = "success";
        } else {
            $conn->rollback(); // Rollback if program deletion fails
            $_SESSION['alert_message'] = "Error saat menghapus program: " . $stmt_program->error;
            $_SESSION['alert_type'] = "danger";
        }
        $stmt_program->close();

    } catch (mysqli_sql_exception $e) {
        $conn->rollback(); // Rollback if any exception occurs
        $_SESSION['alert_message'] = "Terjadi kesalahan database: " . $e->getMessage();
        $_SESSION['alert_type'] = "danger";
    }

    $conn->close();

    header("Location: ../dashboard_admin.php");
    exit();
} else {
    header("Location: ../dashboard_admin.php"); // Redirect jika diakses langsung tanpa POST
    exit();
}
?>