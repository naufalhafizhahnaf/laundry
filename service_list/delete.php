<?php
require_once "../../connection.php";
session_start();

// Cek apakah user login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data user login untuk memeriksa role
$current_user_id = $_SESSION['user_id'];
$user_q = mysqli_query($connection, "SELECT role FROM account WHERE id='$current_user_id'");
$current_user = mysqli_fetch_assoc($user_q);

// Batasi akses hanya untuk Admin atau Staff
if (!isset($current_user['role']) || !in_array($current_user['role'], ['admin', 'staff'])) {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Akses ditolak. Anda tidak memiliki izin untuk menghapus data ini.'
    ];
    header("Location: index.php");
    exit;
}

// Cek apakah ID dikirim lewat URL
if (isset($_GET['id'])) {
    // Sanitasi input
    $id_to_delete = mysqli_real_escape_string($connection, $_GET['id']);

    // Cegah user menghapus dirinya sendiri
    if ($id_to_delete == $current_user_id) {
        $_SESSION['info'] = [
            'status' => 'error',
            'message' => 'Anda tidak dapat menghapus akun yang sedang Anda gunakan!'
        ];
        header("Location: index.php");
        exit;
    }

    // Eksekusi Delete dari tabel 'account'
    $delete = mysqli_query($connection, "DELETE FROM account WHERE id='$id_to_delete'");

    if ($delete) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Pengguna berhasil dihapus!'
        ];
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['info'] = [
            'status' => 'error',
            'message' => 'Gagal menghapus pengguna: ' . mysqli_error($connection)
        ];
        header("Location: index.php");
        exit;
    }

} else {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'ID tidak valid atau tidak ditemukan!'
    ];
    header("Location: index.php");
    exit;
}
?>
