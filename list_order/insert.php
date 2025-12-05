<?php
require_once "../connection.php";
session_start();
include "../sidebar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['user_id'];
$userQuery = mysqli_query($connection, "SELECT * FROM account WHERE id='$id'");
$user = mysqli_fetch_assoc($userQuery);

// Ambil daftar customer (role user)
$customers = mysqli_query($connection, "SELECT id, nama FROM account WHERE role='user'");

// Harga per KG
$prices = [
    'regular' => 5000,
    'express' => 8000,
    'exclusive' => 12000
];

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $account_id = $_POST['account_id'];
    $service = $_POST['service_type'];
    $weight = floatval($_POST['weight']);
    $payment_method = $_POST['payment_method'];

    $price_per_kg = $prices[$service];
    $total = $price_per_kg * $weight;
    
    $code = "ORD" . time();

    $insert = mysqli_query($connection, "
        INSERT INTO orders 
        (order_code, account_id, service_type, weight, total_amount, status, payment_status, payment_method, received_date, due_date, created_at)
        VALUES
        ('$code','$account_id','$service','$weight','$total','pending','unpaid','$payment_method',NOW(),DATE_ADD(NOW(), INTERVAL 2 DAY),NOW())
    ");

    if ($insert) {
        $_SESSION['info'] = [
            'status' => 'success',
            'message' => 'Order berhasil ditambahkan'
        ];
        header("Location: index.php");
        exit;
    } else {
        $message = "Gagal menambah order : " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tambah Order</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
*{font-family:Poppins;margin:0;padding:0;box-sizing:border-box}
body{display:flex;background:#f5f7ff}
.main{flex:1;padding:30px}
form{
  background:white;
  padding:25px;
  border-radius:15px;
  max-width:500px;
  box-shadow:0 4px 12px rgba(0,0,0,0.05)
}
label{display:block;margin-top:15px;font-weight:500}
input,select{
  width:100%;
  padding:10px;
  margin-top:5px;
  border-radius:8px;
  border:1px solid #ccc;
}
button{
  margin-top:20px;
  padding:10px;
  border:none;
  border-radius:20px;
  background:#28a745;
  color:white;
  cursor:pointer;
  font-weight:600;
}
button:hover{background:#218838}
.message{color:red;margin-bottom:10px}
</style>
</head>

<body>

<div class="main">
<h2>Tambah Order</h2>

<?php if($message): ?>
<div class="message"><?= $message ?></div>
<?php endif; ?>

<form method="POST">

<label>Customer</label>
<select name="account_id" required>
  <option value="">-- Pilih Customer --</option>
  <?php while($c = mysqli_fetch_assoc($customers)): ?>
    <option value="<?= $c['id'] ?>"><?= $c['nama'] ?></option>
  <?php endwhile; ?>
</select>

<label>Jenis Layanan</label>
<select name="service_type" required>
  <option value="regular">Regular (5.000/kg)</option>
  <option value="express">Express (8.000/kg)</option>
  <option value="exclusive">Exclusive (12.000/kg)</option>
</select>

<label>Berat (Kg)</label>
<input type="number" step="0.1" name="weight" required>

<label>Metode Pembayaran</label>
<select name="payment_method" required>
  <option value="cash">Cash</option>
  <option value="transfer">Transfer</option>
  <option value="qris">QRIS</option>
</select>

<button type="submit">Simpan Order</button>

</form>
</div>

</body>
</html>
