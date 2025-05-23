<?php
session_start();
require_once("config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$query = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_konsumen = '$username' ORDER BY id_transaksi DESC LIMIT 1");
$transaksi = mysqli_fetch_assoc($query);

if (!$transaksi) {
    echo "<script>alert('Tidak ada transaksi untuk dikonfirmasi.'); window.location.href = 'index.php';</script>";
    exit;
}

// Handle upload bukti transfer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti'])) {
    $id_transaksi = $transaksi['id_transaksi'];
    $nama_file = time() . "_" . basename($_FILES['bukti']['name']);
    $target_dir = "uploads/bukti/";
    $target_file = $target_dir . $nama_file;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

    if (in_array($file_type, $allowed) && move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
        mysqli_query($conn, "UPDATE transaksi SET bukti_transfer = '$nama_file', status = 'menunggu verifikasi' WHERE id_transaksi = $id_transaksi");
        echo "<script>alert('Bukti transfer berhasil diunggah!'); window.location.href='konfirmasi.php';</script>";
        exit;
    } else {
        echo "<script>alert('Upload gagal! Format file harus JPG, PNG, atau PDF.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="scss/style.css">
    <style>
        .konfirmasi-card {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .konfirmasi-card h2 {
            color: #4caf50;
            text-align: center;
            margin-bottom: 25px;
        }
        .btn-home {
            display: block;
            margin: 20px auto 0;
        }
    </style>
</head>
<body>

<?php include('navbar/nav.php'); ?>
<?php include('navbar/bot_nav.php'); ?>

<div class="konfirmasi-card">
    <h2>Konfirmasi Pembayaran</h2>
    <p><strong>Produk:</strong> <?= htmlspecialchars($transaksi['produk']) ?></p>
    <p><strong>Jumlah:</strong> <?= $transaksi['jumlah'] ?></p>
    <p><strong>Total Harga:</strong> Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></p>
    <p><strong>Metode Pembayaran:</strong> <?= $transaksi['metode'] ?? 'COD' ?></p>
    <p><strong>Status:</strong> <span class="badge bg-info"><?= $transaksi['status'] ?></span></p>

    <?php if ($transaksi['metode'] !== 'COD' && empty($transaksi['bukti_transfer'])): ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="bukti" class="form-label">Upload Bukti Transfer (JPG, PNG, PDF)</label>
                <input type="file" class="form-control" name="bukti" id="bukti" required>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Bukti</button>
        </form>
    <?php elseif (!empty($transaksi['bukti_transfer'])): ?>
        <p><strong>Bukti Terkirim:</strong><br>
            <a href="uploads/bukti/<?= htmlspecialchars($transaksi['bukti_transfer']) ?>" target="_blank" class="btn btn-outline-success mt-2">Lihat Bukti</a>
        </p>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary btn-home">Kembali ke Beranda</a>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
