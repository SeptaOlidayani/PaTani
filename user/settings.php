<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

function getUserByUsername($conn, $username)
{
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$username = $_SESSION['username'];
$user = getUserByUsername($conn, $username);
?>
<!-- lalu HTML view seperti biasa -->


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Akun</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2>⚙️ Pengaturan Akun</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Perubahan berhasil disimpan.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form method="post" action="../controllers/user_controller.php" enctype="multipart/form-data">
        <div class="mb-3 text-center">
            <img src="<?= $user['foto'] ?? '../assets/default-profile.png' ?>" class="rounded-circle" width="100" alt="Foto Profil">
            <input type="file" name="foto" class="form-control mt-2" accept="image/*">
        </div>

        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" required><?= htmlspecialchars($user['alamat']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>No. Telepon</label>
            <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($user['no_telp']) ?>" required>
        </div>

        <button type="submit" name="update_profile" class="btn btn-success">💾 Simpan Perubahan</button>
    </form>

    <hr>

    <h4 class="mt-4">🔒 Ganti Password</h4>
    <form method="post" action="../controllers/user_controller.php">
        <div class="mb-3">
            <label>Password Lama</label>
            <input type="password" name="old_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password Baru</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" name="change_password" class="btn btn-warning">🔁 Ganti Password</button>
    </form>

    <hr>
    <a href="../logout.php" class="btn btn-danger">🚪 Logout</a>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
