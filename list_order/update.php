<?php
require_once "../connection.php";
// Pastikan session_start() sudah dijalankan sebelum mengakses $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../sidebar.php"; // Ini akan meng-include HTML dan CSS sidebar

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// ambil user login
$id = $_SESSION['user_id'];
$userQuery = mysqli_query($connection, "SELECT * FROM account WHERE id='$id'");
$user = mysqli_fetch_assoc($userQuery);

// cek id order
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$order_id = mysqli_real_escape_string($connection, $_GET['id']);

// ambil data order
$queryOrder = mysqli_query($connection, "
    SELECT o.*, a.nama as customer_name 
    FROM orders o
    JOIN account a ON o.account_id = a.id
    WHERE o.id = '$order_id'
");

if (mysqli_num_rows($queryOrder) == 0) {
    echo "<script>alert('Order tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

$order = mysqli_fetch_assoc($queryOrder);
$message = "";

// ================= UPDATE ===================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan bersihkan data input
    $service_type   = mysqli_real_escape_string($connection, $_POST['service_type']);
    $weight         = floatval($_POST['weight']);
    $total_amount   = floatval($_POST['total_amount']);
    $payment_status = mysqli_real_escape_string($connection, $_POST['payment_status']);
    $payment_method = mysqli_real_escape_string($connection, $_POST['payment_method']);
    $received_date  = mysqli_real_escape_string($connection, $_POST['received_date']);
    $due_date       = mysqli_real_escape_string($connection, $_POST['due_date']);

    // Query Update
    $update = mysqli_query($connection, "
        UPDATE orders SET
            service_type    = '$service_type',
            weight          = '$weight',
            total_amount    = '$total_amount',
            payment_status  = '$payment_status',
            payment_method  = '$payment_method',
            received_date   = '$received_date',
            due_date        = '$due_date'
        WHERE id = '$order_id'
    ");

    if ($update) {
        $message = "Order berhasil diperbarui!";
        // Ambil ulang data order yang baru diupdate
        $queryOrder = mysqli_query($connection, "
            SELECT o.*, a.nama as customer_name 
            FROM orders o
            JOIN account a ON o.account_id = a.id
            WHERE o.id = '$order_id'
        ");
        $order = mysqli_fetch_assoc($queryOrder);
    } else {
        $message = "Gagal memperbarui order: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Order</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
/* -------------------------------------- */
/* CSS untuk Konten Utama (Form Edit) */
/* -------------------------------------- */
*{
    font-family: 'Poppins', sans-serif;
    margin:0;
    padding:0;
    box-sizing:border-box;
}
body{
    display:flex;
    background:#f5f7ff;
}
.main{
    flex:1;
    padding:30px;
}
.card{
    background:white;
    padding:25px;
    border-radius:15px;
    max-width:600px;
    box-shadow:0 4px 15px rgba(0,0,0,0.05);
}
h2{
    margin-bottom:15px;
}
label{
    margin-top:12px;
    display:block;
    font-weight:500;
}
input,select{
    width:100%;
    padding:10px;
    margin-top:5px;
    border-radius:10px;
    border:1px solid #ccc;
}
button{
    margin-top:20px;
    background:#007bff;
    border:none;
    color:white;
    padding:12px;
    width:100%;
    border-radius:12px;
    cursor:pointer;
}
button:hover{
    background:#0056b3;
}
.message{
    margin-bottom:15px;
    padding:10px;
    border-radius:10px;
    background:#e6ffed;
    border:1px solid #2ecc71;
    color:#156f3c;
}
.back{
    display:inline-block;
    margin-top:15px;
    text-decoration:none;
    color:#007bff;
    font-weight:500;
}
/* Catatan: CSS Sidebar di-*include* dari sidebar.php */
</style>
</head>

<body>
<div class="main">
    <div class="card">
        <h2>Edit Order</h2>

        <?php if($message): ?>
            <div class="message"><?= $message; ?></div>
        <?php endif; ?>

        <form method="POST">

            <label>Kode Order</label>
            <input type="text" value="<?= htmlspecialchars($order['order_code']); ?>" disabled>

            <label>Customer</label>
            <input type="text" value="<?= htmlspecialchars($order['customer_name']); ?>" disabled>

            <label>Layanan</label>
            <select name="service_type" required>
                <option value="regular" <?= $order['service_type']=='regular'?'selected':''; ?>>Regular</option>
                <option value="express" <?= $order['service_type']=='express'?'selected':''; ?>>Express</option>
            </select>

            <label>Berat (Kg)</label>
            <input type="number" step="0.1" name="weight" value="<?= htmlspecialchars($order['weight']); ?>" required>

            <label>Total Harga</label>
            <input type="number" name="total_amount" value="<?= htmlspecialchars($order['total_amount']); ?>" required>

            <label>Status Pembayaran</label>
            <select name="payment_status">
                <option value="unpaid" <?= $order['payment_status']=='unpaid'?'selected':''; ?>>Unpaid</option>
                <option value="paid" <?= $order['payment_status']=='paid'?'selected':''; ?>>Paid</option>
            </select>

            <label>Metode Pembayaran</label>
            <select name="payment_method">
                <option value="cash" <?= $order['payment_method']=='cash'?'selected':''; ?>>Cash</option>
                <option value="transfer" <?= $order['payment_method']=='transfer'?'selected':''; ?>>Transfer</option>
                <option value="qris" <?= $order['payment_method']=='qris'?'selected':''; ?>>QRIS</option>
            </select>

            <label>Tanggal Diterima</label>
            <input type="date" name="received_date" value="<?= date('Y-m-d', strtotime($order['received_date'])); ?>">

            <label>Tanggal Selesai</label>
            <input type="date" name="due_date" value="<?= date('Y-m-d', strtotime($order['due_date'])); ?>">

            <button type="submit">Simpan Perubahan</button>

        </form>

        <a class="back" href="index.php">← Kembali</a>
    </div>
</div>

</body>
</html>