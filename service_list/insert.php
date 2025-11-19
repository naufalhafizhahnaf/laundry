<?php
require_once "../connection.php";
session_start();
include "../sidebar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['user_id'];
$query = mysqli_query($connection, "SELECT * FROM account WHERE id = '$id'");
$user = mysqli_fetch_assoc($query);

// Ambil parameter dari URL
$service = isset($_GET['service']) ? htmlspecialchars($_GET['service']) : '';
$price = isset($_GET['price']) ? floatval($_GET['price']) : 0;
$type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';

// Fungsi generate order code
function generateOrderCode($connection)
{
    $year = date('Y');
    $result = mysqli_query($connection, "SELECT MAX(id) AS last_id FROM orders");
    $data = mysqli_fetch_assoc($result);
    $next_id = ($data['last_id'] ?? 0) + 1;
    return sprintf("LDR-%s-%04d", $year, $next_id);
}

$message = '';
$success = false;
$payment_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_id = $_SESSION['user_id'];
    $service_type = mysqli_real_escape_string($connection, $_POST['service_type']);
    $weight = floatval($_POST['weight']);
    $price_per_kg = floatval($_POST['price']);
    $total_amount = $weight * $price_per_kg;
    $payment_method = mysqli_real_escape_string($connection, $_POST['payment_method']);
    $note = mysqli_real_escape_string($connection, $_POST['note']);

    $order_code = generateOrderCode($connection);
    $received_date = date('Y-m-d H:i:s');
    $due_date = date('Y-m-d H:i:s', strtotime('+2 days'));
    $status = 'pending';
    $payment_status = 'unpaid';

    $query = "
        INSERT INTO orders 
        (order_code, account_id, received_date, due_date, status, service_type, weight, total_amount, payment_status, payment_method, note, created_at, updated_at)
        VALUES 
        ('$order_code', '$account_id', '$received_date', '$due_date', '$status', '$service_type', '$weight', '$total_amount', '$payment_status', '$payment_method', '$note', NOW(), NOW())
    ";

    $insert = mysqli_query($connection, $query);

    if ($insert) {
        $success = true;
        $message = "Order berhasil dibuat dengan kode: $order_code";
        $order_id = mysqli_insert_id($connection);

        // Jika QRIS atau Transfer → generate VA
        if (in_array($payment_method, ['qris', 'transfer'])) {
            $paymentDir = __DIR__ . '/../payment';
            $file = $paymentDir . '/data.json';

            if (!is_dir($paymentDir)) {
                mkdir($paymentDir, 0777, true);
            }

            if (!file_exists($file)) {
                file_put_contents($file, '{}');
            }

            $data = json_decode(file_get_contents($file), true) ?: [];

            // Generate VA
            $va_id = uniqid();
            $va_number = 'VA-' . strtoupper(substr(md5($va_id), 0, 6));

            $data[$va_id] = [
                'order_id' => $order_id,
                'va' => $va_number,
                'amount' => $total_amount,
                'status' => 'pending',
                'created_at' => time()
            ];

            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

            // SIMPAN VA_ID KE DATABASE
            $va_safe = mysqli_real_escape_string($connection, $va_id);
            mysqli_query($connection, "UPDATE orders SET va_id='$va_safe' WHERE id=$order_id LIMIT 1");

            // Link pembayaran
            $base = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            $payment_link = "$base/../payment/pay.php?id=$va_id&order=$order_id";
        }

// 🔥 SWEETALERT LOADING — PREMIUM FULL OVERRIDE
if ($payment_link) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        
        Swal.fire({
            title: '<div class=\"swal-title-custom\">Sedang Memproses Pesanan...</div>',
            html: `
                <div class=\"swal-subtext-custom\">
                    Mohon tunggu sebentar ya 😊<br>
                    Sistem sedang menyiapkan halaman pembayaran.
                </div>
                <div class=\"loader-premium\"></div>
            `,
            background: '#f1f2f4 !important',
            width: '360px',
            padding: '30px 20px',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'popup-premium'
            }
        });

        // Inject custom CSS FULL OVERRIDE
        const style = document.createElement('style');
style.innerHTML = `
    .popup-premium {
        background: rgba(255,255,255,0.65) !important;
        backdrop-filter: blur(14px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(14px) saturate(180%) !important;
        border-radius: 22px !important;
        border: 1px solid rgba(255,255,255,0.35) !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12) !important;
        animation: fadeSlide 0.35s ease !important;
        padding: 28px 24px !important;
    }

    .swal-title-custom {
        font-size: 21px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 8px;
        letter-spacing: -0.3px;
    }

    .swal-subtext-custom {
        font-size: 14px;
        color: #4b5563;
        margin-bottom: 20px;
        line-height: 1.55;
    }

    /* New Premium Loader — iOS Style */
    .loader-premium {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        margin: 0 auto;
        border: 4px solid rgba(0,0,0,0.08);
        border-top-color: #2563eb;
        animation: spin 0.8s cubic-bezier(0.4, 0.0, 0.2, 1) infinite;
    }

    @keyframes spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes fadeSlide {
        0% { opacity: 0; transform: translateY(14px) scale(0.97); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* MOBILE DESIGN — Ultra Clean */
@media (max-width: 480px) {
    .popup-premium {
        width: 96% !important;        /* hampir full width */
        padding: 34px 26px !important; /* diperbesar */
        border-radius: 22px !important;
        transform: scale(1.04);        /* sedikit membesar */
    }
    .swal-title-custom {
        font-size: 21px !important;
    }
    .swal-subtext-custom {
        font-size: 14.5px !important;
        margin-bottom: 22px !important;
    }
    .loader-premium {
        width: 52px;
        height: 52px;
        border-width: 5px;            /* loader lebih bold */
    }
}

`;

        document.head.appendChild(style);

        setTimeout(() => {
            window.location.href = '$payment_link';
        }, 3500);
    </script>";
    exit;
}



    } else {
        $message = 'Terjadi kesalahan query: ' . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Buat Order Laundry</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

body {
    display: flex;
    min-height: 100vh;
    background: #f5f7ff;
}

/* MAIN WRAPPER */
.main {
    flex: 1;
    padding: 40px;
    display: flex;
    justify-content: center;
}

.main-inner {
    width: 100%;
    max-width: 850px;
}

/* PAGE TITLE */
h1 {
    color: #007bff;
    font-weight: 700;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 28px;
}

/* FORM CONTAINER */
form {
    background: #fff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

/* GRID INPUT */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

label {
    font-weight: 500;
    margin-bottom: 5px;
    display: block;
}

input, select, textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #dce1f0;
    border-radius: 10px;
    font-size: 15px;
    margin-bottom: 8px;
    transition: 0.2s;
}

input:focus, select:focus, textarea:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 3px rgba(0,123,255,0.3);
}

