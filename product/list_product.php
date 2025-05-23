<?php
session_start();
require_once("../config/db.php");
if (!isset($_SESSION['username']) || $_SESSION['jenis'] !== 'petani') {
    header("Location: ../login.php");
    exit;
}
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id_produk, nama_produk, harga, kategori, stok, deskripsi, tanggal FROM produk WHERE id_petani = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$data_produk = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Produk Saya</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../scss/style.css">
</head>
<body>
<?php 
include('../navbar/nav.php');
include('../navbar/bot_nav.php');
?>
<div class="container mt-5">
    <h2 class="mb-4 text-dark">Produk Saya</h2>
    <a href="add_product.php" class="btn btn-success mb-3">➕ Tambah Produk</a>
    <?php if (count($data_produk) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Deskripsi</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data_produk as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['kategori'] ?? '') ?></td>
                    <td><?= $row['stok'] ?> Kg</td>
                    <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <td>
                        <a href="update_product.php?id=<?= $row['id_produk'] ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="../controllers/delete_controller.php?id=<?= $row['id_produk'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">Belum ada produk yang ditambahkan.</div>
<?php endif; ?>

</div>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
