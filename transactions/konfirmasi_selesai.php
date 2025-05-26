<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['jenis'] !== 'pembeli') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_transaksi'];
    $username = $_SESSION['username'];

    // Pastikan hanya pembeli terkait yang bisa update status
    $check = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_transaksi = '$id' AND id_konsumen = '$username' AND status = 'dikirim'");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE transaksi SET status = 'selesai' WHERE id_transaksi = '$id'");
    }

    header("Location: riwayat.php");
    exit;
}
?>
