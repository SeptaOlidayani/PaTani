<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['jenis'] !== 'petani') {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];
$result = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_petani = '$username' AND status = 'menunggu konfirmasi' ORDER BY id_transaksi DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Masuk</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2>📥 Pesanan Masuk</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Pembeli</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['produk']) ?></td>
                        <td><?= $row['jumlah'] ?> kg</td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($row['metode']) ?></td>
                        <td><?= htmlspecialchars($row['id_konsumen']) ?></td>
                        <td>
                            <form action="konfirmasi_pesanan.php" method="POST" onsubmit="return confirm('Konfirmasi pesanan ini?');">
                                <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">Konfirmasi</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">Belum ada pesanan masuk.</div>
    <?php endif; ?>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
