<?php
require_once "../../connection.php";

// Cek apakah admin login
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Ambil ID user dari query string
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $delete = mysqli_query($connection, "DELETE FROM admin WHERE id=$id");

    if ($delete) {
        // Bisa redirect ke daftar user dengan pesan sukses
        header("Location: index.php?message=User+berhasil+dihapus");
        exit;
    } else {
        header("Location: index.php?message=Gagal+menghapus+user");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
