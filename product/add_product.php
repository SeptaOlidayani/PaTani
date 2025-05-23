<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['jenis'] != 'petani') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../scss/add.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Tambah Produk Baru</h2>
    <?php if (isset($_GET['error'])) echo "<div class='alert alert-danger'>" . htmlspecialchars($_GET['error']) . "</div>"; ?>
    <form method="POST" enctype="multipart/form-data" action="../controllers/add_controller.php">
        <div class="mb-3">
            <label class="form-label">Nama Produk</label>
            <input type="text" class="form-control" name="nama_produk" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Harga (Rp)</label>
            <input type="number" class="form-control" name="harga" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <input type="text" class="form-control" name="kategori" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stok</label>
            <input type="number" class="form-control" name="stok" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Foto Produk</label>
            <input type="file" class="form-control" name="foto" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="../index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
