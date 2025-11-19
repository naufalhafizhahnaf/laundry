<?php
require_once "../connection.php";
session_start();
include "../sidebar.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$order_id = $_GET['id'];

$orderQuery = mysqli_query($connection,
    "SELECT o.*, a.nama, a.alamat, a.no_hp 
    FROM orders o
    JOIN account a ON o.account_id = a.id
    WHERE o.id='$order_id'"
);
$order = mysqli_fetch_assoc($orderQuery);

// Karena tidak ada tabel order_items
$items = [
    [
        "service_type" => $order["service_type"],
        "weight"       => $order["weight"],
        "total_amount" => $order["total_amount"]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Detail Order</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{display:flex;height:100vh;background:#f0f4ff;}

/* SIDEBAR BIRU */
.left-panel{
    width:320px;
    background:#3c8bff;
    padding:40px 30px;
    color:white;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}
.left-panel h1{
    font-size:40px;
    font-weight:700;
    line-height:45px;
}
.back-btn{
    background:white;
    color:#3c8bff;
    padding:10px 18px;
    border-radius:30px;
    display:flex;
    align-items:center;
    gap:10px;
    border:none;
    cursor:pointer;
    font-weight:600;
}
.back-btn i{font-size:18px;}

/* RIGHT CONTENT */
.right-panel{
    flex:1;
    padding:40px 50px;
    overflow-y:auto;
}

/* GRID INFO CUSTOMER */
.info-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:25px;
    margin-bottom:20px;
}
.info-box label{
    font-size:14px;
    color:#555;
}
.info-box input{
    width:100%;
    padding:10px;
    background:#f1f4ff;
    border:none;
    border-radius:8px;
}

/* TABEL ITEM */
.table-box{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 4px 20px rgba(0,0,0,0.05);
    margin-bottom:20px;
}
table{
    width:100%;
    border-collapse:collapse;
}
th{
    background:#eef3ff;
    padding:12px;
    text-align:left;
    font-size:14px;
}
td{
    padding:12px;
    font-size:14px;
}
tr:nth-child(even){background:#f9faff;}
.item-icon{
    width:35px;height:35px;background:#3c8bff;border-radius:8px;
}

/* NOTE */
.note-box textarea{
    width:100%;
    height:120px;
    border:none;
    padding:15px;
    background:#f1f4ff;
    border-radius:10px;
}

/* TOTAL */
.total-label{
    font-size:22px;
    font-weight:600;
    color:#3c8bff;
    text-align:right;
}
.total-amount{
    font-size:40px;
    font-weight:700;
    text-align:right;
    color:#3c8bff;
    margin-top:-10px;
}

/* RESPONSIVE */
@media(max-width:900px){
    body{flex-direction:column;}
    .left-panel{width:100%;height:auto;}
}
</style>

</head>
<body>

<!-- LEFT BLUE PANEL -->
<div class="left-panel">
    <h1>Product<br>Details</h1>

    <button class="back-btn" onclick="history.back()">
        <i class="fa-solid fa-arrow-left"></i> Back
    </button>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">

    <!-- CUSTOMER INFO -->
    <div class="info-grid">
        <div class="info-box">
            <label>Nama</label>
            <input type="text" value="<?= $order['nama']; ?>" disabled>
        </div>

        <div class="info-box">
            <label>Alamat</label>
            <input type="text" value="<?= $order['alamat']; ?>" disabled>
        </div>

        <div class="info-box">
            <label>No Hp</label>
            <input type="text" value="<?= $order['no_hp']; ?>" disabled>
        </div>
    </div>

    <!-- TABEL PRODUK -->
    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Service ID</th>
                    <th>User ID</th>
                    <th>Transaction Date</th>
                    <th>Total Product</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i): ?>
                <tr>
                    <td><div class="item-icon"></div></td>
<td><?= $order['order_code']; ?></td>
<td><?= $order['account_id']; ?></td>
<td><?= $order['created_at']; ?></td>
<td><?= $order['weight']; ?> Kg</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- NOTE -->
    <div class="note-box">
        <label>Note</label>
        <textarea disabled><?= $order['note']; ?></textarea>
    </div>

    <!-- TOTAL -->
    <p class="total-label">Total</p>
    <p class="total-amount">Rp <?= number_format($order['total_amount'],0,',','.'); ?></p>

</div>

</body>
</html>
