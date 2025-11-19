<?php
require_once "../connection.php";
session_start();
include "../sidebar.php";

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data user
$id = $_SESSION['user_id'];
$query = mysqli_query($connection, "SELECT * FROM account WHERE id='$id'");
$user = mysqli_fetch_assoc($query);

// Proses form submit
// Proses form submit
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($connection, $_POST['nama']);
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
$no_hp = mysqli_real_escape_string($connection, $_POST['no_hp'] ?? '');


    // Cek username sudah ada atau belum
    $check = mysqli_query($connection, "SELECT * FROM account WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Username sudah digunakan!";
    } else {
        // Insert dengan no_hp dan photo NULL
        $insert = mysqli_query($connection, "
            INSERT INTO account (nama, username, password, role, no_hp, photo, created_at)
            VALUES ('$nama', '$username', '$password', '$role', '$no_hp', NULL, NOW())
        ");

        if ($insert) {
            $message = "User berhasil ditambahkan!";
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
  <title>User Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <style>
    /* Styles tetap sama seperti sebelumnya */
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


  </style>
</head>
<body>

  <div class="main">
    <h1>Tambah User</h1>

    <?php if($message): ?>
      <div class="message"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Nama Lengkap</label>
      <input type="text" name="nama" required>

      <label>Username</label>
      <input type="text" name="username" required>

      <label>Password</label>
      <input type="password" name="password" required>
      <label>No HP</label>
<input type="text" name="no_hp">

      <label>Role</label>
      <select name="role" required>
        <option value="admin">Admin</option>
        <option value="staff">Staff</option>
        <option value="user">User</option>
      </select>

      <button type="submit"><i class="fa-solid fa-plus"></i> Tambah User</button>
    </form>
  </div>
</body>
</html>
