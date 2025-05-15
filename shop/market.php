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
            <img src="../uploads/<?=($row['foto']) ?>" alt="<?=($row['nama_produk']) ?>">
            <strong><?=($row['nama_produk']) ?></strong><br>
            Rp<?= number_format($row['harga'],2)?>
            </div> 
           
        <?php } ?>
    </div>
</div>
<div class="container my-4">
    <div class="judul-section">Produk Terlaris</div>
    <div class="produk-grid">
        <?php while ($row = mysqli_fetch_assoc($terlaris)) {?>
            <div class="produk-item">
            <img src="../uploads/<?=($row['foto']) ?>" alt="<?=($row['nama_produk']) ?>">
            <strong><?=($row['total_terjual']) ?></strong><br>
            Rp<?= number_format($row['harga'],2)?>
            </div> 

        <?php } ?>
    </div>
</div>
<script src="../js/script.js"></script>
</body>
</html>

