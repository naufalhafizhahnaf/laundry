<?php
require_once "../connection.php";
session_start();
include "../sidebar.php";

// Cek login & role admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data user login
$id = $_SESSION['user_id'];
$userQuery = mysqli_query($connection, "SELECT * FROM account WHERE id='$id'");
$user = mysqli_fetch_assoc($userQuery);

// Ambil semua order beserta nama customer (join ke tabel account)
$query = "
    SELECT o.*, a.nama AS customer_name 
    FROM orders o 
    JOIN account a ON o.account_id = a.id 
    ORDER BY o.created_at DESC
";
$result = mysqli_query($connection, $query);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
    body { display:flex; height:100vh; background:#f5f7ff; }
    .main { flex:1; padding:30px 40px; overflow-y:auto; }
    .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
    .top-bar h1 { font-size:24px; font-weight:600; color:#222; }


    .content {
      background:white; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.05);
      padding:20px; overflow-x:auto;
    }
    table { width:100%; border-collapse:collapse; margin-top:15px; }
    th, td { padding:14px; text-align:left; font-size:14px; }
    th { background:#007bff; color:white; position:sticky; top:0; }
    tr:nth-child(even){background:#f8f9ff;}
    tr:hover{background:#eef4ff;}
    .action-btn {
      border:none; border-radius:8px; padding:8px 10px;
      cursor:pointer; font-size:14px; transition:0.3s;
    }
    .edit { background:#ffc107; color:white; }
    .delete { background:#dc3545; color:white; }
    .edit:hover { background:#e0a800; }
    .delete:hover { background:#c82333; }

    .status {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
    display: inline-block;
    }
.status-select.pending {
    background: #fff3cd;
    color: #856404;
}
.status-select.process {
    background: #cce5ff;
    color: #004085;
}
.status-select.done {
    background: #d4edda;
    color: #155724;
}
.status-select.taken {
    background:#6f42c1; color:white;
}
.status-select.cancelled {
    background: #f8d7da;
    color: #721c24;
}


    .swal-popup { border-radius:15px !important; box-shadow:0 4px 12px rgba(0,0,0,0.05); padding:25px !important; }
    .swal-confirm-btn {
      background:#dc3545 !important; color:white !important;
      border-radius:12px !important; padding:8px 20px !important; font-weight:600;
    }
    .swal-cancel-btn {
      background:#6c757d !important; color:white !important;
      border-radius:12px !important; padding:8px 20px !important; font-weight:600;
    }
    .header-h3{
        display: flex;
  justify-content: space-between;
  align-items: center; /* biar sejajar vertikal juga */
    }
    .button-add{
        display: flex;
  justify-content: space-between;
  align-items: center; /* biar sejajar vertikal juga */
  gap: 20px;
    }
    .add-btn {
      background: #59df00ff; color:white; border:none; padding:8px 16px;
      border-radius:8px; font-weight:600; cursor:pointer;
      display:flex; align-items:center; gap:8px; transition:0.3s;
    }
    .add-btn:hover { background: #0ead00ff; }
    .edit-btn {
      background: #ffd20aff; color:white; border:none; padding:8px 16px;
      border-radius:8px; font-weight:600; cursor:pointer;
      display:flex; align-items:center; gap:8px; transition:0.3s;
    }
    .edit-btn:hover { background: #c09300ff; }
  </style>
</head>
<body>
<!-- Notifikasi -->
<?php if (isset($_SESSION['info'])) : ?>
  <script>
    iziToast.<?= $_SESSION['info']['status'] === 'success' ? 'success' : 'error' ?>({
      title: "<?= $_SESSION['info']['status'] === 'success' ? 'Sukses' : 'Gagal' ?>",
      message: "<?= $_SESSION['info']['message'] ?>",
      position: 'topCenter',
      timeout: 5000
    });
  </script>
  <?php unset($_SESSION['info']); ?>
<?php endif; ?>
<div class="main">
  <div class="top-bar">
    <h1>Order Management</h1>
    <div class="user-info">
      <span class="hi-text">Hi, <?= htmlspecialchars($user['nama']); ?></span>

    </div>
  </div>

  <div class="content">
    <div class="header-h3">
        <h3>Daftar Order Laundry</h3>
        <div class= "button-add">
          <button class="add-btn" onclick="window.location.href='insert.php'">
        <i class="fa-solid fa-plus"></i> New Order
      </button>
  </div>
  </div>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Kode Order</th>
          <th>Customer</th>
          <th>Layanan</th>
          <th>Berat (Kg)</th>
          <th>Total (Rp)</th>
          <th>Status</th>
          <th>Pembayaran</th>
          <th>Metode</th>
          <th>Diterima</th>
          <th>Estimasi Selesai</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($orders)): $no=1; foreach($orders as $o): ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($o['order_code']); ?></td>
          <td><?= htmlspecialchars($o['customer_name']); ?></td>
          <td><?= ucfirst($o['service_type']); ?></td>
          <td><?= number_format($o['weight'], 2); ?></td>
          <td><?= number_format($o['total_amount'], 0, ',', '.'); ?></td>
<td>
  <?php 
    $isUnpaid = ($o['payment_status'] === 'unpaid');
    $disabled = $isUnpaid ? 'disabled' : '';
    $styleDim = $isUnpaid ? 'opacity:0.5; cursor:not-allowed;' : '';
  ?>

  <select 
    class="status-select" 
    data-id="<?= $o['id']; ?>" 
    <?= $disabled; ?>
    style="
      padding:6px 10px; 
      border-radius:8px; 
      border:1px solid #ccc; 
      font-size:13px;
      <?= $styleDim; ?>
    "
  >
    <option value="pending"   <?= $o['status']=='pending'?'selected':''; ?>>Pending</option>
    <option value="process"   <?= $o['status']=='process'?'selected':''; ?>>Process</option>
    <option value="done"      <?= $o['status']=='done'?'selected':''; ?>>Done</option>
    <option value="taken"     <?= $o['status']=='taken'?'selected':''; ?>>Taken</option>
    <option value="cancelled" <?= $o['status']=='cancelled'?'selected':''; ?>>Cancelled</option>
  </select>
</td>


          <td><?= ucfirst($o['payment_status']); ?></td>
          <td><?= ucfirst($o['payment_method']); ?></td>
          <td><?= htmlspecialchars($o['received_date']); ?></td>
          <td><?= htmlspecialchars($o['due_date']); ?></td>
          <td>
            <button class="action-btn detail" data-id="<?= $o['id']; ?>"><i class="fa-solid fa-eye"></i></button>
            <button class="action-btn edit" data-id="<?= $o['id']; ?>"><i class="fa-solid fa-pen"></i></button>
            <button class="action-btn delete" data-id="<?= $o['id']; ?>"><i class="fa-solid fa-trash"></i></button>
          </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="12" style="text-align:center;color:#888;">Belum ada order.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

<script>
// Fungsi set warna dropdown berdasarkan value
function applyStatusColor(select) {
    const value = select.value;

    // Hapus semua class lama
    select.classList.remove("pending", "process", "done", "taken", "cancelled");

    // Tambahkan class sesuai value
    select.classList.add(value);
}

// Set warna saat halaman pertama kali dibuka
document.querySelectorAll(".status-select").forEach(select => {
    applyStatusColor(select);
});

// Update warna ketika user mengganti status
document.querySelectorAll(".status-select").forEach(select => {
    select.addEventListener("change", function () {
        applyStatusColor(this);

        const id = this.dataset.id;
        const status = this.value;

        const formData = new FormData();
        formData.append("id", id);
        formData.append("status", status);

        fetch("update_status.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            iziToast[res.status]({
                title: res.status === "success" ? "Sukses" : "Gagal",
                message: res.message,
                position: "topCenter",
                timeout: 3000
            });
        })
        .catch(() => {
            iziToast.error({
                title: "Error",
                message: "Terjadi kesalahan pada server",
                position: "topCenter"
            });
        });
    });
});
</script>


<script>
document.querySelectorAll('.delete').forEach(btn => {
  btn.addEventListener('click', function() {
    const id = this.dataset.id;
    Swal.fire({
      title: '<span style="font-family:Poppins;font-weight:600;">Hapus Order?</span>',
      html: '<span style="font-family:Poppins;">Order ini akan dihapus permanen.</span>',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
      background: '#ffffff',
      color: '#333',
      iconColor: '#dc3545',
      customClass: {
        confirmButton: 'swal-confirm-btn',
        cancelButton: 'swal-cancel-btn',
        popup: 'swal-popup'
      },
      buttonsStyling: false
    }).then(result => {
      if (result.isConfirmed) {
        window.location.href = 'delete.php?id=' + id;
      }
    });
  });
});

document.querySelectorAll('.edit').forEach(btn => {
  btn.addEventListener('click', function() {
    const id = this.dataset.id;
    window.location.href = 'update.php?id=' + id;
  });
});
</script>
<script>
document.querySelectorAll('.detail').forEach(btn => {
  btn.addEventListener('click', function() {
    const id = this.dataset.id;
    window.location.href = '../details_order/index.php?id=' + id;
  });
});
</script>

</body>
</html>
