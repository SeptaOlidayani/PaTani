<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['jenis'] !== 'petani') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_transaksi'])) {
    $id_transaksi = intval($_POST['id_transaksi']);
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("UPDATE transaksi SET status = 'terkonfirmasi' WHERE id_transaksi = ? AND id_petani = ?");
    $stmt->bind_param("is", $id_transaksi, $username);

    if ($stmt->execute()) {
        header("Location: pesanan_masuk.php?konfirmasi=berhasil");
    } else {
        echo "Gagal mengkonfirmasi pesanan.";
    }
    exit;
}
?>
