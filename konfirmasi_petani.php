<?php
session_start();
require_once("config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['jenis'] !== 'pembeli') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Tangani upload bukti transfer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_transaksi']) && isset($_FILES['bukti'])) {
    $id_transaksi = intval($_POST['id_transaksi']);
    $nama_file = time() . "_" . basename($_FILES['bukti']['name']);
    $target_dir = "uploads/bukti/";
    $target_file = $target_dir . $nama_file;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

    if (in_array($ext, $allowed) && move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
        mysqli_query($conn, "UPDATE transaksi SET bukti_transfer = '$nama_file', status = 'menunggu verifikasi' 
            WHERE id_transaksi = $id_transaksi AND id_konsumen = '$username'");
        echo "<script>alert('Bukti transfer berhasil diunggah!'); window.location.href='konfirmasi_pembeli.php';</script>";
        exit;
    } else {
        echo "<script>alert('Upload gagal! Format file harus JPG, PNG, atau PDF.');</script>";
    }
}

// Filter status jika dipilih
$status_filter = $_GET['status'] ?? '';
$filter_sql = $status_filter ? "AND status = '" . mysqli_real_escape_string($conn, $status_filter) . "'" : "";

$transaksi = mysqli_query($conn, "SELECT * FROM transaksi 
    WHERE id_konsumen = '$username' $filter_sql 
    ORDER BY id_transaksi DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran Saya</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="scss/style.css">
    <style>
        .card-konfirmasi { margin-bottom: 20px; }
        .bukti-preview { max-height: 150px; margin-top: 10px; display: block; }
        .filter-container { margin: 20px 0; }
    </style>
</head>
<body>

<?php include('navbar/nav.php'); ?>
<?php include('navbar/bot_nav.php'); ?>

<div class="container mt-4">
    <h2>Konfirmasi Pembayaran Saya</h2>

    <!-- Filter status -->
    <form method="GET" class="filter-container">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="status" class="form-label">Filter Status:</label>
            </div>
            <div class="col-auto">
                <select name="status" id="status" class="form-select">
                    <option value="">-- Semua --</option>
                    <option value="menunggu konfirmasi" <?= $status_filter == 'menunggu konfirmasi' ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
                    <option value="menunggu verifikasi" <?= $status_filter == 'menunggu verifikasi' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                    <option value="disetujui" <?= $status_filter == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="ditolak" <?= $status_filter == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Terapkan</button>
            </div>
        </div>
    </form>

    <!-- Daftar transaksi -->
    <?php if (mysqli_num_rows($transaksi) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($transaksi)): ?>
            <div class="card card-konfirmasi">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['produk']) ?></h5>
                    <p class="card-text">
                        <strong>Jumlah:</strong> <?= $row['jumlah'] ?><br>
                        <strong>Total:</strong> Rp <?= number_format($row['total_harga'], 0, ',', '.') ?><br>
                        <strong>Metode Pembayaran:</strong> <?= $row['metode'] ?><br>
                        <strong>Status:</strong>
                        <span class="badge 
                            <?= $row['status'] == 'disetujui' ? 'bg-success' : 
                                ($row['status'] == 'ditolak' ? 'bg-danger' : 
                                ($row['status'] == 'menunggu verifikasi' ? 'bg-info text-dark' : 'bg-warning text-dark')) ?>">
                            <?= $row['status'] ?>
                        </span>
                    </p>

                    <!-- Bukti transfer -->
                    <?php if (!empty($row['bukti_transfer'])): ?>
                        <p><strong>Bukti Transfer:</strong></p>
                        <a href="uploads/bukti/<?= htmlspecialchars($row['bukti_transfer']) ?>" target="_blank">
                            <img src="uploads/bukti/<?= htmlspecialchars($row['bukti_transfer']) ?>" class="bukti-preview" alt="Bukti Transfer">
                        </a>
                    <?php elseif ($row['metode'] !== 'COD' && $row['status'] == 'menunggu konfirmasi'): ?>
                        <form method="POST" enctype="multipart/form-data" class="mt-3">
                            <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Upload Bukti Transfer</label>
                                <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted">Transaksi COD tidak memerlukan bukti transfer.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">Tidak ada transaksi filter saat ini.</div>
    <?php endif; ?>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
