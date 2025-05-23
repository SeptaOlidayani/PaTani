<?php 
require_once("config/db.php");
session_start();
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DB Patani</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="scss/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="js/login.js" defer></script>
</head>
<body class="bg-container">
    <div class="overlay"></div>
    <div class="login-container">
        <h2 style="text-align:center; color:#4aff9e;">Login</h2>
        <div class="role-selector">
            <button id="petaniBtn" class="active" onclick="selectRole('petani')">Petani</button>
            <button id="pembeliBtn" onclick="selectRole('pembeli')">Pembeli</button>
        </div>
        <form action="controllers/login_controller.php" method="POST">
            <input type="hidden" name="role" id="role" value="petani">
            <div class="form-group">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>

        <p style="text-align:center; margin-top:10px;">
            Belum punya akun? <a href="register.php" style="color:#4aff9e;">Register di sini</a>
        </p>
    </div>
    <div class="login-quote">
        <p >"Akses Mudah Panen Melimpah,Masa Depan Petani Dimulai Disini."</p>
    </div>
    <footer class="login-footer"><p>&copy; 2025 PaTani. All rights reserved.</p>
    </footer>
</body>
</html>
