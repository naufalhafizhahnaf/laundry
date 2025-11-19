<?php
session_start();
require_once "../connection.php";
include "../sidebar.php"; // sidebar for user navigation

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
// Fetch user
$user_q = mysqli_query($connection, "SELECT * FROM account WHERE id='{$user_id}' LIMIT 1");
$user = mysqli_fetch_assoc($user_q);

// Filters (for table/search)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

// Basic stats
$totalOrdersQ = mysqli_query($connection, "SELECT COUNT(*) AS cnt FROM orders WHERE account_id='{$user_id}'");
$totalOrders = mysqli_fetch_assoc($totalOrdersQ)['cnt'] ?? 0;

$totalPaidQ = mysqli_query($connection, "SELECT IFNULL(SUM(total_amount),0) AS total FROM orders WHERE account_id='{$user_id}' AND payment_status='paid'");
$totalPaid = mysqli_fetch_assoc($totalPaidQ)['total'] ?? 0;

$totalUnpaidQ = mysqli_query($connection, "SELECT IFNULL(SUM(total_amount),0) AS total FROM orders WHERE account_id='{$user_id}' AND payment_status='unpaid'");
$totalUnpaid = mysqli_fetch_assoc($totalUnpaidQ)['total'] ?? 0;

// Recent orders (limit 6)
$recentQ = mysqli_query($connection, "SELECT * FROM orders WHERE account_id='{$user_id}' ORDER BY created_at DESC LIMIT 6");

// Table query with optional search / date filter
$sql = "SELECT * FROM orders WHERE account_id='{$user_id}'";
if ($search !== '') {
  $s = mysqli_real_escape_string($connection, $search);
  $sql .= " AND (order_code LIKE '%$s%' OR service_type LIKE '%$s%' OR payment_status LIKE '%$s%')";
}
if ($from !== '' && $to !== '') {
  $f = mysqli_real_escape_string($connection, $from);
  $t = mysqli_real_escape_string($connection, $to);
  $sql .= " AND DATE(created_at) BETWEEN '$f' AND '$t'";
}
$sql .= " ORDER BY created_at DESC";
$tableOrders = mysqli_query($connection, $sql);

// Chart data: income per day for last 14 days (user scope)
$chartLabels = [];
$chartValues = [];

$days = 14;
for ($i = $days - 1; $i >= 0; $i--) {
  $d = date('Y-m-d', strtotime("-{$i} days"));
  $chartLabels[] = $d;
  $q = mysqli_query($connection, "SELECT IFNULL(SUM(total_amount),0) AS total FROM orders WHERE account_id='{$user_id}' AND payment_status='paid' AND DATE(created_at)='{$d}'");
  $row = mysqli_fetch_assoc($q);
  $chartValues[] = (float)($row['total'] ?? 0);
}

// Flash notification (example: set $_SESSION['info'] = ['status'=>'success','message'=>'...'] elsewhere)
$flash = null;
if (isset($_SESSION['info'])) { $flash = $_SESSION['info']; unset($_SESSION['info']); }

?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Home — LaundryIn (Premium)</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">

