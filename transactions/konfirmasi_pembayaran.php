<?php
session_start();
require_once("config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['jenis'] !== 'petani') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Ambil semua transaksi yang menunggu konfirmasi dari petani ini
$query = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_petani = '$username' AND status = 'menunggu konfirmasi'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('navbar/nav.php'); ?>
<?php include('navbar/bot_nav.php'); ?>

<div class="container mt-4">
    <h2>Daftar Transaksi Menunggu Konfirmasi</h2>
    <?php if (mysqli_num_rows($query) > 0): ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Konsumen</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['produk']) ?></td>
                        <td><?= htmlspecialchars($row['id_konsumen']) ?></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td><?= $row['metode'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <form method="POST" action="controllers/konfirmasi_controller.php" onsubmit="return confirm('Konfirmasi pembayaran ini?')">
                                <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">Konfirmasi</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">Tidak ada transaksiiii  yang menunggu konfirmasi.</div>
    <?php endif; ?>
</div>

</body>
</html>
