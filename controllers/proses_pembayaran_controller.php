<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_transaksi = intval($_POST['id_transaksi']);
    $username = $_SESSION['username'];

    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === 0) {
        $nama_file = time() . '_' . basename($_FILES['bukti']['name']);
        $tujuan = "../uploads/bukti/" . $nama_file;

        // Pastikan folder ada
        if (!is_dir("../uploads/bukti")) {
            mkdir("../uploads/bukti", 0777, true);
        }

        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $tujuan)) {
            $bukti_path = "uploads/bukti/" . $nama_file;

            $update = mysqli_query($conn, "UPDATE transaksi 
                                           SET status = 'menunggu verifikasi', bukti = '$bukti_path' 
                                           WHERE id_transaksi = $id_transaksi AND id_konsumen = '$username'");

            if ($update) {
                echo "<script>alert('Bukti pembayaran berhasil dikirim!'); window.location.href='../konfirmasi_pembayaran.php';</script>";
            } else {
                echo "<script>alert('Gagal mengupdate transaksi.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Gagal mengunggah bukti.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('File bukti belum dipilih.'); window.history.back();</script>";
    }
}
?>
