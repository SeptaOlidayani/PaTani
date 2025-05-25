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

// Isi manual nama kecamatan petani (karena tidak ambil dari DB user)
$kecamatan_petani_default = "Sukadana"; // <- ubah sesuai produk/petani terkait
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
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="SB-Mid-client-M9wHfTZ_CV_nJKf3"></script>
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
                <input type="hidden" name="jarak_km" id="jarak_value">
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

                <div class="mb-3">
                    <input type="hidden" id="kecamatan_petani_value" value="<?= $kecamatan_petani_default ?>">
                </div>

                <div class="mb-3">
                    <label>Kecamatan Pembeli</label>
                    <select id="kecamatan_pembeli" name="kecamatan_pembeli" class="form-select" required>
                        <option value="">-- Pilih Kecamatan Anda --</option>
                        <option value="Sukadana">Sukadana</option>
                        <option value="Sekampung">Sekampung</option>
                        <option value="Pekalongan">Pekalongan</option>
                        <option value="Way Jepara">Way Jepara</option>
                        <option value="Labuhan Ratu">Labuhan Ratu</option>
                        <option value="Batanghari">Batanghari</option>
                        <!-- Tambahkan sesuai daftar kecamatan di Lampung Timur -->
                    </select>
                </div>

                <div class="mb-3">
                    <label>Alamat Pengiriman</label>
                    <textarea name="alamat" id="alamat" class="form-control" placeholder="Masukan Alamat Pengiriman"></textarea>
                </div>

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
                    <p id="ongkir_label">-</p>
                </div>

                <div class="mb-3">
                    <label>Estimasi Pengiriman</label>
                    <p id="estimasi_label">-</p>
                </div>

                <div class="mb-3"><label>Total Harga + Ongkir</label><input type="text" class="form-control" id="total_harga" disabled></div>

                <div class="mb-3">
                    <label>Metode Pembayaran</label>
                    <select class="form-select" name="metode_pembayaran">
                        <option value="">-- Pilih Metode --</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="COD">COD</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button id="payButton" type="button" class="btn btn-success">Beli Sekarang</button>
                    <a href="../index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
payButton.addEventListener('click', function (event) {
    event.preventDefault(); // Jangan langsung submit form

    const data = {
        id_produk: <?= $produk['id_produk'] ?>,
        nama_produk: "<?= htmlspecialchars($produk['nama_produk']) ?>",
        jumlah: parseInt(jumlahInput.value),
        harga: <?= $produk['harga'] ?>,
        total: harga * parseInt(jumlahInput.value) + parseInt(ongkirInput.value),
        nama: "<?= $_SESSION['username'] ?>", // Ganti dengan data user dari session
    };

    fetch('./midtrans.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if (res.token) {
            window.snap.pay(res.token, {
                onSuccess: function(result) {
                    alert("Pembayaran berhasil!");
                    // Simpan ke database jika perlu, atau redirect
                    window.location.href = "../thank_you.php?order_id=" + res.order_id;
                },
                onPending: function(result) {
                    alert("Menunggu pembayaran.");
                    window.location.href = "../thank_you.php?order_id=" + res.order_id;
                },
                onError: function(result) {
                    alert("Pembayaran gagal!");
                },
                onClose: function() {
                    alert("Transaksi dibatalkan.");
                }
            });
        } else {
            alert("Gagal mendapatkan token Midtrans: " + res.error);
        }
    });
});
</script>

<script>
const harga = <?= $produk['harga'] ?>;
const jumlahInput = document.getElementById('jumlah');
const totalHargaInput = document.getElementById('total_harga');
const ongkirInput = document.getElementById('ongkir_value');
const estimasiInput = document.getElementById('estimasi_value');
const jarakInput = document.getElementById('jarak_value');
const ongkirLabel = document.getElementById('ongkir_label');
const estimasiLabel = document.getElementById('estimasi_label');
const kecamatanPembeli = document.getElementById('kecamatan_pembeli');
const kecamatanPetani = document.getElementById('kecamatan_petani_value');
const kurirSelect = document.getElementById('kurir');

const tarifPerKm = { tiki: 0, jne: 3000, pos: 3000 };
const estimasiKurir = {
    tiki: "Bisa Diambil Hari Ini",
    jne: "1 - 2 Hari",
    pos: "2 - 3 Hari"
};

// Data jarak antar kecamatan (km)
const jarakKecamatan = {
    "Sukadana": {"Sukadana": 0, "Sekampung": 8, "Pekalongan": 6, "Way Jepara": 12, "Labuhan Ratu": 15, "Batanghari": 10},
    "Sekampung": {"Sukadana": 8, "Sekampung": 0, "Pekalongan": 5, "Way Jepara": 10, "Labuhan Ratu": 13, "Batanghari": 6},
    "Pekalongan": {"Sukadana": 6, "Sekampung": 5, "Pekalongan": 0, "Way Jepara": 8, "Labuhan Ratu": 14, "Batanghari": 7},
    "Way Jepara": {"Sukadana": 12, "Sekampung": 10, "Pekalongan": 8, "Way Jepara": 0, "Labuhan Ratu": 7, "Batanghari": 9},
    "Labuhan Ratu": {"Sukadana": 15, "Sekampung": 13, "Pekalongan": 14, "Way Jepara": 7, "Labuhan Ratu": 0, "Batanghari": 11},
    "Batanghari": {"Sukadana": 10, "Sekampung": 6, "Pekalongan": 7, "Way Jepara": 9, "Labuhan Ratu": 11, "Batanghari": 0},
};

function hitungJarakKm(kecPembeli, kecPetani) {
    if (jarakKecamatan[kecPembeli] && jarakKecamatan[kecPembeli][kecPetani]) {
        return jarakKecamatan[kecPembeli][kecPetani];
    }
    return 0;
}

function updateOngkirEstimasi() {
    const pembeli = kecamatanPembeli.value;
    const petani = kecamatanPetani.value;
    const kurir = kurirSelect.value;
    const tarif = tarifPerKm[kurir] || 0;
    const estimasi = estimasiKurir[kurir] || "-";

    const jarak = hitungJarakKm(pembeli, petani);
    const ongkir = Math.ceil(jarak * tarif);

    jarakInput.value = jarak;
    ongkirInput.value = ongkir;
    estimasiInput.value = estimasi;
    ongkirLabel.textContent = "Rp " + ongkir.toLocaleString('id-ID');
    estimasiLabel.textContent = estimasi;

    updateTotal();
}

function updateTotal() {
    const jumlah = parseInt(jumlahInput.value) || 1;
    const ongkir = parseInt(ongkirInput.value) || 0;
    const total = harga * jumlah + ongkir;
    totalHargaInput.value = "Rp " + total.toLocaleString('id-ID');
}

function tambahJumlah() {
    jumlahInput.value = Math.min(parseInt(jumlahInput.value) + 1, <?= $produk['stok'] ?>);
    updateTotal();
}
function kurangJumlah() {
    jumlahInput.value = Math.max(parseInt(jumlahInput.value) - 1, 1);
    updateTotal();
}

// Event listeners
document.addEventListener('DOMContentLoaded', updateOngkirEstimasi);
kurirSelect.addEventListener('change', updateOngkirEstimasi);
kecamatanPembeli.addEventListener('change', updateOngkirEstimasi);
jumlahInput.addEventListener('input', updateTotal);
</script>
</body>
</html>
