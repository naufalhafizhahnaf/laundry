<?php
session_start();
require_once "../connection.php";
include "../sidebar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['user_id'];
$query = mysqli_query($connection, "SELECT * FROM account WHERE id='$id'");
$user = mysqli_fetch_assoc($query);

// FILTER
$search = $_GET['search'] ?? "";
$from = $_GET['from'] ?? "";
$to = $_GET['to'] ?? "";

// MAIN QUERY
$sql = "SELECT * FROM orders WHERE 1";

if ($search != "") {
    $s = mysqli_real_escape_string($connection, $search);
    $sql .= " AND (order_code LIKE '%$s%' OR service_type LIKE '%$s%' OR payment_status LIKE '%$s%')";
}

if ($from != "" && $to != "") {
    $sql .= " AND DATE(created_at) BETWEEN '$from' AND '$to'";
}

$sql .= " ORDER BY created_at DESC";
$orders = mysqli_query($connection, $sql);

// TOTAL INCOME
$incomeQuery = "SELECT SUM(total_amount) AS total FROM orders WHERE payment_status='paid'";
if ($from != "" && $to != "") {
    $incomeQuery .= " AND DATE(created_at) BETWEEN '$from' AND '$to'";
}
$income = mysqli_fetch_assoc(mysqli_query($connection, $incomeQuery))['total'] ?? 0;

// CHART DATA
$chartQuery = "
    SELECT DATE(created_at) AS day, SUM(total_amount) AS total
    FROM orders 
    WHERE payment_status='paid'
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)
";
$chart = mysqli_query($connection, $chartQuery);
$labels = [];
$values = [];
while ($row = mysqli_fetch_assoc($chart)) {
    $labels[] = $row['day'];
    $values[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Statistics</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
body { display:flex; background:#f5f7ff; }

.main { flex:1; padding:30px 40px; overflow-y:auto; }
h1 { font-size:24px; font-weight:600; margin-bottom:25px; color:#222; }

/* TOP SECTION */
.top-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    flex-wrap: wrap;
    align-items: stretch;
}

.chart-box {
    flex: 1;
    min-width: 300px;
    height: 200px;
    background: white;
    padding: 15px;
    border-radius: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* ✅ FULL CENTER FIX */
.income-card {
    width: 260px;
    height: 200px; /* disamakan dengan chart-box */
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);

    display: flex;
    flex-direction: column;
    justify-content: center; /* tengah atas-bawah */
    align-items: center;     /* tengah kanan-kiri */
    text-align: center;
}

.income-card h3 { 
    color: #007bff; 
    font-size: 16px; 
}

.income-card p { 
    font-size: 28px; 
    font-weight: 600; 
    margin-top: 5px; 
}

/* MOBILE */
@media (max-width: 768px) {
    .income-card {
        width: 100%;
    }
}

/* FILTER */
.filter-card {
    width:100%;
    display:flex;
    flex-wrap:wrap;
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
    flex:1;
    min-width:150px;
}

.filter-search {
    flex:2;
}

.filter-card button {
    padding:10px;
    border:none;
    background:#0066ff;
    color:white;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
}

/* TABLE */
.card {
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    margin-bottom:20px;
    overflow-x:auto;
}

table { width:100%; border-collapse:collapse; }
th, td { padding:12px; font-size:14px; border-bottom:1px solid #eee; }
th { background:#eef4ff; color:#007bff; font-weight:600; }

.paid { color:green; font-weight:600; }
.unpaid { color:red; font-weight:600; }

/* ================= MOBILE RESPONSIVE ================= */
@media (max-width: 768px){

    body{
        flex-direction: column;
    }

    .main{
        padding:20px 15px;
    }

    h1{
        font-size:20px;
    }

    .top-stats{
        flex-direction: column;
    }

    .chart-box{
        width:100%;
        height:220px;
    }

    .income-card{
        width:100%;
    }

    .filter-card{
        flex-direction: column;
    }

    .filter-card input,
    .filter-card button{
        width:100%;
    }

    /* RESPONSIVE TABLE -> CARD STYLE */
    table, thead, tbody, th, td, tr {
        display: block;
        width: 100%;
    }

    thead {
        display:none;
    }

    table tr {
        margin-bottom: 15px;
        background:white;
        padding:12px;
        border-radius:12px;
        box-shadow:0 2px 6px rgba(0,0,0,0.05);
    }

    table td {
        border: none;
        font-size: 14px;
        padding:6px 0;
    }

    table td::before {
        content: attr(data-label);
        font-weight:600;
        color:#007bff;
        margin-bottom:3px;
        display:block;
    }
}
</style>
</head>

<body>

<div class="main">
    <h1>Order Statistics</h1>

    <!-- TOP SECTION -->
    <div class="top-stats">
        <div class="chart-box">
            <canvas id="incomeChart"></canvas>
        </div>

        <div class="income-card">
            <h3>Total Income</h3>
            <p>Rp <?= number_format($income, 0, ',', '.') ?></p>
        </div>
    </div>

    <!-- FILTER -->
    <form class="filter-card" method="GET">
        <input type="date" name="from" value="<?= $from ?>">
        <input type="date" name="to" value="<?= $to ?>">
        <input type="text" name="search" class="filter-search" placeholder="Search..." value="<?= $search ?>">
        <button type="submit">Filter</button>
    </form>

    <!-- TABLE -->
    <div class="card">
        <h3 style="color:#007bff; margin-bottom:15px;">Order List</h3>
        <table>
            <thead>
                <tr>
                    <th>Order Code</th>
                    <th>Date</th>
                    <th>Service</th>
                    <th>Weight</th>
                    <th>Amount</th>
                    <th>Payment</th>
                </tr>
            </thead>

            <tbody>
            <?php while($o = mysqli_fetch_assoc($orders)): ?>
            <tr>
                <td data-label="Order Code"><?= $o['order_code'] ?></td>
                <td data-label="Date"><?= $o['created_at'] ?></td>
                <td data-label="Service"><?= $o['service_type'] ?></td>
                <td data-label="Weight"><?= $o['weight'] ?> Kg</td>
                <td data-label="Amount">Rp <?= number_format($o['total_amount'],0,',','.') ?></td>
                <td data-label="Payment" class="<?= $o['payment_status']=='paid'?'paid':'unpaid' ?>">
                    <?= ucfirst($o['payment_status']) ?>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>

        </table>
    </div>

</div>

<script>
const ctx = document.getElementById('incomeChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: "Income",
            data: <?= json_encode($values) ?>,
            borderWidth: 2,
            tension: 0.3,
            fill: false
        }]
    },
    options: {
        plugins: { legend: { display:false }},
        scales: {
            x: { ticks:{ font:{ size:10 }}},
            y: { ticks:{ font:{ size:10 }}}
        },
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

</body>
</html>