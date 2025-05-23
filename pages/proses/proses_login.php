<?php
session_start();
require '../../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Ganti ke password_hash() kalau ingin lebih aman

    $query = "SELECT * FROM admins WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows === 1) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;

        header("Location: ../admin/dashboard_admin.php");
        exit();
    } else {
        header("Location: ../../login.php?error=Username atau password salah");
        exit();
    }
} else {
    header("Location: ../../login.php");
    exit();
}
