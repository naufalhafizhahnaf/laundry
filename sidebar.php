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

<!-- WRAPPER UNTUK ANTI KEDUT -->
<div class="sidebar-wrapper">
  <div class="sidebar">

    <div class="logo">
      <a href="../dashboard/index.php"
        class="menu-item <?= $current == 'index.php' && strpos($_SERVER['PHP_SELF'], 'tiket_umrah') !== false ? 'active' : '' ?>"
        title="Tiket Umrah">
        <i class="fa-solid fa-circle-nodes"></i>
      </a>
    </div>

    <div class="menu">

      <?php if ($role === 'admin') : ?>
        <a href="../Statistik/index.php"
          class="menu-item <?= $current == 'index.php' && strpos($_SERVER['PHP_SELF'], 'statistic') !== false ? 'active' : '' ?>"
          title="Statistic">
          <i class="fa-solid fa-chart-simple"></i>
          <span>Statistic</span>
        </a>

        <a href="../users/index.php"
          class="menu-item <?= $current == 'index.php' && strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : '' ?>"
          title="User Management">
          <i class="fa-solid fa-users"></i>
          <span>User Management</span>
        </a>

        <a href="../list_order/index.php"
          class="menu-item <?= $current == 'index.php' && strpos($_SERVER['PHP_SELF'], 'List_order') !== false ? 'active' : '' ?>"
          title="List Order">
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

    <!-- BAWAH -->
    <div class="logout">
      <a href="../profile/profile.php" class="menu-item photo" title="photo">
        <?php if ($user && $user['photo']) : ?>
          <img src="../uploads/profile/<?= $user['photo']; ?>" class="profile-icon">
        <?php else: ?>
          <i class="fa-solid fa-circle-user profile-icon-default"></i>
        <?php endif; ?>
        <span>Profile</span>
      </a>

      <a href="../login.php" class="menu-item" title="Logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
      </a>
    </div>

  </div>
</div>

</body>

<style>
/* Profile image */
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

/* ----- WRAPPER ANTI KEDUT ----- */
.sidebar-wrapper {
  width: 70px;
  transition: width .35s ease;
  height: 100vh;
}

.role-admin .sidebar-wrapper:hover {
  width: 230px;
}

.role-user .sidebar-wrapper:hover {
  width: 200px;
}

/* SIDEBAR */
.sidebar {
  width: 100%;
  height: 100vh;
  background: #fff;
  display: flex;
  flex-direction: column;
  padding: 20px 10px;
  overflow: hidden;
  transition: all .35s ease;
}

/* Menu */
.menu,
.logo {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: flex-start;
}

.menu {
  margin-top: auto;
  margin-bottom: auto;
}

/* Items */
.menu-item {
  color: #2c79ff;
  font-size: 22px;
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  width: 100%;
  padding: 10px 14px;
  border-radius: 12px;
  transition: .25s;
}

.menu-item:hover {
  color: #007bff;
  border: 2px solid #007bff;
  transform: scale(1.04);
}

/* ICON SPACING */
.menu-item i {
  min-width: 25px;
  text-align: center;
}

/* LABEL */
.menu-item span {
  font-size: 15px;
  opacity: 0;
  transition: opacity .3s ease;
  white-space: nowrap;
}

/* Tampil saat hover */
.sidebar-wrapper:hover .menu-item span,
.menu-item.active span {
  opacity: 1;
}

/* Logout section */
.logout {
  margin-top: auto;
  display: flex;
  flex-direction: column;
  gap: 5px;
}
</style>
