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
<html>
<head>
<title>Edit User</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

<style>
* { 
    margin:0; 
    padding:0; 
    box-sizing:border-box; 
    font-family:'Poppins',sans-serif; 
}

body {
    background:#f5f7ff;
    min-height:100vh;
    overflow-x:hidden;
}

/* KONTEN FULLSCREEN */
.content {
    position: relative;
    min-height: 100vh;
    width: 100%;
    padding: 40px;
    padding-left: 120px; 
    transition: all .3s ease;

    display: flex;
    justify-content: center;
    align-items: center;
}

/* Sidebar hover (user) */
.role-user .sidebar:hover ~ .content {
    padding-left: 230px;
}

/* Sidebar hover (admin) */
.role-admin .sidebar:hover ~ .content {
    padding-left: 260px;
}

/* Sidebar di atas konten */
.sidebar {
    position: fixed;
    top:0;
    left:0;
    z-index: 999;
}

/* CARD PROFILE */
.profile-card {
    width: 900px;
    background: rgba(255,255,255,0.9);
    padding: 35px;
    border-radius: 24px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.4);
    box-shadow: 0 14px 40px rgba(0,0,0,0.12);
    animation: slideUp .5s ease-in-out;
}

@keyframes slideUp {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}

/* TOP */
.profile-top {
    text-align:center;
    margin-bottom:20px;
}

.profile-card h1 {
    font-size:28px;
    font-weight:600;
    margin-bottom:10px;
    color:#333;
}

.subtitle {
    font-size:13px;
    color:#666;
    margin-bottom:25px;
}

/* FORM */
.profile-card form {
    display: block; /* Tetap vertikal */
}

/* INPUT */
.input-box { margin-bottom:18px; }

.input-box label {
    font-weight:600;
    font-size:14px;
    margin-bottom:5px;
    display:block;
    color:#333;
}

.input-box input, .input-box select {
    width:100%;
    padding:12px;
    border-radius:12px;
    border:1px solid #dcdcdc;
    font-size:14px;
    transition:.2s;
    background:#fafafa;
}

.input-box input:focus, .input-box select:focus {
    border-color:#4B8CFF;
    background:#fff;
    box-shadow:0 0 0 4px rgba(75,140,255,0.15);
    outline:none;
}

/* BUTTON */
button {
    width:100%;
    padding:14px;
    border:none;
    background:linear-gradient(135deg,#4B8CFF,#0066FF);
    color:#fff;
    border-radius:14px;
    font-weight:600;
    font-size:15px;
    cursor:pointer;
    box-shadow:0 8px 20px rgba(0,100,255,0.25);
    transition:.2s;
}

button:hover {
    transform:translateY(-2px);
    box-shadow:0 12px 25px rgba(0,100,255,0.3);
}

/* MESSAGE */
.message {
    font-weight: 800;
    text-align: center;
    width: 100%;
    padding: 10px 20px;
    margin-bottom: 20px;
    color: #00ff15;
    border: 1px solid #00ff15;
    background: #fff;
    border-radius: 20px;
}

/* BACK LINK */
.back {
    margin-top: 15px;
    display: inline-block;
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    text-align: center;
    width: 100%;
}

.back:hover {
    text-decoration: underline;
}

/* RESPONSIVE DESIGN */
/* Tablet dan Mobile */
@media (max-width: 1080px) {
    .content {
        padding: 10px;
        align-items: flex-start;
        padding-left: 50px;
        padding-top: 50px;
    }

    .profile-card {
        width: 480px;
        max-width: 450px;
        padding: 25px;
    }

    .profile-card h1 {
        font-size: 24px;
    }

    .subtitle {
        font-size: 12px;
    }

    .input-box input, .input-box select {
        padding: 10px;
        font-size: 13px;
    }

    button {
        padding: 12px;
        font-size: 14px;
    }
}

/* Mobile kecil */
@media (max-width: 390px) {
    .content {
        padding: 15px;
        padding-left: 50px;
        padding-top: 50px;
    }

    .profile-card {
        padding: 20px;
    }

    .profile-card h1 {
        font-size: 20px;
    }

    .input-box label {
        font-size: 13px;
    }

    .input-box input, .input-box select {
        padding: 8px;
        font-size: 12px;
    }

    button {
        padding: 10px;
        font-size: 13px;
    }
}
</style>
</head>
<body class="role-<?= $_SESSION['role'] ?>">
    <div class="content">
<div class="profile-card">
    
    <div class="profile-top">
        <h1>Edit User</h1>
        <p class="subtitle">Perbarui informasi akun user</p>
    </div>

    <?php if($message): ?>
      <div class="message"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-box">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($edit_user['nama']); ?>" required>
        </div>

        <div class="input-box">
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($edit_user['username']); ?>" required>
        </div>

        <div class="input-box">
            <label>Password (kosongkan jika tidak ingin mengubah)</label>
            <input type="password" name="password" placeholder="••••••">
        </div>

        <div class="input-box">
            <label>Role</label>
            <select name="role" required>
                <option value="admin" <?= $edit_user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="staff" <?= $edit_user['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                <option value="user" <?= $edit_user['role'] == 'user' ? 'selected' : '' ?>>User</option>
            </select>
        </div>

        <button type="submit"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
    </form>

    <a href="index.php" class="back"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
</div>
</body>
</html>
