<?php if (isset($_GET['keranjang']) && $_GET['keranjang'] == 'berhasil'): ?>
    <div class="alert alert-success text-center">
        Produk berhasil ditambahkan ke keranjang!
    </div>
<?php endif; ?>
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
include('navbar/bot_nav.php');
?>

<div class="container my-4">

<?php
// Tambahkan bagian ini untuk menampilkan daftar nama produk petani
if ($jenis_user == 'petani') {
    $listNama = [];
    $resultList = mysqli_query($conn, "SELECT nama_produk FROM produk WHERE id_petani = '$username'");
    while ($row = mysqli_fetch_assoc($resultList)) {
        $listNama[] = htmlspecialchars($row['nama_produk']);
    }
    if (!empty($listNama)) {
        echo '<div class="alert alert-info"><strong>Produk Saya:</strong> ' . implode(', ', $listNama) . '</div>';
    } else {
        echo '<div class="alert alert-warning">Anda belum menambahkan produk apa pun.</div>';
    }
}
?>

    <div class="row g-4">
        <?php 
        if ($jenis_user == 'petani') {
            $result = mysqli_query($conn, "SELECT * FROM produk WHERE id_petani = '$username' ORDER BY tanggal DESC");
        } else {
            if (isset($_GET['cari']) && !empty($_GET['cari'])) {
                $cari = mysqli_real_escape_string($conn, $_GET['cari']);
                $result = mysqli_query($conn, "SELECT * FROM produk WHERE nama_produk LIKE '%$cari%' ORDER BY tanggal DESC");
            } else {
                $result = mysqli_query($conn, "SELECT * FROM produk ORDER BY tanggal DESC");
            }
        }

        while ($produk = mysqli_fetch_assoc($result)) {
        ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <!-- Foto Produk -->
                    <img src="uploads/<?= htmlspecialchars($produk['foto']); ?>" class="card-img-top" alt="<?= htmlspecialchars($produk['nama_produk']); ?>" style="height: 200px; object-fit: cover;">

                    <!-- Nama Produk di Bawah Gambar -->
                    <div class="text-center fw-bold py-2" style="background-color: #f8f9fa; color: #212529;">
                        <?= htmlspecialchars($produk['nama_produk']); ?>
                    </div>

                    <div class="card-body">
                        <p class="card-text">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></p>
                        <?php if ($jenis_user == 'pembeli'): ?>
    <div class="d-flex gap-2">
        <a href="transactions/purchase.php?id_produk=<?= $produk['id_produk']; ?>" class="btn btn-success btn-sm flex-fill">Beli</a>
        <form action="add_to_cart.php" method="GET" class="flex-fill">
            <input type="hidden" name="id_produk" value="<?= $produk['id_produk']; ?>">
            <button type="submit" class="btn btn-warning btn-sm w-100">🛒</button>
        </form>
    </div>
<?php endif; ?>

                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>
