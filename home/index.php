<?php
session_start();
require_once "../connection.php";
include "../sidebar.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ===============================
// FETCH USER
// ===============================
$user_q = mysqli_query($connection, "SELECT * FROM account WHERE id='{$user_id}' LIMIT 1");
$user   = mysqli_fetch_assoc($user_q);

// ===============================
// FILTERS
// ===============================
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$from   = isset($_GET['from'])   ? trim($_GET['from'])   : '';
$to     = isset($_GET['to'])     ? trim($_GET['to'])     : '';
$hasFilter = false;

// ===============================
// BASIC STATS
// ===============================
$totalOrders = mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT COUNT(*) AS cnt FROM orders WHERE account_id='{$user_id}'")
)['cnt'] ?? 0;

$totalPaid = mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT IFNULL(SUM(total_amount),0) AS total
                               FROM orders
                               WHERE account_id='{$user_id}' AND payment_status='paid'")
)['total'] ?? 0;

$totalUnpaid = mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT IFNULL(SUM(total_amount),0) AS total
                               FROM orders
                               WHERE account_id='{$user_id}' AND payment_status='unpaid'")
)['total'] ?? 0;

// ===============================
// RECENT ORDERS (6 TERBARU)
// ===============================
$recentQ = mysqli_query($connection,
    "SELECT * FROM orders
     WHERE account_id='{$user_id}'
     ORDER BY created_at DESC
     LIMIT 6"
);

// ===============================
// TABLE ORDERS (AUTO LIMIT 5 IF NO FILTER)
// ===============================
$sql = "SELECT * FROM orders WHERE account_id='{$user_id}'";

// Search filter
if ($search !== '') {
    $s = mysqli_real_escape_string($connection, $search);
    $hasFilter = true;

    $sql .= " AND (
        order_code LIKE '%$s%' OR
        service_type LIKE '%$s%' OR
        payment_status LIKE '%$s%'
    )";
}

// Date filter
if ($from !== '' && $to !== '') {
    $f = mysqli_real_escape_string($connection, $from);
    $t = mysqli_real_escape_string($connection, $to);
    $hasFilter = true;

    $sql .= " AND DATE(created_at) BETWEEN '$f' AND '$t'";
}

$sql .= " ORDER BY created_at DESC";

if (!$hasFilter) {
    $sql .= " LIMIT 5";
}

$tableOrders = mysqli_query($connection, $sql);

// ===============================
// CHART DATA (LAST 14 DAYS)
// ===============================
$chartLabels = [];
$chartValues = [];

for ($i = 13; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $chartLabels[] = $date;

    $q = mysqli_query($connection,
        "SELECT IFNULL(SUM(total_amount),0) AS total
         FROM orders
         WHERE account_id='{$user_id}'
         AND payment_status='paid'
         AND DATE(created_at)='{$date}'"
    );

    $row = mysqli_fetch_assoc($q);
    $chartValues[] = (float)($row['total'] ?? 0);
}

