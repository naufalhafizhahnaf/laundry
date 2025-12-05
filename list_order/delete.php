<?php
require_once "../connection.php";
session_start();

<<<<<<< HEAD
=======
// Cek login menggunakan session user_id
>>>>>>> 3a4f77b45a997afa8c5bc6b42cfe0ff597efbc1e
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
<<<<<<< HEAD

    $id = intval($_GET['id']);
    $account_id = $_SESSION['user_id'];

    // ✅ FIX: pakai account_id (bukan user_id)
    $query = "DELETE FROM orders WHERE id = ? AND account_id = ?";
    $stmt = mysqli_prepare($connection, $query);

    mysqli_stmt_bind_param($stmt, "ii", $id, $account_id);
    $execute = mysqli_stmt_execute($stmt);

    if ($execute) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Order berhasil dihapus'
        ];
=======
    $id = intval($_GET['id']);

    // Hapus berdasarkan tabel account bukan admin (opsional tergantung struktur DB)
    $delete = mysqli_query($connection, "DELETE FROM admin WHERE id=$id");

    if ($delete) {
        header("Location: index.php?message=User+berhasil+dihapus");
        exit;
>>>>>>> 3a4f77b45a997afa8c5bc6b42cfe0ff597efbc1e
    } else {
        $_SESSION['info'] = [
            'status' => 'error',
            'message' => 'Gagal menghapus order'
        ];
    }

    mysqli_stmt_close($stmt);
    header("Location: index.php");
    exit;

} else {
    header("Location: index.php");
    exit;
}
