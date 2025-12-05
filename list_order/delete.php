<?php
require_once "../connection.php";
session_start();

// Cek login menggunakan session user_id
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil ID user dari query string
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Hapus berdasarkan tabel account bukan admin (opsional tergantung struktur DB)
    $delete = mysqli_query($connection, "DELETE FROM admin WHERE id=$id");

    if ($delete) {
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
