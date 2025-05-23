<?php
session_start();
require_once("config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Ambil transaksi yang masih "menunggu konfirmasi"
$query = mysqli_query($conn, "SELECT * FROM transaksi 
                              WHERE id_konsumen = '$username' AND status = 'menunggu konfirmasi' 
                              ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran - PaTani</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0fff4;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 2rem;
        }
        .card {
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .card-header {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-success:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<?php include("navbar/nav.php"); ?>
<?php include("navbar/bot_nav.php"); ?>

<div class="container">
    <h2 class="mb-4 text-center text-success">Konfirmasi Pembayaran</h2>

    <?php if (mysqli_num_rows($query) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($query)): ?>
            <div class="card">
                <div class="card-header">
                    Transaksi: <?= htmlspecialchars($row['produk']) ?> (Rp <?= number_format($row['total_harga'], 0, ',', '.') ?>)
                </div>
                <div class="card-body">
                    <p><strong>Jumlah:</strong> <?= $row['jumlah'] ?> item</p>
                    <p><strong>Metode:</strong> <?= htmlspecialchars($row['metode']) ?></p>
                    <form action="controllers/proses_konfirmasi.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="bukti" class="form-label">Upload Bukti Pembayaran (Gambar)</label>
                            <input type="file" class="form-control" name="bukti" accept="image/*" required>
                        </div>
                        <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi'] ?>">
                        <button type="submit" class="btn btn-success">Kirim Konfirmasi</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">Tidak ada transaksi yang perlu dikonfirmasi saat ini.</div>
    <?php endif; ?>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
