<?php
session_start();
require_once('../config/db.php');

if (!isset($_SESSION['username']) || $_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../login.php");
    exit;
}

$id_konsumen    = $_SESSION['username'];
$id_produk      = mysqli_real_escape_string($conn, $_POST['id_produk']);
$id_petani      = mysqli_real_escape_string($conn, $_POST['id_petani']);
$produk         = mysqli_real_escape_string($conn, $_POST['nama_produk']);
$harga          = (int) $_POST['harga'];
$jumlah         = (int) $_POST['jumlah'];
$total          = $harga * $jumlah;

    $query = "INSERT INTO transaksi (id_konsumen, id_petani, id_produk, produk, jumlah, total_harga, status) 
            VALUES ('$id_konsumen', '$id_petani', '$id_produk', '$produk', '$jumlah', '$total', 'menunggu konfirmasi')";

if (mysqli_query($conn, $query)) {
    header("Location: ../index.php");
    exit;
} else {
    die("Gagal menyimpan transaksi: " . mysqli_error($conn));
}