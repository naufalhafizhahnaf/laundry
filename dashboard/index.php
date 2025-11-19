<?php
include '../sidebar.php';
require_once "../connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['user_id'];
$query = mysqli_query($connection, "SELECT * FROM account WHERE id='$id'");
$user = mysqli_fetch_assoc($query);

// Tentukan link tujuan tombol berdasarkan role
$role = $user['role'] ?? 'user'; // default user
$redirectLink = ($role === 'admin') ? 'static.php' : 'order.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>User Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet" />
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Roboto Mono", monospace;
}

body {
  min-height: 100vh;
  position: relative;
}

/* Background */
.bg-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: url('../Assets/Home.jpg') no-repeat center center fixed;
  background-size: cover;
  z-index: -2;
}

.overlay-color {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255,255,255,0.6);
  z-index: -1;
}

/* Sidebar + Main */
.main {
  display: flex;
  flex-direction: column;
  margin-left: 60px;
  padding: 30px;
  position: relative;
  z-index: 1;
}

/* Top bar */
.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.top-bar h1 {
  font-size: 24px;
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
  font-size: 10px;
}

/* Content full-screen di depan gambar */
.content-wrapper {
  position: absolute;
  top: 200px;
  left: 50px; /* sesuaikan dengan sidebar */
  width: calc(100%);
  height: 100vh;
  display: flex;
  justify-content: left;
  align-items: left;
  text-align: left;
  z-index: 2;
}

.content h1 {
  font-size: 100px;
  color: #ffffffff;
  margin-bottom: 90px;
  line-height: 1;
  text-shadow: 5px 5px 15px rgba(0, 0, 0, 0.4); /* bayangan lembut */
  font-weight: 800;
}

.content p {
  font-size: 30px;
  margin-bottom: 30px;
  font-weight: 800;
  color: #ffffffff;
  line-height: 1;
  text-shadow: 5px 5px 15px rgba(0, 0, 0, 0.4); /* bayangan lembut */
}

.btn-start {
  padding: 20px 25px;
  font-size: 1.1rem;
  font-weight: 600;
  color: #fff;
  background: linear-gradient(135deg, #8e2de2, #ff6fd8);
  box-shadow:5px 5px 15px rgba(0, 0, 0, 0.4); 
  border: none;
  border-radius: 50px;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-start:hover {
  background: linear-gradient(135deg, #9b4dff, #ff85e4);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(142, 45, 226, 0.4);
}


/* Responsive */
@media (max-width: 768px) {
  .main {
    margin-left: 0;
    padding: 20px;
  }

  .content-wrapper {
    left: 0;
    width: 100%;
  }

  .content h1 {
    font-size: 2rem;
  }

  .content p {
    font-size: 1rem;
  }

  .btn-start {
    padding: 10px 25px;
    font-size: 1rem;
  }
}
</style>
</head>
<body>
<div class="bg-overlay">
    
</div>
<div class="overlay-color">
    <div class="main">

  <div class="content-wrapper">
    <div class="content">
      <h1>Selamat Datang di <br>Laundryin</h1>
      <p>Click Button Below to Start the Program</p>
    </div>
  </div>
</div>
</div>


</body>
</html>
