<?php
require '../../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || strlen($password) < 6) {
        header("Location: ../../register.php?error=Data tidak valid");
        exit();
    }

    $check = $conn->query("SELECT * FROM admins WHERE username = '$username'");
    if ($check->num_rows > 0) {
        header("Location: ../../register.php?error=Username sudah terdaftar");
        exit();
    }

    $hashed = md5($password);
    $insert = $conn->query("INSERT INTO admins (username, password) VALUES ('$username', '$hashed')");

    if ($insert) {
        header("Location: ../../login.php?success=Registrasi berhasil");
    } else {
        header("Location: ../../register.php?error=Gagal menyimpan");
    }
}
?>
