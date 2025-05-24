<?php
session_start();
require_once("../config/db.php");
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];
$jenis = $_SESSION['jenis'];

$query = $jenis === 'petani'
    ? "SELECT * FROM transaksi WHERE id_petani = '$username' AND status != 'menunggu konfirmasi'"
    : "SELECT * FROM transaksi WHERE id_konsumen = '$username'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2>🕓 Riwayat Transaksi</h2>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['produk']) ?></td>
                        <td><?= $row['jumlah'] ?> kg</td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td><span class="badge bg-success"><?= $row['status'] ?></span></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">Belum ada transaksi.</div>
    <?php endif; ?>
</div>
</body>
</html>
