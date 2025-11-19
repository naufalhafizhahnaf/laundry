<?php
// Konfigurasi koneksi database
$host     = "localhost";  // atau 127.0.0.1
$user     = "root";       // default user Laragon
$pass     = "";           // default password Laragon biasanya kosong
$dbname   = "laundryDB"; // ganti dengan nama database kamu

// Membuat koneksi
$connection = mysqli_connect($host, $user, $pass, $dbname);

// Mengecek koneksi
if (!$connection) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
