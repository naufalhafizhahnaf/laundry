<?php
session_start();
require_once "./../connection.php";
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

// Cek ID user yang ingin diedit
if (!isset($_GET['id'])) {
    $_SESSION['info'] = ['status' => 'error', 'message' => 'ID user tidak ditemukan!'];
    header("Location: index.php");
    exit;
}

$edit_id = mysqli_real_escape_string($connection, $_GET['id']);
$result = mysqli_query($connection, "SELECT * FROM account WHERE id='$edit_id'");

if (mysqli_num_rows($result) == 0) {
    $_SESSION['info'] = ['status' => 'error', 'message' => 'User tidak ditemukan!'];
    header("Location: index.php");
    exit;
}

// Ambil data user yang akan diedit
$edit_user = mysqli_fetch_assoc($result);

$message = null; // variabel untuk menampung error jika POST gagal

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Cegah user biasa merubah role menjadi admin/staff
    if ($user['role'] !== 'admin' && $_POST['role'] !== $edit_user['role']) {
        $message = "Akses ditolak: Anda tidak memiliki izin untuk mengubah Role.";
    } else {
        $nama = mysqli_real_escape_string($connection, $_POST['nama']);
        $username = mysqli_real_escape_string($connection, $_POST['username']);
        $role = mysqli_real_escape_string($connection, $_POST['role']);
        $password = $_POST['password'];

        // Cek apakah username dipakai user lain
        $check = mysqli_query($connection, "SELECT id FROM account WHERE username='$username' AND id != '$edit_id'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Username $username sudah digunakan oleh user lain!";
        } else {
            $sql = "UPDATE account SET nama='$nama', username='$username', role='$role', updated_at=NOW()";

            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $sql .= ", password='$hashed'";
            }

            $sql .= " WHERE id='$edit_id'";

            $update = mysqli_query($connection, $sql);

            if ($update) {
                $_SESSION['info'] = ['status' => 'success', 'message' => "User $nama berhasil diperbarui!"];
                header("Location: edit.php?id=$edit_id");
                exit;
            } else {
                $message = "Terjadi kesalahan: " . mysqli_error($connection);
            }
        }
    }
}

// Ambil flash message
$flash = null;
if (isset($_SESSION['info'])) {
    $flash = $_SESSION['info'];
    unset($_SESSION['info']);
}

// Ambil ulang data terbaru
$edit_user = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM account WHERE id='$edit_id'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit User</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
    <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />

    <style>
        :root {
            --main-color:#007bff;
            --error-bg:#f8d7da;
            --error-color:#721c24;
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:"Poppins",sans-serif; }
        body { display:flex; height:100vh; background:#f5f7ff; }

        .main { flex:1; padding:30px 40px; }
        h1 { margin-bottom:20px; color:var(--main-color); font-weight:700; font-size:28px; }

        form {
            background:#fff; padding:30px; border-radius:15px;
            box-shadow:0 4px 12px rgba(0,0,0,0.05); max-width:500px;
        }

        label { display:block; margin-top:15px; font-weight:500; color:#333; }

        input, select {
            width:100%; padding:12px; margin-top:5px; border-radius:10px;
            border:1px solid #dce1f0; font-size:15px; transition:0.2s;
        }

        input:focus, select:focus {
            border-color:var(--main-color);
            box-shadow:0 0 0 3px rgba(0,123,255,0.1);
            outline:none;
        }

        button {
            margin-top:25px; padding:12px 25px; background:var(--main-color);
            color:#fff; border:none; border-radius:10px; cursor:pointer;
            font-size:16px; font-weight:600; transition:0.3s; display:flex;
            align-items:center; gap:8px;
        }

        button:hover { background:#0056b3; }

        .message {
            padding:12px; margin-bottom:20px; border-radius:8px; background:var(--error-bg);
            color:var(--error-color); border:1px solid #f5c6cb; font-weight:500;
        }

        .back {
            margin-top:20px; display:inline-block; color:var(--main-color);
            text-decoration:none; font-weight:500; padding:8px 15px; border-radius:8px;
            transition:0.2s;
        }

        .back:hover { background:#eaf4ff; }

        @media (max-width:768px) {
            .main { padding:20px; }
            form { padding:20px; }
            h1 { font-size:24px; }
        }
    </style>
</head>
<body>

<div class="main">
    <h1><i class="fa-solid fa-user-edit"></i> Edit User: <?= htmlspecialchars($edit_user['nama']); ?></h1>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($edit_user['nama']); ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($edit_user['username']); ?>" required>

        <label>Password (kosongkan jika tidak mengubah)</label>
        <input type="password" name="password" placeholder="••••••">

        <label>Role</label>
        <select name="role" required>
            <option value="admin" <?= $edit_user['role']=='admin'?'selected':'' ?>>Admin</option>
            <option value="staff" <?= $edit_user['role']=='staff'?'selected':'' ?>>Staff</option>
            <option value="user" <?= $edit_user['role']=='user'?'selected':'' ?>>User</option>
        </select>

        <button type="submit">
            <i class="fa-solid fa-save"></i> Simpan Perubahan
        </button>
    </form>

    <a href="index.php" class="back">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar User
    </a>
</div>

<script>
<?php if ($flash): ?>
    iziToast.<?= $flash['status'] === 'success' ? 'success' : 'error' ?>({
        title: '<?= $flash['status'] === 'success' ? 'Sukses' : 'Gagal' ?>',
        message: '<?= addslashes($flash['message']) ?>',
        position: 'topCenter'
    });
<?php endif; ?>
</script>

</body>
</html>
