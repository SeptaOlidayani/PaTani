<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];

$selected = $_POST['selected'] ?? [];
$jumlahList = $_POST['jumlah'] ?? [];
$metode = $_POST['metode_pembayaran'] ?? '';

if (empty($selected) || empty($metode)) {
    echo "<script>alert('Pilih produk dan metode pembayaran!'); window.history.back();</script>";
    exit;
}

foreach ($selected as $id_keranjang) {
    // Pastikan input valid
    $id_keranjang = intval($id_keranjang);
    $jumlah = isset($jumlahList[$id_keranjang]) ? intval($jumlahList[$id_keranjang]) : 1;
    if ($jumlah < 1) $jumlah = 1;

    // Ambil info produk dari keranjang
    $query = mysqli_query($conn, "SELECT p.id_produk, p.id_petani, p.nama_produk, p.harga 
                                  FROM keranjang k 
                                  JOIN produk p ON k.id_produk = p.id_produk 
                                  WHERE k.id_keranjang = $id_keranjang AND k.username = '$username'");
    
    if (!$query || mysqli_num_rows($query) == 0) continue;

    $data = mysqli_fetch_assoc($query);

    $id_produk = $data['id_produk'];
    $id_petani = $data['id_petani'];
    $nama_produk = $data['nama_produk'];
    $harga = (int)$data['harga'];
    $total_harga = $harga * $jumlah;

    // Simpan ke tabel transaksi
    $insert = mysqli_query($conn, "INSERT INTO transaksi 
        (id_konsumen, id_petani, id_produk, produk, jumlah, total_harga, metode, status) 
        VALUES 
        ('$username', '$id_petani', '$id_produk', '$nama_produk', $jumlah, $total_harga, '$metode', 'menunggu konfirmasi')");

    // Hapus dari keranjang
    if ($insert) {
        mysqli_query($conn, "DELETE FROM keranjang WHERE id_keranjang = $id_keranjang AND username = '$username'");
    }
}

echo "<script>alert('Checkout berhasil!'); window.location.href = '../index.php';</script>";
exit;
?>
