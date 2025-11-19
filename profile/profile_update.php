<?php
require "../connection.php";
session_start();

$id = $_SESSION['user_id'];

$nama = $_POST['nama'];
$no_hp = $_POST['no_hp'];
$alamat = $_POST['alamat'];
$username = $_POST['username'];
$password = $_POST['password'];

// Update foto
$photoName = null;
if (!empty($_FILES['photo']['name'])) {
    $photoName = time() . "_" . $_FILES['photo']['name'];
    $target = "../uploads/profile/" . $photoName;
    move_uploaded_file($_FILES['photo']['tmp_name'], $target);
}

// Query dynamic
$sql = "UPDATE account SET 
            nama='$nama',
            no_hp='$no_hp',
            alamat='$alamat',
            username='$username'";

if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $sql .= ", password='$hashed'";
}

if ($photoName !== null) {
    $sql .= ", photo='$photoName'";
}

$sql .= " WHERE id='$id'";

if (mysqli_query($connection, $sql)) {
    header("Location: profile.php?success=1");
    exit();
} else {
    echo "Error: " . mysqli_error($connection);
}
?>