<style>
:root{--bg:#f5f7ff;--card:#fff;--accent:#007bff;--muted:#778;}
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
    body { display:flex; height:100vh; background:#f5f7ff; }
        .main { flex:1; padding:30px 40px; overflow-y:auto; }
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.header .title{font-size:22px;color:var(--accent);font-weight:700}
.header .greeting{color:var(--muted)}

.grid-3{display:grid;grid-template-columns:1fr 340px 220px;gap:18px;margin-bottom:18px}
.card{background:var(--card);padding:16px;border-radius:12px;box-shadow:0 6px 20px rgba(13,38,76,0.04)}
/* FILTER */
.filter-card {
    width:1250px;
    display:flex;
    gap:10px;
    padding:15px;
    background:white;
    border-radius:15px;
    margin-bottom:25px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.filter-card input {
    padding:10px;
    border-radius:10px;
    border:1px solid #ccc;
}

.filter-search {
    width:1000px; /* 🔥 Search tidak terlalu panjang */
}

.filter-card button {
    padding:10px 18px;
    border:none;
    background:#0066ff;
    color:white;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
}

.stats{display:flex;gap:12px}
.stat{flex:1;padding:14px;border-radius:10px}
.stat h4{margin:0;font-size:13px;color:var(--muted)}
.stat .value{font-size:20px;font-weight:700;margin-top:6px}

.quick-actions {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
}

.action{
    background:linear-gradient(180deg,#ffffff,#f5f8ff);
    padding:16px;
    border-radius:14px;
    text-align:center;
    cursor:pointer;
    box-shadow:0 6px 16px rgba(0,0,0,0.08);
    transition:0.25s;
}

.action:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 22px rgba(0,0,0,0.12);
}

.action i{
    font-size:22px;
    color:var(--accent);
    margin-bottom:8px;
}


.recent-orders{display:grid;gap:10px}
.order-item{display:flex;justify-content:space-between;align-items:center;padding:12px;border-radius:10px;background:linear-gradient(180deg,#fff,#fbfdff);box-shadow:0 4px 12px rgba(10,10,10,0.03)}
.order-left{display:flex;gap:12px;align-items:center}
.badge{padding:6px 10px;border-radius:8px;font-weight:600;font-size:12px}
.badge.pending{background:#fff3cd;color:#856404}
.badge.process{background:#cce5ff;color:#004085}
.badge.done{background:#d4edda;color:#155724}
.badge.cancel{background:#f8d7da;color:#721c24}

.table-card{overflow:auto}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #eee;text-align:left}
th{background:var(--accent);color:#fff;position:sticky;top:0}

@media(max-width:1000px){.grid-3{grid-template-columns:1fr 1fr}}
@media(max-width:700px){.grid-3{grid-template-columns:1fr}.container{padding:18px;margin-left:0}}

</style>
</head>
<body>
<div class="main">

  <div class="header">
    <div>
      <div class="title">LaundryIn (Premium)</div>
      <div class="greeting">Hi, <?= htmlspecialchars($user['nama'] ?? 'User') ?> — Selamat datang!</div>
    </div>
    <div>
      <button onclick="window.location.href='profile.php'" style="background:transparent;border:0;cursor:pointer;font-size:18px">
        <i class="fa-solid fa-user"></i>
      </button>
    </div>
  </div>

  <div class="grid-2">
<div class="card" style="margin-bottom:12px">
  <h4 style="margin:0 0 10px 0;color:var(--muted)">Quick Actions</h4>
  <div class="quick-actions" style="grid-template-columns:repeat(2,1fr)">
    
    <div class="action" onclick="location.href='insert.php'">
      <i class="fa-solid fa-plus"></i>
      <div>Add Order</div>
    </div>

    <div class="action" onclick="location.href='profile.php'">
      <i class="fa-solid fa-user-cog"></i>
      <div>Profile</div>
    </div>

  </div>
</div>
    <!-- LEFT: filter + table -->
    <div>
    <!-- ==================== FILTER BOX ===================== -->
    <form class="filter-card" method="GET">
        <input type="date" name="from" value="<?= $from ?>">
        <input type="date" name="to" value="<?= $to ?>">
        <input type="text" name="search" class="filter-search" placeholder="Search..." value="<?= $search ?>">
        <button type="submit">Filter</button>
    </form>

      <div class="card table-card">
        <h3 style="margin-top:0;color:var(--accent)">Riwayat Order</h3>
        <table>
          <thead>
            <tr>
              <th>Kode</th>
              <th>Service</th>
              <th>Berat</th>
              <th>Total</th>
              <th>Status</th>
              <th>Pembayaran</th>
              <th>Tgl</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($tableOrders)): ?>
            <tr>
              <td><?= htmlspecialchars($row['order_code']) ?></td>
              <td><?= htmlspecialchars($row['service_type']) ?></td>
              <td><?= number_format($row['weight'],2) ?> Kg</td>
              <td>Rp <?= number_format($row['total_amount'],0,',','.') ?></td>
              <td><span class="badge <?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
              <td><?= htmlspecialchars($row['payment_status']) ?></td>
              <td><?= htmlspecialchars(date('d M Y H:i', strtotime($row['created_at']))) ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MIDDLE: mini chart + quick actions + stats -->
    <div>


    <!-- RIGHT: recent orders summary -->
    <div>
      <div class="card">
        <h4 style="margin:0 0 10px 0;color:var(--muted)">Recent Orders</h4>
        <div class="recent-orders">
          <?php while($r = mysqli_fetch_assoc($recentQ)): ?>
            <div class="order-item">
              <div class="order-left">
                <div style="width:44px;height:44;border-radius:8px;background:#f3f7ff;display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--accent)">#</div>
                <div>
                  <div style="font-weight:700"><?= htmlspecialchars($r['order_code']) ?></div>
                  <div style="font-size:13px;color:var(--muted)"><?= htmlspecialchars($r['service_type']) ?> • <?= number_format($r['weight'],2) ?> Kg</div>
                </div>
              </div>
              <div style="text-align:right">
                <div style="font-weight:700">Rp <?= number_format($r['total_amount'],0,',','.') ?></div>
                <div style="margin-top:6px"><span class="badge <?= htmlspecialchars($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>

      <div style="height:14px"></div>

      <div class="card">
        <h4 style="margin:0 0 10px 0;color:var(--muted)">Need Help?</h4>
        <p style="margin:0 0 8px 0">Butuh bantuan atau ingin chat admin? Klik tombol di bawah.</p>
        <button onclick="location.href='support.php'" style="background:var(--accent);color:#fff;padding:10px;border-radius:10px;border:0;cursor:pointer">Contact Support</button>
      </div>

    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
<script>
// flash
<?php if($flash): ?>
  iziToast.<?= $flash['status'] === 'success' ? 'success' : 'error' ?>({
    title: "<?= $flash['status'] === 'success' ? 'Sukses' : 'Gagal' ?>",
    message: "<?= addslashes($flash['message']) ?>",
    position: 'topCenter'
  });
<?php endif; ?>
</script>
</body>
</html>
