<?php
require_once("../config/db.php");

$unggulan = mysqli_query($conn, "SELECT * FROM produk ORDER BY tanggal DESC LIMIT 5");
$terlaris = mysqli_query($conn, "SELECT p.*,SUM(t.jumlah) AS total_terjual FROM transaksi t JOIN produk p ON t.id_produk = p.id_produk GROUP BY t.id_produk ORDER BY total_terjual DESC LIMIT 5");
?>