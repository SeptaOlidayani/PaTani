<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require_once("config/db.php");
$username = $_SESSION['username'];
$query = mysqli_query($conn, "SELECT jenis FROM user WHERE username = '$username'");
$data = mysqli_fetch_assoc($query);
$jenis_user = $data['jenis'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PakTani</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="scss/style.css">
</head>
<body>
<?php 
include('navbar/nav.php');
include('navbar/bot_nav.php')
?>

<div class="container my-4">
    <div class="row g-4">
        <?php if ($jenis_user == 'petani') {
            $result = mysqli_query($conn, "SELECT * FROM produk WHERE id_petani = '$username' ORDER BY tanggal DESC");
        } else {
            $result = mysqli_query($conn, "SELECT * FROM produk ORDER BY tanggal DESC");
        }
        while ( $produk = mysqli_fetch_assoc($result))
        {
        ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="uploads/<?= htmlspecialchars($produk['foto']); ?>" class="card-img-top" alt="<?= htmlspecialchars($produk['nama_produk']); ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($produk['nama_produk']); ?></h5>
                        <p class="card-text">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></p>
                    <?php if ($jenis_user == 'pembeli'): ?>
                        <a href="transactions/purchase.php?id_produk=<?= $produk['id_produk']; ?>" class="btn btn-success">Beli</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>
