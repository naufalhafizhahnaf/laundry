<?php
require_once "../connection.php";
header('Content-Type: application/json');

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request"
    ]);
    exit;
}

$id = $_POST['id'];
$status = $_POST['status'];

$update = mysqli_query($connection, "UPDATE orders SET status='$status' WHERE id='$id'");

if ($update) {
    echo json_encode([
        "status" => "success",
        "message" => "Status berhasil diperbarui!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal memperbarui status"
    ]);
}
