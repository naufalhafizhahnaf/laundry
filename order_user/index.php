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

// Ambil semua user
$userQuery = mysqli_query($connection, "SELECT * FROM account ORDER BY created_at DESC");
$users = mysqli_fetch_all($userQuery, MYSQLI_ASSOC);
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
    .main { flex:1; padding:30px 40px; overflow-y:auto; }
    .top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.filter-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

.filter-container select {
  padding: 5px 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

    .top-bar h1 { font-size:24px; font-weight:600; color:#222; }
    .user-info { display:flex; align-items:center; gap:15px; }
    .hi-text { font-weight:500; color:#444; font-size:18px; }
    .add-user-btn { background:#28a745; color:white; border:none; padding:8px 16px; border-radius:8px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px; transition:0.3s; }
    .add-user-btn:hover { background:#218838; }
    .content { background:white; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.05); padding:20px; overflow-x:auto; }
    table { width:100%; border-collapse:collapse; margin-top:15px; min-width:100px; }
    th, td { padding:20px; text-align:left; }
    th { background:#007bff; color:white; position:sticky; top:0; }
    tr:nth-child(even){background:#f8f9ff;}
    tr:hover {background:#eef4ff;}
    .action-btn { border:none; border-radius:8px; padding:10px; cursor:pointer; font-size:14px; transition:0.3s; }
    .edit { background:#ffc107; color:white; }
    .delete { background:#dc3545; color:white; }
    .edit:hover { background:#e0a800; }
    .delete:hover { background:#c82333; }
    /* Popup rounded & shadow */
.swal-popup {
  border-radius: 15px !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  padding: 25px !important;
}

/* Tombol delete / confirm */
.swal-confirm-btn {
  background: #dc3545 !important; /* sama dengan tombol delete */
  color: white !important;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  border-radius: 12px !important;
  padding: 8px 20px !important;
  margin: 0 5px !important;
  cursor: pointer;
  transition: 0.3s;
}

.swal-confirm-btn:hover {
  background: #c82333 !important;
}

/* Tombol cancel */
.swal-cancel-btn {
  background: #6c757d !important; /* abu-abu lembut */
  color: white !important;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  border-radius: 12px !important;
  padding: 8px 20px !important;
  margin: 0 5px !important;
  cursor: pointer;
  transition: 0.3s;
}

.swal-cancel-btn:hover {
  background: #5a6268 !important;
}

  </style>
</head>
<body>

<div class="main">
  <div class="top-bar">
    <h1>User Management</h1>
    <div class="user-info">
      <span class="hi-text">Hi, <?= htmlspecialchars($user['nama']); ?></span>

    </div>
  </div>

  <div class="content">
  <div class="top-bar">
    <h3>Daftar Pengguna</h3>

    <div class="filter-container">
      <label for="roleFilter">Filter Role:</label>
      <select id="roleFilter" onchange="filterRole(this.value)">
        <option value="">Semua</option>
        <option value="customer" <?= (isset($_GET['role']) && $_GET['role'] == 'user') ? 'selected' : '' ?>>Customer</option>
        <option value="admin" <?= (isset($_GET['role']) && $_GET['role'] == 'admin') ? 'selected' : '' ?>>Karyawan</option>
      </select>
    </div>

    <button class="add-user-btn" onclick="window.location.href='insert.php'">
      <i class="fa-solid fa-plus"></i> Add User
    </button>
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Role</th>
        <th>Tanggal Dibuat</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $filter = $_GET['role'] ?? '';
      $no = 1;

      // Filter data user berdasarkan role
      $filteredUsers = array_filter($users, function($u) use ($filter) {
        return $filter ? strtolower($u['role']) == strtolower($filter) : true;
      });

      if (!empty($filteredUsers)):
        foreach ($filteredUsers as $row): ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama']); ?></td>
            <td><?= htmlspecialchars($row['role']); ?></td>
            <td><?= htmlspecialchars($row['created_at']); ?></td>
            <td>
              <button class="action-btn edit" data-id="<?= $row['id']; ?>"><i class="fa-solid fa-pen"></i></button>
              <button class="action-btn delete" data-id="<?= $row['id']; ?>"><i class="fa-solid fa-trash"></i></button>
            </td>
          </tr>
      <?php endforeach; else: ?>
        <tr>
          <td colspan="5" style="text-align:center; color:#888;">Belum ada data user.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
</div>

<script>
document.querySelectorAll('.delete').forEach(btn => {
  btn.addEventListener('click', function() {
    const userId = this.dataset.id;
    Swal.fire({
      title: '<span style="font-family:Poppins; font-weight:600;">Konfirmasi Hapus</span>',
      html: '<span style="font-family:Poppins;">Apakah Anda yakin ingin menghapus user ini? <br> Tindakan ini tidak bisa dibatalkan.</span>',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
      background: '#ffffff',   // warna modal sesuai style content
      color: '#333',           // warna text
      iconColor: '#dc3545',    // warna icon warning
      customClass: {
        confirmButton: 'swal-confirm-btn',
        cancelButton: 'swal-cancel-btn',
        popup: 'swal-popup'
      },
      buttonsStyling: false    // supaya bisa custom css sendiri
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'delete.php?id=' + userId;
      }
    });
  });
});


</script>
<script>
  document.querySelectorAll('.action-btn.edit').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.dataset.id;
      window.location.href = 'update.php?id=' + id;
    });
  });
</script>
<script>
function filterRole(role) {
  const url = new URL(window.location.href);
  if (role) {
    url.searchParams.set('role', role);
  } else {
    url.searchParams.delete('role');
  }
  window.location.href = url.toString();
}
</script>

</body>
</html>
