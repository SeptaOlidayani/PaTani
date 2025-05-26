<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['jenis'] !== 'petani') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_transaksi'];
    $aksi = $_POST['aksi'];

    if ($aksi === 'konfirmasi') {
        $status = 'dikonfirmasi';
    } elseif ($aksi === 'dikirim') {
        $status = 'dikirim';
    } else {
        exit("Aksi tidak valid.");
    }

    mysqli_query($conn, "UPDATE transaksi SET status = '$status' WHERE id_transaksi = '$id'");
    header("Location: pesanan_masuk.php");
    exit;
}
?>
