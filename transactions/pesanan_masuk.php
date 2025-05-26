<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['jenis'] !== 'petani') {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];

// Ambil pesanan masuk untuk petani ini
$query = "
    SELECT 
        t.id_transaksi, t.jumlah, t.total_harga, t.status, 
        t.kurir, t.metode, t.id_konsumen, 
        p.nama_produk,
        u.username AS nama_pembeli
    FROM transaksi t
    JOIN produk p ON t.id_produk = p.id_produk
    JOIN user u ON t.id_konsumen = u.username
    WHERE t.id_petani = '$username' AND t.status IN ('menunggu konfirmasi', 'dikonfirmasi')
    ORDER BY t.id_transaksi DESC
";
$result = mysqli_query($conn, $query);
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
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Pembeli</th>
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
                        <td><?= htmlspecialchars($row['metode'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['nama_pembeli']) ?></td>
                        <td><span class="badge bg-warning text-dark"><?= ucwords($row['status']) ?></span></td>
                        <td>
                            <form action="konfirmasi_pesanan.php" method="POST" style="display: inline-block;" onsubmit="return confirm('Lanjutkan aksi?');">
                                <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi'] ?>">
                                <?php if ($row['status'] === 'menunggu konfirmasi'): ?>
                                    <button type="submit" name="aksi" value="konfirmasi" class="btn btn-success btn-sm">Konfirmasi</button>
                                <?php elseif ($row['status'] === 'dikonfirmasi'): ?>
                                    <button type="submit" name="aksi" value="dikirim" class="btn btn-primary btn-sm">Kirim</button>
                                <?php endif; ?>
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
