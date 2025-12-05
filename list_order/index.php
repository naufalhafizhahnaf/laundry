<?php
require_once "../connection.php";
session_start();
include "../sidebar.php";

<<<<<<< HEAD
=======
// Cek login
>>>>>>> 3a4f77b45a997afa8c5bc6b42cfe0ff597efbc1e
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['user_id'];
$userQuery = mysqli_query($connection, "SELECT * FROM account WHERE id='$id'");
$user = mysqli_fetch_assoc($userQuery);

<<<<<<< HEAD
=======
// ❗ Cek Role: HANYA ADMIN/STAFF yang bisa melihat halaman ini
if (!in_array($user['role'], ['admin', 'staff'])) {
    header("Location: ../index.php");
    exit;
}

// Ambil semua order beserta nama customer (join account)
>>>>>>> 3a4f77b45a997afa8c5bc6b42cfe0ff597efbc1e
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
<<<<<<< HEAD
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
=======
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
>>>>>>> 3a4f77b45a997afa8c5bc6b42cfe0ff597efbc1e
<title>Order Management</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<<<<<<< HEAD
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet"/>

<style>
* {margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif;}
body {display:flex; background:#f5f7ff;}
.main {flex:1; padding:20px; overflow-y:auto;}

/* Header */
.top-bar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
    gap:10px;
    flex-wrap:wrap;
}
.top-bar h1 {font-size:24px;}

/* Container */
.content {
    background:white;
    border-radius:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    padding:15px;
}

/* HEADER CONTENT */
.header-h3 {
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:10px;
}

.button-add {
    display:flex;
    align-items:center;
}

