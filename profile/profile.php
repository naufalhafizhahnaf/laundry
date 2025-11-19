<?php
include "../sidebar.php"; 
require "../connection.php";
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['user_id'];
$query = mysqli_query($connection, "SELECT * FROM account WHERE id='$id'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Profile Saya</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

<style>
    /* Styles tetap sama seperti sebelumnya */
        * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }
    body { display:flex; min-height:100vh; font-family:"Poppins", sans-serif; background:#f5f7ff; }
.main { 
    flex: 1; 
    padding: 30px 40px; 
    overflow: hidden; /* 🚫 Matikan scroll */
}

/* KONTEN FULLSCREEN */
.content {
    position: relative;
    width: 100%;
    min-height: calc(100vh - 40px);
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden; /* 🚫 Hentikan scroll */
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
    margin-bottom:10px;
}

.profile-card img {
    width:135px;
    height:135px;
    border-radius:50%;
    object-fit:cover;
    box-shadow:0 6px 20px rgba(0,0,0,0.2);
    border:4px solid #fff;
    margin-bottom:10px;
}

.profile-card h2 {
    font-size:24px;
    font-weight:600;
    margin-bottom:5px;
}

.subtitle {
    font-size:13px;
    color:#666;
    margin-bottom:25px;
}

/* FORM */
.profile-card form {
    display: grid;
    grid-template-columns: 1fr 1fr; /* 2 kolom pada desktop */
    gap: 18px;
}

/* INPUT */
.input-box { margin-bottom:0; } /* Hapus margin bottom karena gap di grid */

.input-box label {
    font-weight:600;
    font-size:14px;
    margin-bottom:5px;
    display:block;
    color:#333;
}

.input-box input {
    width:100%;
    padding:12px;
    border-radius:12px;
    border:1px solid #dcdcdc;
    font-size:14px;
    transition:.2s;
    background:#fafafa;
}

.input-box input:focus {
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
    grid-column: span 2; /* Button span 2 kolom */
}

button:hover {
    transform:translateY(-2px);
    box-shadow:0 12px 25px rgba(0,100,255,0.3);
}
.input-file-modern {
    padding: 10px;
    border: 2px dashed #4e73df;
    border-radius: 12px;
    width: 100%;
    cursor: pointer;
    transition: 0.2s;
    background: #f8f9fc;
    font-family: Arial;
}

.input-file-modern:hover {
    border-color: #3653b8;
    background: #eef1ff;
}

.input-file-modern::-webkit-file-upload-button {
    background: #4e73df;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    transition: 0.2s;
}

.input-file-modern::-webkit-file-upload-button:hover {
    background: #3653b8;
}

/* Foto profil ketika sudah upload */
.profile-foto{
    width:135px;
    height:135px;
    border-radius:50%;
    object-fit:cover;
    box-shadow:0 6px 20px rgba(0,0,0,0.2);
    border:4px solid #fff;
    margin-bottom:10px;
}

/* Default icon (tanpa upload foto) */
.photo-icon-default2 {
    width:135px;
    height:135px;
    background:#fff;
    border-radius:50%;
    border:4px solid #fff;
    box-shadow:0 6px 20px rgba(0,0,0,0.2);
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0 auto 10px auto;
}

.photo-icon-default2 i {
    font-size:110px;  /* PAS banget supaya tidak keluar dari lingkaran */
    color:#cccccc;
}

/* RESPONSIVE DESIGN */
/* Tablet dan Mobile */
@media (max-width: 1080px) {
    .content {
        padding: 10px;
        align-items: flex-start; /* Ubah agar card mulai dari atas jika perlu */
        padding-left: 50px;
        padding-top: 50px;
    }

    .profile-card {
        width: 480px; /* Full width pada mobile */
        max-width: 450px; /* Tetap batas maksimal */
        padding: 25px; /* Kurangi padding */
    }

    .profile-card form {

        display: block; /* Kembali ke vertikal pada mobile */
        gap: 0;
    }

    .input-box {
        margin-bottom: 18px; /* Kembalikan margin pada mobile */
    }

    .profile-card img {
        width: 100px; /* Lebih kecil pada mobile */
        height: 100px;
    }

    .profile-card h2 {
        font-size: 20px; /* Lebih kecil */
    }

    .subtitle {
        font-size: 12px;
    }

    .input-box input {
        padding: 10px; /* Kurangi padding input */
        font-size: 13px;
    }

    button {
        padding: 12px; /* Kurangi padding button */
        font-size: 14px;
        grid-column: auto; /* Reset grid-column */
    }
.input-file-modern {
    padding: 10px;
    border: 2px dashed #4e73df;
    border-radius: 12px;
    width: 100%;
    cursor: pointer;
    transition: 0.2s;
    background: #f8f9fc;
    font-family: Arial;
}

.input-file-modern:hover {
    border-color: #3653b8;
    background: #eef1ff;
}

.input-file-modern::-webkit-file-upload-button {
    background: #4e73df;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    transition: 0.2s;
}

.input-file-modern::-webkit-file-upload-button:hover {
    background: #3653b8;
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

    .profile-card img {
        width: 80px;
        height: 80px;
    }

    .profile-card h2 {
        font-size: 18px;
    }

    .input-box label {
        font-size: 13px;
    }

    .input-box input {
        padding: 8px;
        font-size: 12px;
    }

    button {
        padding: 10px;
        font-size: 13px;
    }
.input-file-modern {
    padding: 10px;
    border: 2px dashed #4e73df;
    border-radius: 12px;
    width: 100%;
    cursor: pointer;
    transition: 0.2s;
    background: #f8f9fc;
    font-family: Arial;
}

.input-file-modern:hover {
    border-color: #3653b8;
    background: #eef1ff;
}

.input-file-modern::-webkit-file-upload-button {
    background: #4e73df;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    transition: 0.2s;
}

.input-file-modern::-webkit-file-upload-button:hover {
    background: #3653b8;
}
}

</style>
</head>
<body class="role-<?= $_SESSION['role'] ?>">
<div class="main">
    <div class="content">
<div class="profile-card">
    
    <div class="profile-top">
        <a title="photo">
    <?php if (!empty($user['photo']) && $user['photo'] !== "default"): ?>
        <img src="/uploads/profile/<?= $user['photo']; ?>" class="profile-foto">
    <?php else: ?>
        <div class="photo-icon-default2">
            <i class="fa-solid fa-circle-user"></i>
        </div>
    <?php endif; ?>
</a>
        <h2><?php echo $user['username']; ?></h2>
        <p class="subtitle">Kelola profil & informasi akun Anda</p>
    </div>

    <form action="profile_update.php" method="POST" enctype="multipart/form-data">

        <div class="input-box">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?php echo $user['nama']; ?>">
        </div>

        <div class="input-box">
            <label>No Telpon</label>
            <input type="text" name="no_hp" value="<?php echo $user['no_hp']; ?>">
        </div>

        <div class="input-box">
            <label>Alamat</label>
            <input type="text" name="alamat" value="<?php echo $user['alamat']; ?>">
        </div>

        <div class="input-box">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo $user['username']; ?>">
        </div>

        <div class="input-box">
            <label>Password Baru (opsional)</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak diganti">
        </div>

        <div class="input-box">
            <label>Foto Profil</label>
            <input type="file" name="photo"class="input-file-modern">
        </div>

        <button type="submit"><i class="fa-solid fa-save"></i> &nbsp; Update Profile</button>
    </form>
</div>
</div>
</div>
</body>
</html>
