<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}
include('../controllers/market_controller.php');?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Marketplace</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../scss/style.css">
    <link rel="stylesheet" href="../scss/market.css">
</head>
<body>
<?php 
include('../navbar/nav.php');
include('../navbar/bot_nav.php');
?>
<div class="container my-4">
    <div class="judul-section">Produk Unggulan</div>
    <div class="produk-grid">
        <?php while ($row = mysqli_fetch_assoc($unggulan)) {?>
            <div class="produk-item">
            <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
<strong><?= htmlspecialchars($row['nama_produk']) ?></strong><br>
Rp <?= number_format($row['harga'], 0, ',', '.') ?>

<!-- Tombol aksi hanya jika pembeli -->
<?php if ($_SESSION['jenis'] === 'pembeli'): ?>
    <div class="d-flex gap-2 mt-2">
        <a href="../transactions/purchase.php?id_produk=<?= $row['id_produk']; ?>" class="btn btn-success btn-sm w-100">Beli</a>
        <form action="../add_to_cart.php" method="GET" class="w-100">
            <input type="hidden" name="id_produk" value="<?= $row['id_produk']; ?>">
            <button type="submit" class="btn btn-warning btn-sm w-100">🛒</button>
        </form>
    </div>
<?php endif; ?>

            </div> 
           
        <?php } ?>
    </div>
</div>
<div class="container my-4">
    <div class="judul-section">Produk Terlaris</div>
    <div class="produk-grid">
        <?php while ($row = mysqli_fetch_assoc($terlaris)) {?>
            <div class="produk-item">
            <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
<strong><?= htmlspecialchars($row['nama_produk']) ?></strong><br>
Rp <?= number_format($row['harga'], 0, ',', '.') ?>

<!-- Tombol aksi hanya jika pembeli -->
<?php if ($_SESSION['jenis'] === 'pembeli'): ?>
    <div class="d-flex gap-2 mt-2">
        <a href="../transactions/purchase.php?id_produk=<?= $row['id_produk']; ?>" class="btn btn-success btn-sm w-100">Beli</a>
        <form action="../add_to_cart.php" method="GET" class="w-100">
            <input type="hidden" name="id_produk" value="<?= $row['id_produk']; ?>">
            <button type="submit" class="btn btn-warning btn-sm w-100">🛒</button>
        </form>
    </div>
<?php endif; ?>

            </div> 

        <?php } ?>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>