.add-btn {
    background:#59df00ff;
    color:#fff;
    border:none;
    padding:10px 16px;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
    display:flex;
    align-items:center;
    gap:8px;
}
.add-btn:hover { background:#0ead00ff; }

/* TABLE RESPONSIVE */
.table-wrapper {
    width:100%;
    overflow-x:auto;
    margin-top:15px;
}

table {
    width:100%;
    min-width:1100px;
    border-collapse:collapse;
}

th, td {
    padding:12px 14px;
    text-align:left;
    font-size:14px;
    white-space:nowrap;
}

th {
    background:#007bff;
    color:white;
    position:sticky;
    top:0;
}

tr:nth-child(even){background:#f8f9ff;}
tr:hover{background:#eef4ff;}

/* Buttons */
.action-btn {
    border:none;
    border-radius:50%;
    padding:8px 10px;
    cursor:pointer;
}
.detail { background:#17a2b8; color:white; }
.edit { background:#ffc107; color:white; }
.delete { background:#dc3545; color:white; }

/* Status */
.status-select {
    padding:6px 10px; 
    border-radius:8px; 
    border:1px solid #ccc; 
    font-size:13px;
}
.pending { background:#fff3cd;color:#856404; }
.process { background:#cce5ff;color:#004085; }
.done { background:#d4edda;color:#155724; }
.taken { background:#6f42c1; color:white; }
.cancelled { background:#f8d7da;color:#721c24; }

/* MOBILE */
@media (max-width: 768px){
    .main{
        padding:15px 10px;
    }

    .top-bar h1{
        font-size:18px;
    }

    .add-btn span{
        display:none;
    }

    th, td {
        font-size:12px;
        padding:10px 8px;
    }

    .action-btn {
        padding:6px 8px;
    }

    .add-btn {
        padding:8px 10px;
    }
=======
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{display:flex;height:100vh;background:#f5f7ff;}
.main{flex:1;padding:30px 40px;overflow-y:auto;}
.top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;}
.top-bar h1{font-size:24px;font-weight:600;color:#222;}
.user-info .hi-text{color:#555;font-size:14px;}

.content{background:white;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.05);padding:20px;overflow-x:auto;}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:14px;text-align:left;font-size:14px;}
th{background:#007bff;color:white;position:sticky;top:0;}
tr:nth-child(even){background:#f8f9ff;}
tr:hover{background:#eef4ff;}

.action-btn{border:none;border-radius:8px;padding:8px 10px;cursor:pointer;font-size:14px;transition:0.3s;margin:2px 0;}
.detail{background:#17a2b8;color:white;}
.edit{background:#ffc107;color:white;}
.delete{background:#dc3545;color:white;}
.detail:hover{background:#138496;}
.edit:hover{background:#e0a800;}
.delete:hover{background:#c82333;}

.status-select{padding:6px 10px!important;border-radius:8px!important;border:1px solid #ccc!important;font-size:13px!important;font-weight:600;text-transform:capitalize;appearance:none;}

.status-select.pending{background:#fff3cd;color:#856404;border-color:#ffeeba;}
.status-select.process{background:#cce5ff;color:#004085;border-color:#b8daff;}
.status-select.done{background:#d4edda;color:#155724;border-color:#c3e6cb;}
.status-select.taken{background:#6f42c1;color:white;border-color:#5d38a0;}
.status-select.cancelled{background:#f8d7da;color:#721c24;border-color:#f5c6cb;}

.header-h3{display:flex;justify-content:space-between;align-items:center;}
.button-add{display:flex;align-items:center;gap:10px;}
.add-btn{background:#28a745;color:white;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;}
.add-btn:hover{background:#1e7e34;}

@media(max-width:768px){
.main{padding:20px 15px;}
.content{padding:10px;}
.header-h3{flex-direction:column;align-items:flex-start;}
.button-add{margin-top:10px;width:100%;justify-content:flex-start;}
table{display:block;overflow-x:auto;white-space:nowrap;}
th,td{padding:10px 8px;}
>>>>>>> 3a4f77b45a997afa8c5bc6b42cfe0ff597efbc1e
}
</style>
</head>

<body>

<<<<<<< HEAD
<?php if (isset($_SESSION['info'])) : ?>
<script>
iziToast.<?= $_SESSION['info']['status'] === 'success' ? 'success' : 'error' ?>({
  title: "<?= $_SESSION['info']['status'] === 'success' ? 'Sukses' : 'Gagal' ?>",
  message: "<?= $_SESSION['info']['message'] ?>",
  position: 'topCenter',
  timeout: 4000
=======
<?php if(isset($_SESSION['info'])): ?>
<script>
iziToast.<?= $_SESSION['info']['status']=='success'?'success':'error' ?>({
    title:"<?= $_SESSION['info']['status']=='success'?'Sukses':'Gagal' ?>",
    message:"<?= $_SESSION['info']['message'] ?>",
    position:"topCenter",
    timeout:5000
>>>>>>> 3a4f77b45a997afa8c5bc6b42cfe0ff597efbc1e
});
</script>
<?php unset($_SESSION['info']); endif; ?>

<div class="main">
<<<<<<< HEAD

<div class="top-bar">
  <h1>Order Management</h1>
  <div>Hi, <?= htmlspecialchars($user['nama']); ?></div>
</div>

<div class="content">

<div class="header-h3">
<h3>Daftar Order Laundry</h3>
<div class="button-add">
<button class="add-btn" onclick="window.location.href='insert.php'">
<i class="fa-solid fa-plus"></i><span>New Order</span>
</button>
</div>
</div>

<div class="table-wrapper">
<table>
<thead>
<tr>
<th>No</th>
<th>Kode</th>
<th>Customer</th>
<th>Layanan</th>
<th>Kg</th>
<th>Total</th>
<th>Status</th>
<th>Pembayaran</th>
<th>Metode</th>
<th>Diterima</th>
<th>Selesai</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php if (!empty($orders)): $no=1; foreach($orders as $o): ?>
<tr>
<td><?= $no++; ?></td>
<td><?= $o['order_code']; ?></td>
<td><?= $o['customer_name']; ?></td>
<td><?= ucfirst($o['service_type']); ?></td>
<td><?= number_format($o['weight'],2); ?></td>
<td><?= number_format($o['total_amount'],0,',','.'); ?></td>
<td>
<select class="status-select <?= $o['status']; ?>" data-id="<?= $o['id']; ?>" <?= $o['payment_status']=='unpaid'?'disabled':''; ?>>
<option value="pending" <?= $o['status']=='pending'?'selected':''; ?>>Pending</option>
<option value="process" <?= $o['status']=='process'?'selected':''; ?>>Process</option>
<option value="done" <?= $o['status']=='done'?'selected':''; ?>>Done</option>
<option value="taken" <?= $o['status']=='taken'?'selected':''; ?>>Taken</option>
<option value="cancelled" <?= $o['status']=='cancelled'?'selected':''; ?>>Cancelled</option>
</select>
</td>

<td><?= ucfirst($o['payment_status']); ?></td>
<td><?= ucfirst($o['payment_method']); ?></td>
<td><?= $o['received_date']; ?></td>
<td><?= $o['due_date']; ?></td>
<td>
<button class="action-btn detail" data-id="<?= $o['id']; ?>"><i class="fa fa-eye"></i></button>
<button class="action-btn edit" data-id="<?= $o['id']; ?>"><i class="fa fa-pen"></i></button>
<button class="action-btn delete" data-id="<?= $o['id']; ?>"><i class="fa fa-trash"></i></button>
</td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="12" style="text-align:center;color:#888">Belum ada order</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

<script>
document.querySelectorAll('.status-select').forEach(select=>{
    select.addEventListener('change',function(){
        const id=this.dataset.id;
        const status=this.value;
        fetch("update_status.php",{
            method:'POST',
            body:new URLSearchParams({id:id,status:status})
        }).then(res=>res.json()).then(res=>{
            iziToast[res.status]({title:res.status,message:res.message})
        })
    })
})
</script>

<script>
document.querySelectorAll('.delete').forEach(btn=>{
  btn.onclick=()=>{
    Swal.fire({ 
      title:'Hapus?',
      icon:'warning',
      showCancelButton:true,
      confirmButtonText:'Hapus'
    }).then(r=>{
      if(r.isConfirmed) window.location='delete.php?id='+btn.dataset.id;
    })
  }
})

document.querySelectorAll('.edit').forEach(btn=>{
  btn.onclick=()=>window.location='update.php?id='+btn.dataset.id;
})

document.querySelectorAll('.detail').forEach(btn=>{
  btn.onclick=()=>window.location='../details_order/index.php?id='+btn.dataset.id;
})
=======
<div class="top-bar">
<h1><i class="fa-solid fa-list-check"></i> Order Management</h1>
<div class="user-info">
<span class="hi-text">👋 Hi, <?= htmlspecialchars($user['nama']) ?> (<?= ucfirst($user['role']) ?>)</span>
</div>
</div>

<div class="content">
<div class="header-h3">
<h3>Daftar Order Laundry</h3>
<div class="button-add">
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
<th>Berat/Unit</th>
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

<?php if(!empty($orders)): $no=1; foreach($orders as $o): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($o['order_code']) ?></td>
<td><?= htmlspecialchars($o['customer_name']) ?></td>
<td><?= ucfirst($o['service_type']) ?></td>
<td><?= number_format($o['weight'],2) ?></td>
<td><?= number_format($o['total_amount'],0,',','.') ?></td>

<td>
<?php 
$isUnpaid = ($o['payment_status']=="unpaid");
$disabled = $isUnpaid ? "disabled" : "";
$dim = $isUnpaid ? "opacity:0.6;cursor:not-allowed;" : "";
?>
<select class="status-select <?= $o['status'] ?>" data-id="<?= $o['id'] ?>" <?= $disabled ?> style="<?= $dim ?>">
<option value="pending" <?= $o['status']=="pending"?"selected":"" ?>>Pending</option>
<option value="process" <?= $o['status']=="process"?"selected":"" ?>>Process</option>
<option value="done" <?= $o['status']=="done"?"selected":"" ?>>Done</option>
<option value="taken" <?= $o['status']=="taken"?"selected":"" ?>>Taken</option>
<option value="cancelled" <?= $o['status']=="cancelled"?"selected":"" ?>>Cancelled</option>
</select>
</td>

<td><?= ucfirst($o['payment_status']) ?></td>
<td><?= ucfirst($o['payment_method']) ?></td>
<td><?= date("d M Y H:i", strtotime($o['received_date'])) ?></td>
<td><?= date("d M Y H:i", strtotime($o['due_date'])) ?></td>

<td style="white-space:nowrap;">
<button class="action-btn detail" data-id="<?= $o['id'] ?>"><i class="fa-solid fa-eye"></i></button>
<button class="action-btn edit" data-id="<?= $o['id'] ?>"><i class="fa-solid fa-pen"></i></button>
<button class="action-btn delete" data-id="<?= $o['id'] ?>"><i class="fa-solid fa-trash"></i></button>
</td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="12" style="text-align:center;padding:20px;color:#777;">Belum ada order.</td></tr>
<?php endif; ?>

</tbody>
</table>
</div>
</div>

<script>
document.querySelectorAll(".status-select").forEach(select=>{
select.addEventListener("change",function(){
const id=this.dataset.id;
const status=this.value;
const data=new FormData();
data.append("id",id);
data.append("status",status);
fetch("update_status.php",{method:"POST",body:data})
.then(r=>r.json()).then(res=>{
iziToast[res.status]({
title:res.status=="success"?"Sukses":"Gagal",
message:res.message,position:"topCenter",timeout:3000
});
});
});
});
</script>

<script>
document.querySelectorAll(".delete").forEach(btn=>{
btn.addEventListener("click",function(){
const id=this.dataset.id;
Swal.fire({
title:"Hapus Order?",
text:"Order akan terhapus permanen",
icon:"warning",
showCancelButton:true,
confirmButtonText:"Ya, hapus!",
cancelButtonText:"Batal"
}).then(x=>{
if(x.isConfirmed) window.location.href="delete.php?id="+id;
});
});
});

document.querySelectorAll(".edit").forEach(btn=>{
btn.addEventListener("click",()=>window.location.href="update.php?id="+btn.dataset.id);
});

document.querySelectorAll(".detail").forEach(btn=>{
btn.addEventListener("click",()=>window.location.href="../details_order/index.php?id="+btn.dataset.id);
});
>>>>>>> 3a4f77b45a997afa8c5bc6b42cfe0ff597efbc1e
</script>

</body>
</html>