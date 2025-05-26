<?php
session_start();
require_once('../config/db.php');

if (!isset($_SESSION['username']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php");
    exit;
}

$id_konsumen   = $_SESSION['username'];
$id_produk     = mysqli_real_escape_string($conn, $_POST['id_produk']);
$id_petani     = mysqli_real_escape_string($conn, $_POST['id_petani']);
$produk        = mysqli_real_escape_string($conn, $_POST['nama_produk']);
$harga         = (int) $_POST['harga'];
$jumlah        = (int) $_POST['jumlah'];
$ongkir        = (int) $_POST['ongkir'];
$jarak_km      = (float) $_POST['jarak_km'];
$estimasi      = mysqli_real_escape_string($conn, $_POST['estimasi_kirim']);
$metode        = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);
$total_harga   = $harga * $jumlah + $ongkir;

$stmt = $conn->prepare("INSERT INTO transaksi 
    (id_konsumen, id_petani, id_produk, produk, jumlah, total_harga, ongkir, metode, status, jarak_km, estimasi_kirim) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'menunggu konfirmasi', ?, ?)");

$stmt->bind_param("ssssiiddss", 
    $id_konsumen, $id_petani, $id_produk, $produk, $jumlah,
    $total_harga, $ongkir, $metode, $jarak_km, $estimasi
);

if ($stmt->execute()) {
    header("Location: ../index.php");
    exit;
} else {
    die("Gagal menyimpan transaksi: " . $stmt->error);
}
