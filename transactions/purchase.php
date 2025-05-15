<?php
session_start();
require_once("../config/db.php");
if (!isset($_SESSION['username']) || !isset($_GET['id_produk'])) {
    header("Location: ../login.php");
    exit;
}
require_once("../config/db.php");
$id_produk = $_GET['id_produk'];
$result = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = $id_produk");
$produk = mysqli_fetch_assoc($result);

if (!$produk){
    echo "Produk tidak ditemukan";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Marketplace</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../scss/style.css">
    <link rel="stylesheet" href="../scss/market.css">
</head>
<body>
<div class="container mt-4">
    <h2>Form Pembelian Produk</h2>
    <div class="card">
        <div class="card-body">
        <form action="../controllers/purchase_controller.php" method="POST">
            <input type="hidden" name="id_petani" value="<?= $produk['id_petani'] ?>">
            <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
            <input type="hidden" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>">
            <input type="hidden" name="harga" values="<?= $produk['harga'] ?>">
            <div class="mb-3">
                <label>Nama Produk</label>
                <input type="text" class="form comtrol" value="<?= htmlspecialchars($produk['nama_produk']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label>Harga Satuan</label>
                <input type="text" class="form comtrol" value="Rp <?= number_format($produk['harga'], 3); ?>" disabled>
            </div>
            <div class="mb-3">
                <label>Jumlah</label>
                <input type="number" class="form comtrol" name="jumlah" id="jumlah" min="1" max="<?= $produk['stok']; ?>" value="1" required>
            </div>
            <div class="mb-3">
                <label>Total Harga</label>
                <input type="text" class="form-control" id="total_harga" value="Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                <select class="form-select" name="metode_pembayaran" id="metode_pembayaran" required>
                    <option value="">-- Pilih Metode Pembayaran --</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="E-Wallet">E-Wallet (OVO, DANA, dsb)</option>
                    <option value="COD">Cash on Delivery (COD)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Beli Sekarang</button>
            <a href="../index.php" class="btn btn-secondary">Batal</a>
        </form>
        <script>
            const jumlahInput = document.getElementById('jumlah');
            const totalHargaInput = document.getElementById('total_harga');
            const harga = parseint(document.getElementById('harga').value);
            jumlahInput.addEventListener('input', function() {
                const jumlah = parseint(this.value) || 0;
                const totalHarga = jumlah * harga;
                totalHargaInput.value = totalHarga;
            });
        </script>
        </div>
    </div>
</div>
</body>
</html>