<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username'])) {
    die("Akses ditolak. Harap login terlebih dahulu.");
}

// Ambil ID konsumen dari session
$username = $_SESSION['username'];
$user_query = mysqli_query($conn, "SELECT id_user FROM user WHERE username = '$username'");
$user_data = mysqli_fetch_assoc($user_query);
$id_konsumen = $user_data['id_user'] ?? null;

if (!$id_konsumen) {
    die("User tidak ditemukan.");
}

// Ambil data dari form
$id_petani = $_POST['id_petani'];
$id_produk = $_POST['id_produk'];
$jumlah = $_POST['jumlah'];
$harga_satuan = $_POST['harga'];
$alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
$ongkir = $_POST['ongkir'];
$jarak_km = $_POST['jarak_km'];
$estimasi_kirim = $_POST['estimasi_kirim'];
$kurir = $_POST['kurir'];
$metode_pembayaran = $_POST['metode_pembayaran'];
$tanggal_transaksi = date("Y-m-d H:i:s");

$total_harga = ($harga_satuan * $jumlah) + $ongkir;

// Insert transaksi
$insert = mysqli_query($conn, "INSERT INTO transaksi (
    id_konsumen, id_petani, id_produk, jumlah, total_harga, ongkir, jarak_km, estimasi_kirim, kurir, alamat, metode_pembayaran, status, tanggal_transaksi
) VALUES (
    '$id_konsumen', '$id_petani', '$id_produk', '$jumlah', '$total_harga', '$ongkir', '$jarak_km', '$estimasi_kirim', '$kurir', '$alamat', '$metode_pembayaran', 'Menunggu Konfirmasi', '$tanggal_transaksi'
)");

if ($insert) {
    // Update stok produk
    mysqli_query($conn, "UPDATE produk SET stok = stok - $jumlah WHERE id_produk = $id_produk");

    echo "success";
} else {
    echo "Gagal menyimpan transaksi: " . mysqli_error($conn);
}
?>
