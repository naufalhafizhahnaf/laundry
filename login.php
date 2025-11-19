<?php
require_once "connection.php";
session_start();

$message_login = "";
$message_register = "";

// LOGIN HANDLER
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    // login sekarang pakai tabel account
    $sql = "SELECT * FROM account WHERE username = '$username'";
    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // redirect sesuai role (optional)
            if($row['role'] === 'admin') {
                header("Location: dashboard/index.php");
            } else {
                header("Location: dashboard/index.php"); // halaman user
            }
            exit;
        } else {
            $message_login = "❌ Password salah!";
        }
    } else {
        $message_login = "⚠️ Username tidak ditemukan!";
    }
}

// REGISTER HANDLER
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $nama = mysqli_real_escape_string($connection, $_POST['nama']);
    $alamat = mysqli_real_escape_string($connection, $_POST['alamat']);
    $no_hp = mysqli_real_escape_string($connection, $_POST['no_hp']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user'; // default role

    // cek username
    $check = mysqli_query($connection, "SELECT * FROM account WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $message_register = "⚠️ Username sudah digunakan!";
    } else {
        $query = "INSERT INTO account (username, password, nama, role, alamat, no_hp, created_at)
                  VALUES ('$username', '$password', '$nama', '$role', '$alamat', '$no_hp', NOW())";
        if (mysqli_query($connection, $query)) {
            $message_register = "✅ Akun berhasil dibuat! Silakan login.";
        } else {
            $message_register = "❌ Gagal membuat akun: " . mysqli_error($connection);
        }
    }
}

