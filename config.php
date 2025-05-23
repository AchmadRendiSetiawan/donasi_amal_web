<?php
$host = "localhost";
$user = "root"; // Sesuaikan
$password = ""; // Sesuaikan
$database = "donasi_amal";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>