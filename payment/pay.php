<?php
// pay.php — Halaman utama pembayaran (generate QR ke pay_done.php?id=xxx&paid=1)

$file = __DIR__ . '/data.json';
if (!file_exists($file)) file_put_contents($file, '{}');
$data = json_decode(file_get_contents($file), true);

function saveData($data) {
  global $file;
  file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// === PARAMETER INPUT ===
$id = $_GET['id'] ?? uniqid();
$amount = isset($_GET['amount']) ? (int)$_GET['amount'] : 10000;

// === BUAT TRANSAKSI BARU KALAU BELUM ADA ===
if (!isset($data[$id])) {
  $va = 'VA-' . strtoupper(substr(md5($id), 0, 6));
  $data[$id] = [
    'va' => $va,
    'amount' => $amount,
    'status' => 'pending',
    'created_at' => time()
  ];
  saveData($data);
}
$trx = $data[$id];

// === GENERATE QR CODE ===
// === GENERATE QR CODE ===
// isi QR = link ke pay_done.php?id=XXX&paid=1 → buka = auto paid
$qrLink = "http://10.223.124.105/payment/pay_done.php?id=$id&paid=1";
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($qrLink) . "&size=250x250";
$qrBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($qrUrl));
file_get_contents($qrUrl);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pembayaran Laundry</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #007bff, #00c6ff);
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  margin: 0;
  padding: 15px; /* supaya mobile tidak terpotong */
}

.card {
  background: #fff;
  border-radius: 20px;
  padding: 30px;
  width: 360px;
  max-width: 100%;            /* penting untuk mobile */
  text-align: center;
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  animation: fadeIn 0.6s ease;
}

h2 { 
  color: #333; 
  font-weight: 600; 
  margin-bottom: 10px; 
}

.va { 
  font-size: 18px; 
  font-weight: 700; 
  color: #007bff; 
  letter-spacing: 1px; 
  margin-bottom: 10px; 
}

.amount { 
  font-size: 22px; 
  color: #28a745; 
  font-weight: 700; 
  margin-bottom: 25px; 
}

img { 
  margin: 15px 0; 
  border-radius: 10px; 
  background: #fff; 
  padding: 10px; 
  box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
  width: 200px; 
  height: 200px; 
  object-fit: contain; 
}

.status { 
  margin-top: 15px; 
  font-weight: 500; 
  color: #666; 
}

.paid { 
  color: #28a745; 
  font-weight: 700; 
}

button {
  background: #007bff;
  color: #fff;
  border: none;
  padding: 12px 25px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  transition: all .3s;
  width: 100%;               /* biar full di mobile */
  max-width: 250px;          /* tetap bagus di desktop */
}

button:hover { 
  background: #0056b3; 
  transform: scale(1.05); 
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ========================= */
/*   RESPONSIVE BREAKPOINTS  */
/* ========================= */

/* 📱 Mobile < 480px */
@media (max-width: 480px) {
  .card {
    padding: 22px 18px;
    border-radius: 18px;
    width: 100%;
  }

  h2 { font-size: 20px; }
  .va { font-size: 16px; }
  .amount { font-size: 20px; }
  
  img {
    width: 160px;
    height: 160px;
  }

  button {
    padding: 12px 18px;
    font-size: 14px;
  }
}

/* 📱 Small Tablet (480px–768px) */
@media (max-width: 768px) {
  .card {
    width: 420px;
    padding: 28px;
  }
}

/* 💻 Large Screen > 1200px — biar tidak kejauhan */
@media (min-width: 1200px) {
  body {
    padding-top: 40px;
  }
}

  </style>
</head>
<body>
  <div class="card">
    <h2>Pembayaran Laundry</h2>
    <div class="va">Nomor VA: <?= htmlspecialchars($trx['va']) ?></div>
    <div class="amount">Rp <?= number_format($trx['amount'], 0, ',', '.') ?></div>
    <img src="<?= $qrBase64 ?>" alt="QR Code">
    <div class="status">
      Status:
      <?php if ($trx['status'] === 'paid'): ?>
        <span class="paid">✅ Sudah Dibayar</span>
      <?php else: ?>
        <span>Belum Dibayar</span>
      <?php endif; ?>
    </div>

<?php if ($trx['status'] !== 'paid'): ?>
  <form method="POST" action="pay_done.php?id=<?= $id ?>&paid=1">
    <button type="submit">Bayar Manual</button>
  </form>
<?php endif; ?>

<a href="pay_done.php?id=<?= $id ?>&paid=0" style="
    display:block;
    margin-top:15px;
    padding:10px 20px;
    background:#6c757d;
    color:white;
    border-radius:10px;
    font-weight:600;
    text-decoration:none;
">
    Bayar Nanti
</a>

</body>
</html>
