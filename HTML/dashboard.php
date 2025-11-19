<?php
include '../sidebar.php';

require_once "../connection.php"; // pastikan path ke file koneksi benar

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data admin
$id = $_SESSION['admin_id'];
$query = mysqli_query($connection, "SELECT * FROM admin WHERE id='$id'");
$admin = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      background: #f5f7ff;
      display: flex;
      height: 100vh;
      color: #333;
    }

    /* Sidebar */
    .sidebar {
      width: 70px;
      background: #ffffff;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
      gap: 25px;
    }

    .sidebar i {
      font-size: 22px;
      color: #007bff;
      cursor: pointer;
      transition: 0.3s;
    }

    .sidebar i:hover {
      color: #0056b3;
      transform: scale(1.1);
    }

    /* Main content */
    .main {
      flex: 1;
      padding: 25px 30px;
      display: flex;
      flex-direction: column;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }

    .top-bar h1 {
      font-size: 22px;
      font-weight: 600;
      color: #222;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .hi-text {
      font-weight: 500;
      color: #444;
      font-size: 20px;
    }

    .logout-btn {
      background: #007bff;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 20px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .logout-btn:hover {
      background: #0056b3;
    }

    /* Content layout */
    .content {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
      height: 100%;
    }

    .left {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .categories {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 15px;
    }

    .cat-card {
      background: white;
      border-radius: 15px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: 0.3s;
    }

    .cat-card:hover {
      transform: translateY(-5px);
    }

    .cat-card i {
      font-size: 26px;
      color: #007bff;
      margin-bottom: 10px;
    }

    .recent {
      background: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .recent h3 {
      margin-bottom: 15px;
      color: #007bff;
    }

    .order-item {
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 10px;
      transition: 0.2s;
      cursor: pointer;
    }

    .order-item:hover {
      background: #f0f7ff;
    }

    .order-item .icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: #e7f0ff;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #007bff;
      margin-right: 15px;
    }

    .order-item span {
      color: #555;
      font-weight: 500;
    }

    .right {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .balance,
    .report {
      background: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .balance h3 {
      color: #007bff;
      margin-bottom: 10px;
    }

    .balance p {
      font-size: 24px;
      font-weight: 600;
    }

    .balance small {
      color: #666;
    }

    .report h3 {
      color: #007bff;
      margin-bottom: 10px;
    }

    .report .row {
      display: flex;
      justify-content: space-between;
      margin: 5px 0;
      font-size: 14px;
      color: #444;
    }

    .report .row span {
      color: #007bff;
      font-weight: 600;
    }
  </style>
</head>
<body>

  <div class="main">
    <div class="top-bar">
      <h1>Dashboard</h1>

      <div class="user-info">
        <span class="hi-text">Hi, <?= htmlspecialchars($admin['nama']); ?></span>
      </div>
    </div>

    <div class="content">
      <div class="left">
        <div class="categories">
          <div class="cat-card"><i class="fa-solid fa-shirt"></i><h4>Clothes</h4></div>
          <div class="cat-card"><i class="fa-solid fa-user"></i><h4>Pants</h4></div>
          <div class="cat-card"><i class="fa-solid fa-vest"></i><h4>Jackets</h4></div>
          <div class="cat-card"><i class="fa-solid fa-grid-2"></i><h4>See More</h4></div>
        </div>

        <div class="recent">
          <h3>Recent Order</h3>
          <div class="order-item">
            <div class="icon"><i class="fa-solid fa-box"></i></div>
            <span>Order #00123 - Processing</span>
          </div>
          <div class="order-item">
            <div class="icon"><i class="fa-solid fa-box"></i></div>
            <span>Order #00122 - Delivered</span>
          </div>
          <div class="order-item">
            <div class="icon"><i class="fa-solid fa-box"></i></div>
            <span>Order #00121 - Pending</span>
          </div>
        </div>
      </div>

      <div class="right">
        <div class="balance">
          <h3>BALANCE</h3>
          <p>Rp.000.000.000</p>
          <small>Up this week ↑ 7%</small>
        </div>

        <div class="report">
          <h3>REPORT</h3>
          <div class="row"><p>Total Order</p><span>0</span></div>
          <div class="row"><p>Received</p><span>0</span></div>
          <div class="row"><p>On Progress</p><span>0</span></div>
          <div class="row"><p>Completed</p><span>0</span></div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
