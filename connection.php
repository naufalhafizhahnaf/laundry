<?php
$host = "localhost"; // Atau 127.0.0.1
$user = "root";      // Username database XAMPP default
$password = "";      // Password database XAMPP default
$database = "laundrydb"; // ❗ Pastikan ini benar

$connection = mysqli_connect($host, $user, $password, $database);

if (!$connection) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>