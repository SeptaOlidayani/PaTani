<?php
session_start();
require_once("config/db.php");

if (!isset($_SESSION['username']) || !isset($_GET['id_produk'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$id_produk = intval($_GET['id_produk']);

// Cek apakah sudah ada produk ini di keranjang
$cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE username = '$username' AND id_produk = $id_produk");
if (mysqli_num_rows($cek) > 0) {
    mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE username = '$username' AND id_produk = $id_produk");
} else {
    mysqli_query($conn, "INSERT INTO keranjang (username, id_produk, jumlah) VALUES ('$username', $id_produk, 1)");
}

header("Location: index.php?keranjang=berhasil");
exit;
