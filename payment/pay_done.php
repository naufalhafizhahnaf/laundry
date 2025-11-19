<?php
// pay_done.php — Menandai transaksi sudah dibayar dan update status di database

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Path file data.json
$file = realpath(__DIR__ . '/data.json');
if (!$file || !file_exists($file)) {
  $file = __DIR__ . '/data.json';
  file_put_contents($file, '{}');
}

$json = file_get_contents($file);
$data = json_decode($json, true);
if (!is_array($data)) $data = [];

// Ambil parameter dari URL
$id = $_GET['id'] ?? null;
$autoPay = isset($_GET['paid']) && $_GET['paid'] == '1';
$payLater = isset($_GET['paid']) && $_GET['paid'] == '0';

if (!$id) {
  die('❌ Transaksi tidak ditemukan (ID kosong)');
}

// Kalau belum ada entry, buat dummy
if (!isset($data[$id])) {
  $va = 'VA-' . strtoupper(substr(md5($id), 0, 6));
  $data[$id] = [
    'va' => $va,
    'amount' => 10000,
    'status' => 'pending',
    'created_at' => time()
  ];
}

// Kalau ada ?paid=1, ubah status JSON dan update DB
if ($autoPay) {
  $data[$id]['status'] = 'paid';
  $data[$id]['paid_at'] = time();

  // Simpan ke data.json
  $result = file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
  if ($result === false) {
    die('⚠️ Gagal menulis ke data.json — periksa permission folder');
  }

  // 🔹 UPDATE DATABASE ORDERS
  $dbFile = __DIR__ . '/../connection.php';
  if (file_exists($dbFile)) {
    require_once $dbFile;

    if (isset($data[$id]['order_id'])) {
      // Update berdasarkan order_id
      $order_id = intval($data[$id]['order_id']);
      $query = "UPDATE orders SET payment_status='paid', updated_at=NOW() WHERE id=$order_id";
      mysqli_query($connection, $query);

    } elseif (isset($data[$id]['order_code'])) {
      // Update berdasarkan order_code
      $order_code = mysqli_real_escape_string($connection, $data[$id]['order_code']);
      $query = "UPDATE orders SET payment_status='paid', updated_at=NOW() WHERE order_code='$order_code' LIMIT 1";
      mysqli_query($connection, $query);
    }
  } else {
    error_log("❌ connection.php tidak ditemukan di: $dbFile");
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">

<?php if ($autoPay): ?>
    <title>Pembayaran Sukses</title>
    <script>
        setTimeout(() => {
            window.location.href = "../service_list/index.php";
        }, 2000);
    </script>
<?php else: ?>
    <title>Bayar Nanti</title>
    <script>
        setTimeout(() => {
            window.location.href = "../service_list/index.php";
        }, 2000);
    </script>
<?php endif; ?>


  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: <?= $autoPay ? "linear-gradient(135deg, #00b09b, #96c93d)" : "linear-gradient(135deg, #b5b5b5, #e0e0e0)" ?>;
      color: white;
      text-align: center;
      flex-direction: column;
      animation: fadeIn 0.5s ease-in-out;
    }
    .icon {
      font-size: 80px;
      margin-bottom: 20px;
      animation: pop 0.5s ease;
      opacity: 0.9;
    }
    h1 { margin: 0; font-size: 28px; }
    p { font-size: 16px; margin-top: 8px; opacity: 0.9; }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pop {
      0% { transform: scale(0); }
      80% { transform: scale(1.2); }
      100% { transform: scale(1); }
    }
  </style>
</head>
<body>

  <?php if ($autoPay): ?>
      <div class="icon">✅</div>
      <h1>Pembayaran Berhasil!</h1>
      <p>Nomor VA: <?= htmlspecialchars($data[$id]['va']) ?></p>
      <p>Nominal: Rp <?= number_format($data[$id]['amount'], 0, ',', '.') ?></p>
      <p>Anda akan diarahkan ke halaman utama...</p>

  <?php else: ?>
      <div class="icon">⏳</div>
      <h1>Bayar Nanti Yahh</h1>
      <p>Ditunggu yaa bayar nyaa 😊</p>
      <p>Nomor VA: <?= htmlspecialchars($data[$id]['va']) ?></p>
      <p>Nominal: Rp <?= number_format($data[$id]['amount'], 0, ',', '.') ?></p>
      <p>Mengalihkan kembali...</p>
  <?php endif; ?>

</body>
</html>
