<?php
session_start();
require_once("../controllers/user_controller.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'];
$user = getUserByUsername($conn, $username);
$_SESSION['jenis'] = $user['jenis'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../scss/profile.css">
    <link rel="stylesheet" href="../scss/style.css">
</head>
<body>

<?php include('../navbar/nav.php'); ?>
<?php include('../navbar/bot_nav.php'); ?>

<div class="profile-container">
    <h2>Profil Saya</h2>
    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">Profil berhasil diperbarui.</div>
    <?php endif; ?>
    
    <form method="post" action="../controllers/user_controller.php" class="profile-form" enctype="multipart/form-data">   
        <div class="text-center mb-3">
            <img src="<?= $user['foto'] ?? '../assets/default-profile.png' ?>" id="previewFoto" class="rounded-circle" width="120" height="120" alt="Foto Profil">
            <input type="file" name="foto" id="fotoInput" class="form-control mt-2" accept="image/*">
        </div>
        <div class="form-group">
            <label>Username:</label>
            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled>
        </div>
        <div class="form-group">
            <label>Jenis:</label>
            <input type="text" value="<?= htmlspecialchars($user['jenis']) ?>" disabled>
        </div>
        <div class="form-group">
            <label>Alamat:</label>
            <textarea name="alamat" required><?= htmlspecialchars($user['alamat']) ?></textarea>
        </div>
        <div class="form-group">
            <label>No. Telepon:</label>
            <input type="text" name="no_telp" value="<?= htmlspecialchars($user['no_telp']) ?>" required>
        </div>
        <button type="submit" name="update_profile" class="btn-save">Simpan Perubahan</button>
    </form>
    <?php if ($user['jenis'] === 'petani'): ?>
    <div class="my-product-link">
        <a href="../product/list_product.php" class="btn-produk-saya">Lihat Produk Saya</a>
    </div>
    <?php endif; ?>
</div>
<script src="../js/profile.js"></script>
<script src="../js/script.js"></script>
</body>
</html>
