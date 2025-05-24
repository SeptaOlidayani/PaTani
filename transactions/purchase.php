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

// Ambil data lokasi pembeli & petani
$username = $_SESSION['username'];
$qUser = mysqli_query($conn, "SELECT latitude, longitude FROM user WHERE username = '$username'");
$userLoc = mysqli_fetch_assoc($qUser);

$qPetani = mysqli_query($conn, "SELECT latitude, longitude FROM user WHERE username = '{$produk['id_petani']}'");
$petaniLoc = mysqli_fetch_assoc($qPetani);

// Hitung jarak
function hitungJarak($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $lat1 = deg2rad($lat1);
    $lat2 = deg2rad($lat2);
    $a = sin($dLat / 2) ** 2 +
         sin($dLon / 2) ** 2 * cos($lat1) * cos($lat2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return round($earthRadius * $c, 1);
}
$jarak = hitungJarak($userLoc['latitude'], $userLoc['longitude'], $petaniLoc['latitude'], $petaniLoc['longitude']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pembelian Produk</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-img { max-width: 100%; height: 250px; object-fit: cover; border-radius: 10px; margin-bottom: 15px; }
        .jumlah-wrapper { display: flex; gap: 5px; align-items: center; }
        .jumlah-wrapper input { text-align: center; width: 80px; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4 text-success text-center">Form Pembelian Produk</h2>
    <div class="card shadow">
        <div class="card-body">
            <img src="../uploads/<?= htmlspecialchars($produk['foto']) ?>" alt="Foto Produk" class="product-img">
            <form action="../controllers/purchase_controller.php" method="POST">
                <input type="hidden" name="id_petani" value="<?= $produk['id_petani'] ?>">
                <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
                <input type="hidden" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>">
                <input type="hidden" name="harga" value="<?= $produk['harga'] ?>">
                <input type="hidden" name="jarak_km" value="<?= $jarak ?>" id="jarak_value">
                <input type="hidden" name="ongkir" id="ongkir_value">
                <input type="hidden" name="estimasi_kirim" id="estimasi_value">

                <div class="mb-3"><label>Nama Produk</label><input type="text" class="form-control" value="<?= htmlspecialchars($produk['nama_produk']); ?>" disabled></div>
                <div class="mb-3"><label>Harga Satuan</label><input type="text" class="form-control" value="Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>" disabled></div>
                <div class="mb-3">
                    <label>Jumlah</label>
                    <div class="jumlah-wrapper">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="kurangJumlah()">−</button>
                        <input type="number" class="form-control" name="jumlah" id="jumlah" min="1" max="<?= $produk['stok']; ?>" value="1" required>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="tambahJumlah()">+</button>
                    </div>
                </div>
                <div class="mb-3"><label>Total Harga + Ongkir</label><input type="text" class="form-control" id="total_harga" disabled></div>

                <div class="mb-3">
                    <label>Kurir</label>
                    <select class="form-select" name="kurir" id="kurir" required>
                        <option value="tiki">Pick Up Sendiri</option>
                        <option value="jne">SAPX Express</option>
                        <option value="pos">J&T Express</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Ongkos Kirim</label>
                    <p id="ongkir_label">Rp -</p>
                </div>
                <div class="mb-3">
                    <label>Estimasi Pengiriman</label>
                    <p id="estimasi_label">-</p>
                </div>

                <div class="mb-3">
                    <label>Metode Pembayaran</label>
                    <select class="form-select" name="metode_pembayaran" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="COD">COD</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Beli Sekarang</button>
                    <a href="../index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const jumlahInput = document.getElementById('jumlah');
const totalHargaInput = document.getElementById('total_harga');
const harga = <?= $produk['harga'] ?>;
const jarak = <?= $jarak ?>;

function updateTotal() {
    const jumlah = parseInt(jumlahInput.value) || 1;
    const ongkir = parseInt(document.getElementById('ongkir_value').value) || 0;
    const total = harga * jumlah + ongkir;
    totalHargaInput.value = 'Rp ' + total.toLocaleString('id-ID');
}

function tambahJumlah() {
    if (parseInt(jumlahInput.value) < <?= $produk['stok'] ?>) {
        jumlahInput.value = parseInt(jumlahInput.value) + 1;
        updateTotal();
    }
}
function kurangJumlah() {
    if (parseInt(jumlahInput.value) > 1) {
        jumlahInput.value = parseInt(jumlahInput.value) - 1;
        updateTotal();
    }
}

function hitungOngkirDanEstimasi(jarak, kurir) {
    let tarif = {'tiki': 0, 'jne': 3000, 'pos': 4000};
    let estimasi = {'tiki': 'Bisa Diambil Hari Ini', 'jne': '1 - 2 Hari', 'pos': '2 - 3 Hari'};

    let ongkir = Math.ceil(jarak * (tarif[kurir] || 0));
    document.getElementById('ongkir_value').value = ongkir;
    document.getElementById('estimasi_value').value = estimasi[kurir] || '-';
    document.getElementById('ongkir_label').textContent = 'Rp ' + ongkir.toLocaleString('id-ID');
    document.getElementById('estimasi_label').textContent = estimasi[kurir] || '-';
    updateTotal();
}

document.getElementById('kurir').addEventListener('change', function() {
    hitungOngkirDanEstimasi(jarak, this.value);
});

window.addEventListener('DOMContentLoaded', () => {
    hitungOngkirDanEstimasi(jarak, document.getElementById('kurir').value);
});
</script>
</body>
</html>
