<?php
session_start();
require_once("../config/db.php");
if (!isset($_SESSION['username']) || !isset($_GET['id_produk'])) {
    header("Location: ../login.php");
    exit;
}
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
    <title>Form Pembelian Produk - PaTani</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../scss/style.css">
    <link rel="stylesheet" href="../scss/market.css">
    <style>
        .product-img {
            max-width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .jumlah-wrapper {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .jumlah-wrapper input {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4 text-success text-center">Form Pembelian Produk</h2>
    <div class="card shadow">
        <div class="card-body">

            <!-- Foto Produk -->
            <img src="../uploads/<?= htmlspecialchars($produk['foto']) ?>" alt="Foto Produk" class="product-img">

            <form action="../controllers/purchase_controller.php" method="POST">
                <input type="hidden" name="id_petani" value="<?= $produk['id_petani'] ?>">
                <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
                <input type="hidden" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>">
                <input type="hidden" id="harga" value="<?= $produk['harga'] ?>">

                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($produk['nama_produk']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label>Harga Satuan</label>
                    <input type="text" class="form-control" value="Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label>Jumlah</label>
                    <div class="jumlah-wrapper">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="kurangJumlah()">−</button>
                        <input type="number" class="form-control" name="jumlah" id="jumlah" min="1" max="<?= $produk['stok']; ?>" value="1" style="width: 80px;" required>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="tambahJumlah()">+</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Total Harga</label>
                    <input type="text" class="form-control" id="total_harga" value="Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label>Kota Tujuan</label>
                    <select class="form-select" name="kota_tujuan" id="kota_tujuan" required>
                        <option value="501">Lampung Timur</option>
                        <option value="114">Bandar Lampung</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Kurir</label>
                    <select class="form-select" name="kurir" id="kurir" required>
                        <option value="tiki">Pick Up Sendiri</option>
                        <option value="jne">SAPX Express</option>
                        <option value="pos">J&T Express</option>
                    </select>
                <div class="mb-3">
                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                    <select class="form-select" name="metode_pembayaran" id="metode_pembayaran" required>
                        <option value="">-- Pilih Metode Pembayaran --</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet (OVO, DANA, dsb)</option>
                        <option value="COD">Cash on Delivery (COD)</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Beli Sekarang</button>
                    <a href="../add_to_cart.php?id_produk=<?= $produk['id_produk'] ?>" class="btn btn-warning">🛒 Tambah ke Keranjang</a>
                    <a href="../message/chat.php?= $produk['id_petani'] ?>" class="btn btn-outline-success">💬 Chat Petani</a>
                    <a href="../index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>

            <script>
                const jumlahInput = document.getElementById('jumlah');
                const totalHargaInput = document.getElementById('total_harga');
                const harga = parseInt(document.getElementById('harga').value);

                function updateTotal() {
                    const jumlah = parseInt(jumlahInput.value) || 0;
                    const total = harga * jumlah;
                    totalHargaInput.value = 'Rp ' + total.toLocaleString('id-ID');
                }

                jumlahInput.addEventListener('input', updateTotal);

                function tambahJumlah() {
                    let current = parseInt(jumlahInput.value);
                    if (current < <?= $produk['stok'] ?>) {
                        jumlahInput.value = current + 1;
                        updateTotal();
                    }
                }

                function kurangJumlah() {
                    let current = parseInt(jumlahInput.value);
                    if (current > 1) {
                        jumlahInput.value = current - 1;
                        updateTotal();
                    }
                }
            </script>

        </div>
    </div>
</div>

</body>
</html>
