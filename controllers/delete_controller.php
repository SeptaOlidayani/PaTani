<?php
session_start();
require_once("../config/db.php");
if (!isset($_SESSION['username']) || $_SESSION['jenis'] !== 'petani') {
    header("Location: ../login.php");
    exit;
}
if (!isset($_GET['id'])) {
    header("Location: ../product/list_product.php");
    exit;
}

$id_produk = $_GET['id'];
$username = $_SESSION['username'];
$cek = $conn->prepare("SELECT foto FROM produk WHERE id_produk = ? AND id_petani = ?");
$cek->bind_param("is", $id_produk, $username);
$cek->execute();
$result = $cek->get_result();

if ($result->num_rows === 0) {
    header("Location: ../product/list_product.php");
    exit;
}

$data = $result->fetch_assoc();

$cekTransaksi = $conn->prepare("SELECT COUNT(*) as total FROM transaksi WHERE id_produk = ?");
$cekTransaksi->bind_param("i", $id_produk);
$cekTransaksi->execute();
$transaksi = $cekTransaksi->get_result()->fetch_assoc();

if ($transaksi['total'] > 0) {
    echo "<script>alert('Produk tidak bisa dihapus karena sudah ada transaksi.'); window.location.href='../product/list_product.php';</script>";
    exit;
}

if ($data['foto'] && file_exists("../uploads/" . $data['foto'])) {
    unlink("../uploads/" . $data['foto']);
}

$delete = $conn->prepare("DELETE FROM produk WHERE id_produk = ? AND id_petani = ?");
$delete->bind_param("is", $id_produk, $username);

if ($delete->execute()) {
    echo "<script>alert('Produk berhasil dihapus!'); window.location.href='../product/list_product.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus produk.'); window.location.href='../product/list_product.php';</script>";
}
?>