// menentukan container aktif
$isRegisterActive = isset($_POST['action']) && $_POST['action'] === 'register';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Login & Register</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
* {margin:0;padding:0;box-sizing:border-box;font-family:"Poppins", sans-serif;}
body {display:flex;justify-content:center;align-items:center;height:100vh;background:#f5f6fa;padding:20px;}
.container {position:relative;width:850px;height:500px;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.1);transition:all 0.4s ease;}
.form-container {position:absolute;top:0;height:100%;width:50%;display:flex;align-items:center;justify-content:center;transition:all 0.6s ease-in-out;padding:20px;}
form {width:100%;max-width:320px;display:flex;flex-direction:column;}
form h2 {font-size:24px;margin-bottom:5px;color:#333;font-weight:600;}
form p {font-size:13px;margin-bottom:20px;color:#777;}
.input-field {position:relative;display:flex;align-items:center;margin-bottom:15px;border:1px solid #ddd;border-radius:10px;padding:8px 12px;background:#fafafa;}
.input-field i.fa-eye {position:absolute;right:12px;color:#999;cursor:pointer;transition:color 0.2s;}
.input-field i.fa-eye:hover {color:#007bff;}
.input-field i {color:#999;margin-right:10px;width:20px;text-align:center;}
.input-field input {width:100%;border:none;outline:none;background:none;font-size:12px;color:#333;}
button {margin-top:15px;padding:10px;border:none;background:#007bff;color:#fff;border-radius:20px;cursor:pointer;transition:0.3s;font-size:15px;}
button:hover {background:#0056b3;}
.login-container {left:0;z-index:2;}
.register-container {right:0;opacity:0;transform:translateX(100%);z-index:1;}
.overlay-container {position:absolute;top:0;left:50%;width:50%;height:100%;overflow:hidden;transition:transform 0.6s ease-in-out;z-index:5;}
.overlay {background:#007bff;color:#fff;position:relative;left:-100%;height:100%;width:200%;display:flex;transition:transform 0.6s ease-in-out;}
.overlay-panel {display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;width:50%;padding:0 40px;transition:0.6s;}
.overlay-panel h2 {font-size:24px;margin-bottom:10px;}
.overlay-panel p {font-size:14px;margin-bottom:20px;}
.overlay-panel button {background:#fff;color:#007bff;border:none;border-radius:20px;padding:10px 25px;font-weight:600;cursor:pointer;transition:0.3s;}
.overlay-panel button:hover {background:#e5e5e5;}
.container.active .login-container {transform:translateX(-100%);opacity:0;}
.container.active .register-container {transform:translateX(0);opacity:1;z-index:5;}
.container.active .overlay-container {transform:translateX(-100%);}
.container.active .overlay {transform:translateX(50%);}
.popup-message {position:static;background:#fff;text-align:center;justify-content:center;align-items:center;color:#333;padding:5px 8px;border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,0.15);font-weight:500;z-index:9999;display:flex;gap:4px;margin-bottom:20px;opacity:0;transform:translateY(-10px);animation:popupSlide 2.5s ease forwards;}
@keyframes popupSlide {0% {opacity:0;transform:translateY(-10px);}15% {opacity:1;transform:translateY(0);}85% {opacity:1;transform:translateY(0);}100% {opacity:0;transform:translateY(-10px);}}
@media (max-width:900px){.container{width:100%;max-width:400px;height:550px;border-radius:15px;}.overlay-container{position:absolute;width:0%;height:100%;left:0;top:0;z-index:0;}.overlay{width:200%;left:-100%;transform:translateX(0);}.form-container{width:100%;position:absolute;z-index:2;transition:all 0.6s ease;background:#fff;}.login-container,.register-container{width:100%;transition:all 0.6s ease;}.container.active .login-container{transform:translateX(-100%);opacity:0;}.container.active .register-container{transform:translateX(0%);opacity:1;z-index:5;}.container.active .overlay{transform:translateX(100%);}form h2{font-size:20px;}button{font-size:14px;}}
</style>
</head>
<body>
<div class="container <?= $isRegisterActive ? 'active' : '' ?>" id="container">
    <!-- LOGIN -->
    <div class="form-container login-container">
        <form method="POST">
            <h2>LaundryIn</h2>
            <p>Login to your account</p>
            <?php if ($message_login): ?>
                <div class="popup-message"><span><?= htmlspecialchars($message_login) ?></span></div>
            <?php endif; ?>
            <div class="input-field">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="username" placeholder="Username" required />
            </div>
            <div class="input-field">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="loginPassword" name="password" placeholder="Password" required />
                <i class="fa-solid fa-eye" id="toggleLoginPassword"></i>
            </div>
            <button type="submit" name="action" value="login">Login</button>
            <p style="margin-top:10px;font-size:13px;">
                Don't have an account? <a href="#" id="mobileSignUp" style="color:#007bff;text-decoration:none;">Sign Up</a>
            </p>
        </form>
    </div>

    <!-- REGISTER -->
    <div class="form-container register-container">
        <form method="POST">
            <h2>Get Started</h2>
            <?php if ($message_register): ?>
                <div class="popup-message"><span><?= htmlspecialchars($message_register) ?></span></div>
            <?php endif; ?>
            <p>Already have an account? <a href="#" id="mobileLogIn" style="color:#007bff;text-decoration:none;">Log In</a></p>
            <div class="input-field"><i class="fa-solid fa-id-card"></i><input type="text" name="nama" placeholder="Full Name" required></div>
            <div class="input-field"><i class="fa-solid fa-user"></i><input type="text" name="username" placeholder="Username" required></div>
            <div class="input-field"><i class="fa-solid fa-house"></i><input type="text" name="alamat" placeholder="Alamat" required></div>
            <div class="input-field"><i class="fa-solid fa-phone"></i><input type="text" name="no_hp" placeholder="No HP" required></div>
            <div class="input-field"><i class="fa-solid fa-lock"></i><input type="password" id="registerPassword" name="password" placeholder="*Password length (10-32)" required><i class="fa-solid fa-eye" id="toggleRegisterPassword"></i></div>
            <button type="submit" name="action" value="register">Sign Up</button>
        </form>
    </div>

    <!-- OVERLAY -->
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h2>Already have an account?</h2>
                <p>Click below to log in</p>
                <button id="overlayLogInBtn">Log In</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h2>New here?</h2>
                <p>Create your account and join us!</p>
                <button id="signUpBtn">Sign Up</button>
            </div>
        </div>
    </div>
</div>

<script>
const container = document.getElementById("container");
const signUpBtn = document.getElementById("signUpBtn");
const overlayLogInBtn = document.getElementById("overlayLogInBtn");
const mobileSignUp = document.getElementById("mobileSignUp");
const mobileLogIn = document.getElementById("mobileLogIn");

// switch
signUpBtn?.addEventListener("click",()=>container.classList.add("active"));
overlayLogInBtn?.addEventListener("click",()=>container.classList.remove("active"));
mobileSignUp?.addEventListener("click",(e)=>{e.preventDefault();container.classList.add("active");});
mobileLogIn?.addEventListener("click",(e)=>{e.preventDefault();container.classList.remove("active");});

// toggle password
function togglePass(inputId,toggleId){
    const input=document.getElementById(inputId);
    const toggle=document.getElementById(toggleId);
    toggle.addEventListener("click",()=>{input.type=input.type==="password"?"text":"password";toggle.classList.toggle("fa-eye-slash");});
}
togglePass("loginPassword","toggleLoginPassword");
togglePass("registerPassword","toggleRegisterPassword");

// auto remove popup
document.querySelectorAll('.popup-message').forEach(msg=>{
    setTimeout(()=>{msg.style.transition='opacity 0.5s, transform 0.5s';msg.style.opacity='0';msg.style.transform='translateY(-20px)';setTimeout(()=>msg.remove(),500);},2000);
});
</script>
</body>
</html>
