<?php
// Mulai session kalau belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi DB
require_once "../connection.php";

// Ambil user_id dari session
$id = $_SESSION['user_id'] ?? null;

// Ambil role
$role = $_SESSION['role'] ?? 'user';

// Ambil nama file aktif
$current = basename($_SERVER['PHP_SELF']);

// Ambil data user
$user = null;
if ($id) {
    $query = mysqli_query($connection, "SELECT * FROM account WHERE id='$id' LIMIT 1");
    $user = mysqli_fetch_assoc($query);
}
?>


<body class="role-<?= htmlspecialchars($role) ?>">
  <div class="sidebar">
    <!-- Logo -->
    <div class="logo">
      <a href="../dashboard/index.php"
        class="menu-item <?= $current == 'index.php' && strpos($_SERVER['PHP_SELF'], 'tiket_umrah') !== false ? 'active' : '' ?>"
        title="Tiket Umrah">
        <i class="fa-solid fa-circle-nodes"></i>
      </a>
    </div>

    <!-- Menu utama -->
    <div class="menu">

      <?php if ($role === 'admin') : ?>
        <a href="../Statistik/index.php"
          class="menu-item <?= $current == 'index.php' && strpos($_SERVER['PHP_SELF'], 
          'statistic') !== false ? 'active' : '' ?>" title="Statistic">
          <i class="fa-solid fa-chart-simple"></i>
          <span>Statistic</span>
        </a>
        <a href="../users/index.php"
          class="menu-item <?= $current == 'index.php' && strpos($_SERVER['PHP_SELF'],
          'users') !== false ? 'active' : '' ?>" title="User Management">
          <i class="fa-solid fa-users"></i>
          <span>User Management</span>
        </a>

        <a href="../list_order/index.php"
          class="menu-item <?= $current == 'index.php' && strpos($_SERVER['PHP_SELF'], 
          'List_order') !== false ? 'active' : '' ?>" title="List Order">
          <i class="fa-solid fa-layer-group"></i>
          <span>List Order</span>
        </a>
      <?php endif; ?>
      <?php if ($role === 'user') : ?>
      <a href="../home/index.php"
        class="menu-item <?= $current == 'index.php' && strpos($_SERVER['PHP_SELF'], 'tiket_umrah') !== false ? 'active' : '' ?>"
        title="Home">
        <i class="fa-solid fa-house"></i>
        <span>Home</span>
      </a>

      <a href="../service_list/index.php"
        class="menu-item <?= $current == 'index.php' && strpos($_SERVER['PHP_SELF'], 'hotel_mekkah') !== false ? 'active' : '' ?>"
        title="Service List">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Service List</span>
      </a>
      <?php endif; ?>


    </div>

    <!-- Bawah -->
<div class="logout">
<a href="../profile/profile.php" class="menu-item photo" title="photo">
<?php if ($user['photo'] !== null && $user['photo'] !== ""): ?>
      <img src="../uploads/profile/<?= $user['photo']; ?>" class="profile-icon">
<?php else: ?>
      <i class="fa-solid fa-circle-user photo-icon-default"></i>
<?php endif; ?>
  <span>Profile</span>

      <a href="../login.php" class="menu-item" title="Logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>
</body>

<style>
.profile-icon {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 8px;
}

.profile-icon-default {
    font-size: 26px;
    color: #ccc;
    margin-right: 8px;
}


  .sidebar {
    width: 70px;
    height: 100vh;
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    /* isi sidebar tetap di kiri */
    padding: 20px 10px;
    transition: all 0.4s ease;
    overflow: hidden;
    /* biar gak melebarin konten ke luar */
  }

  .logo {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-top: 10px;
  }

  .logo .menu-item {
    color: #007bff;
  }

  .menu {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: center;
    /* item tetap nempel kiri */
    margin-top: auto;
    margin-bottom: auto;
  }

  .menu-item {
    color: #2c79ff;
    font-size: 22px;
    text-decoration: none;
    display: flex;
    align-items: center;
    /* ikon & teks sejajar vertikal */
    justify-content: flex-start;
    /* biar konten mulai dari kiri */
    gap: 12px;
    /* jarak antara ikon dan teks */
    padding: 10px 14px;
    border-radius: 12px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    white-space: nowrap;
    width: 100%;
    /* biar teks bisa rata */
    box-sizing: border-box;
  }

  .menu-item:hover {
    color: #007bff;
    border: 2px solid #007bff;
    transform: scale(1.05);
  }

  /* Sidebar melebar pas salah satu menu di-hover */
  /* Untuk admin */
  .role-admin .sidebar:hover {
    width: 230px;
    transform: translateX(5px);
    box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
  }

  /* Untuk user biasa */
  .role-user .sidebar:hover {
    width: 200px;
    transform: translateX(3px);
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
  }

  .menu-item i {
    min-width: 25px;
    /* biar semua ikon punya lebar konsisten */
    text-align: center;
  }

/* Label selalu kelihatan */
.menu-item span {
    font-size: 15px;
    opacity: 0;
    visibility: visible;
    transition: opacity 0.3s ease;
}

/* Sidebar hover — tidak perlu lagi menyalakan span */
.sidebar:has(.menu-item:hover) .menu-item span {
    opacity: 1;
    visibility: visible;
}

/* Item aktif — tetap kelihatan */
.menu-item.active span {
    opacity: 1;
    visibility: visible;
}


  .logout {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-top: auto;
  }

  .logout .menu-item {
    color: #007bff;
  }

  .menu-item.photo {
    color: #007bff;
  }
</style>