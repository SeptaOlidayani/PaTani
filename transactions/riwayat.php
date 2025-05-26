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
    ? "SELECT t.*, p.nama_produk 
       FROM transaksi t 
       JOIN produk p ON t.id_produk = p.id_produk 
       WHERE t.id_petani = '$username' AND t.status != 'menunggu konfirmasi'
       ORDER BY t.id_transaksi DESC"
    : "SELECT t.*, p.nama_produk 
       FROM transaksi t 
       JOIN produk p ON t.id_produk = p.id_produk 
       WHERE t.id_konsumen = '$username'
       ORDER BY t.id_transaksi DESC";

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
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                        <td><?= $row['jumlah'] ?> kg</td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td><span class="badge bg-<?= $row['status'] === 'selesai' ? 'secondary' : 'warning text-dark' ?>">
                            <?= ucwords($row['status']) ?>
                        </span></td>
                        <td>
                            <?php if ($jenis === 'pembeli' && $row['status'] === 'dikirim'): ?>
                                <form action="konfirmasi_selesai.php" method="POST" onsubmit="return confirm('Konfirmasi pesanan telah selesai?');">
                                    <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Selesai</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
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
