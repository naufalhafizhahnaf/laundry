<?php
require_once "../connection.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {

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