// ===============================
// FLASH MESSAGE
// ===============================
$flash = null;
if (isset($_SESSION['info'])) {
    $flash = $_SESSION['info'];
    unset($_SESSION['info']);
}
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
/* =============================== */
/* CSS */
/* =============================== */
:root { --bg:#f5f7ff; --card:#fff; --accent:#007bff; --muted:#778; }

* {
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body {
    display:flex;
    height:100vh;
    background:#f5f7ff;
}

.main {
    flex:1;
    padding:30px 40px;
    overflow-y:auto;
}

.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.header .title { font-size:22px; color:var(--accent); font-weight:700 }
.header .greeting { color:var(--muted) }

.grid-3 {
    display:grid;
    grid-template-columns:1fr 340px 220px;
    gap:18px;
    margin-bottom:18px;
}

.card {
    background:var(--card);
    padding:16px;
    border-radius:12px;
    box-shadow:0 6px 20px rgba(13,38,76,0.04);
}

.filter-card {
    width:100%;
    display:flex;
    gap:10px;
    padding:15px;
    background:white;
    border-radius:15px;
    margin-bottom:25px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.filter-card input,
.filter-card .filter-search {
    flex:1;
    padding:10px;
    border-radius:10px;
    border:1px solid #ccc;
}

.filter-card input[type="date"] {
    max-width:160px;
    flex:0 0 160px;
}

.filter-card button {
    padding:10px 18px;
    border:none;
    background:#0066ff;
    color:white;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
    flex:0 0 120px;
}

.quick-actions {
    display:grid;
    grid-template-columns:1fr;
    gap:12px;
}

.action {
    background:linear-gradient(180deg,#ffffff,#f5f8ff);
    padding:16px;
    border-radius:14px;
    text-align:center;
    cursor:pointer;
    box-shadow:0 6px 16px rgba(0,0,0,0.08);
    transition:0.25s;
}

.action:hover {
    transform:translateY(-3px);
    box-shadow:0 10px 22px rgba(0,0,0,0.12);
}

.action i {
    font-size:22px;
    color:var(--accent);
    margin-bottom:8px;
}

.recent-orders { display:grid; gap:10px }

.order-item {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px;
    border-radius:10px;
    background:linear-gradient(180deg,#fff,#fbfdff);
    box-shadow:0 4px 12px rgba(10,10,10,0.03);
}

.badge {
    padding:6px 10px;
    border-radius:8px;
    font-weight:600;
    font-size:12px;
}

.badge.pending { background:#fff3cd; color:#856404 }
.badge.process { background:#cce5ff; color:#004085 }
.badge.done    { background:#d4edda; color:#155724 }
.badge.cancel  { background:#f8d7da; color:#721c24 }

.table-card { overflow:auto }

.status-badge {
    padding:6px 12px;
    border-radius:6px;
    font-size:12px;
    font-weight:600;
    display:inline-block;
    cursor:default;
}

.status-badge.unpaid {
    background:#f8d7da;
    color:#721c24;
    cursor:pointer;
}

.status-badge.paid {
    background:#d1ecf1;
    color:#0c5460;
}

.clickable-badge:hover { opacity:0.75 }

table {
    width:100%;
    border-collapse:collapse;
}

th, td {
    padding:10px;
    border-bottom:1px solid #eee;
    text-align:left;
}

th {
    background:var(--accent);
    color:#fff;
    position:sticky;
    top:0;
}

@media(max-width:1000px) { .grid-3 { grid-template-columns:1fr 1fr } }
@media(max-width:700px)  { .grid-3 { grid-template-columns:1fr } }
</style>
</head>

<body>
<div class="main">

    <div class="top-bar">
        <h1>Choose Your Laundry Service</h1>
        <div class="user-info">
            <span>👋 Hi, <?= htmlspecialchars($user['nama']); ?></span>
        </div>
    </div>

    <div class="card" style="margin-bottom:12px">
        <h4 style="margin-bottom:10px;color:var(--muted)">Quick Actions</h4>
        <div class="quick-actions">
            <div class="action" onclick="location.href='../service_list/index.php'">
                <i class="fa-solid fa-plus"></i>
                <div>Add Order</div>
            </div>
        </div>
    </div>

    <form class="filter-card" method="GET">
        <input type="date" name="from"   value="<?= $from ?>">
        <input type="date" name="to"     value="<?= $to ?>">
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
                    <th>Tanggal</th>
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

                    <td data-label="Pembayaran">
                        <?php if ($row['payment_status'] === 'unpaid'): ?>
                            <a href="../payment/pay.php?id=<?= $row['va_id']; ?>&order=<?= $row['id']; ?>"
                               class="status-badge unpaid clickable-badge"
                               style="text-decoration:none;display:inline-block;">
                                unpaid
                            </a>
                        <?php else: ?>
                            <span class="status-badge paid">paid</span>
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars(date('d M Y H:i', strtotime($row['created_at']))) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
<script>
// flash message
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
