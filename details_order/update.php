<?php
require_once "../connection.php";
session_start();
include "../sidebar.php";

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data user yang login
$id = $_SESSION['user_id'];
$query = mysqli_query($connection, "SELECT * FROM account WHERE id='$id'");
$user = mysqli_fetch_assoc($query);

// Cek apakah ada ID user yang ingin diedit
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$edit_id = $_GET['id'];
$result = mysqli_query($connection, "SELECT * FROM account WHERE id='$edit_id'");
if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('User tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}
$edit_user = mysqli_fetch_assoc($result);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($connection, $_POST['nama']);
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Cek apakah username sudah digunakan oleh user lain
    $check = mysqli_query($connection, "SELECT * FROM account WHERE username='$username' AND id != '$edit_id'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Username sudah digunakan oleh user lain!";
    } else {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $update = mysqli_query($connection, "UPDATE account SET nama='$nama', username='$username', password='$hashed', role='$role' WHERE id='$edit_id'");
        } else {
            $update = mysqli_query($connection, "UPDATE account SET nama='$nama', username='$username', role='$role' WHERE id='$edit_id'");
        }

        if ($update) {
            $message = "User berhasil diperbarui!";
            // refresh data
            $edit_user = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM account WHERE id='$edit_id'"));
        } else {
            $message = "Terjadi kesalahan: " . mysqli_error($connection);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit User</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }
    body { display:flex; height:100vh; font-family:"Poppins", sans-serif; background:#f5f7ff; }
    .main {
      flex: 1;
      padding: 30px 40px;
    }
    h1 {
      margin-bottom: 20px;
      color: #222;
    }
    form {
      background: #fff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      max-width: 500px;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: 500;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    button {
      margin-top: 20px;
      padding: 10px 20px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background: #0056b3;
    }
    .message {
      font-weight: 800;
      text-align: center;
      width: 28%;
      padding: 10px 20px;
      margin-bottom: 10px;
      color: #00ff15ff;
      border: 1px solid #00ff15ff;
      background: #ffffffff;
      border-radius: 20px;
    }
    .back {
      margin-top: 15px;
      display: inline-block;
      color: #007bff;
      text-decoration: none;
      font-weight: 500;
    }
    .back:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="main">
    <h1>Edit User</h1>

    <?php if($message): ?>
      <div class="message"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Nama Lengkap</label>
      <input type="text" name="nama" value="<?= htmlspecialchars($edit_user['nama']); ?>" required>

      <label>Username</label>
      <input type="text" name="username" value="<?= htmlspecialchars($edit_user['username']); ?>" required>

      <label>Password (kosongkan jika tidak ingin mengubah)</label>
      <input type="password" name="password" placeholder="••••••">

      <label>Role</label>
      <select name="role" required>
        <option value="admin" <?= $edit_user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="staff" <?= $edit_user['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
        <option value="user" <?= $edit_user['role'] == 'user' ? 'selected' : '' ?>>User</option>
      </select>

      <button type="submit"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
    </form>

    <a href="index.php" class="back"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>
</body>
</html>