input[readonly] {
    background: #f3f4f8;
    color: #666;
}

/* FULL WIDTH */
.full {
    grid-column: span 2;
}

/* BUTTON */
button {
    margin-top: 20px;
    padding: 14px 25px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: 0.3s;
}

button:hover {
    background: #005ecb;
}

/* SUCCESS / ERROR BOX */
.message {
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.message.success {
    background: #d4f4dd;
    color: #207a36;
}

.message.error {
    background: #fce4e4;
    color: #8a1f1f;
}

/* RESPONSIVE — MOBILE */
@media (max-width: 900px) {
    .main {
        padding: 25px;
    }
}

@media (max-width: 768px) {

    .main {
        padding: 20px;
    }

    h1 {
        font-size: 22px;
        margin-bottom: 10px;
    }

    .form-grid {
        grid-template-columns: 1fr;
        gap: 0px;
    }

    .full {
        grid-column: span 1;
    }

    form {
        padding: 20px;
    }

    input, select, textarea {
        padding: 11px;
        font-size: 14px;
    }

    button {
        width: 100%;
        margin-top: 10px;
    }
}

/* EXTRA MOBILE < 480px */
@media (max-width: 480px) {
    h1 {
        font-size: 19px;
    }

    input, select, textarea {
        padding: 10px;
    }
}

</style>
</head>
<body>
  <div class="main">
        <div class="main-inner">
    <h1><i class="fa-solid fa-basket-shopping"></i> Buat Order Laundry</h1>

    <?php if($message): ?>
      <div class="message <?= $success ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message); ?>
        <?php if($payment_link): ?>
          <br><br>
          <a href="<?= $payment_link ?>" target="_blank">
            <button style="background:#28a745;">💳 Bayar Sekarang</button>
          </a>
          <br><br>
          <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?= urlencode($payment_link) ?>" alt="QR">
        <?php endif; ?>
      </div>
    <?php endif; ?>

<form method="POST" action="">
    
    <div class="form-grid">

        <!-- ROW 1 -->
        <div>
            <label>Nama Customer</label>
            <input type="text" value="<?= htmlspecialchars($user['nama']); ?>" readonly>
        </div>

        <div>
            <label>Service</label>
            <input type="text" name="service_type" value="<?= htmlspecialchars($service ?: 'tidak diketahui'); ?>" readonly>
        </div>

        <!-- ROW 2 -->
        <?php if($type): ?>
        <div>
            <label>Tipe Laundry</label>
            <input type="text" value="<?= htmlspecialchars($type); ?>" readonly>
        </div>
        <?php else: ?>
        <div></div>
        <?php endif; ?>

        <div>
            <label>Harga per Kg (Rp)</label>
            <input type="number" name="price" id="price" value="<?= $price; ?>" readonly>
        </div>

        <!-- ROW 3 -->
        <div>
            <label>Berat Cucian (Kg)</label>
            <input type="number" step="0.1" name="weight" id="weight" min="0.1" required>
        </div>

        <div>
            <label>Total Harga (Rp)</label>
            <input type="number" name="total_amount" id="total_amount" readonly>
        </div>

        <!-- ROW 4 -->
        <div>
            <label>Metode Pembayaran</label>
            <select name="payment_method" required>
                <option value="">-- Pilih Metode --</option>
                <option value="cash">Cash</option>
                <option value="qris">QRIS</option>
                <option value="transfer">Transfer</option>
            </select>
        </div>

        <div></div>

        <!-- ROW 5 (FULL WIDTH) -->
        <div class="full">
            <label>Catatan</label>
            <textarea name="note" rows="3" placeholder="Tulis catatan tambahan (opsional)..."></textarea>
        </div>

    </div>

    <button type="submit">
        <i class="fa-solid fa-check"></i> Buat Order
    </button>

</form>
        </div>
  </div>

  <script>
    document.getElementById('weight').addEventListener('input', function() {
      const price = parseFloat(document.getElementById('price').value) || 0;
      const weight = parseFloat(this.value) || 0;
      document.getElementById('total_amount').value = (price * weight).toFixed(0);
    });
  </script>
</body>
</html>
