<?php
require_once("config/db.php");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Shopee Style</title>
    <link rel="stylesheet" href="scss/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="js/register.js" defer></script>
</head>
<body class="bg-container">
<div class="overlay"></div>>
    <div class="login-container">
    <h2 style="text-align:center; color:#4aff9e;">Register</h2>
        <form id="registerForm">
            <div class="form-group">
                <input type="text" id="usernameRegister" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" id="passwordRegister" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="text" id="alamatRegister" name="alamat" placeholder="Alamat" required>
            </div>
            <div class="form-group">
                <input type="text" id="noTelpRegister" name="no_telp" placeholder="No. Telepon" required>
            </div>
            <div class="form-group">
                <select id="roleRegister" name="role" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="petani">Petani</option>
                    <option value="pembeli">Pembeli</option>
                </select>
            </div>
            <button type="submit" class="login-btn">Register</button>
        </form>
        <p style="text-align:center; margin-top:10px;">Sudah punya akun? <a href="login.php" style="color:#4aff9e;">Login</a></p>
    </div>
</body>
</html>

