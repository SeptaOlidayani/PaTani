<?php
session_start();
require_once("../config/db.php");
require_once("../controllers/update_controller.php");

if (!isset($_SESSION['username']) || $_SESSION['jenis'] !== 'petani') {
    header("Location: ../login.php");
    exit;
}
if (!isset($_GET['id'])) {
    header("Location: list_product.php");
    exit;
}
$id_produk = $_GET['id'];
$username = $_SESSION['username'];
$produk = getProdukById($id_produk, $username);
if (!$produk) {
    echo "Produk tidak ditemukan.";
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    $foto_lama = $produk['foto'];
    $foto_baru = uploadFoto($foto_lama);
    updateProduk($id_produk, $username, $nama_produk, $harga, $stok, $deskripsi, $foto_baru);
    header("Location: list_product.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Produk</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Nama Produk</label>
            <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" value="<?= $produk['harga'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" value="<?= $produk['stok'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" required><?= htmlspecialchars($produk['deskripsi']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Foto Saat Ini</label><br>
            <img src="../uploads/<?= htmlspecialchars($produk['foto']) ?>" alt="Foto Produk" style="max-height: 150px;">
        </div>
        <div class="mb-3">
            <label>Ganti Foto</label>
            <input type="file" name="foto" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="list_product.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>