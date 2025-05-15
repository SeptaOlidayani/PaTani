<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username']) || $_SESSION['jenis'] != 'petani') {
    header("Location: ../login.php");
    exit;
}
$username = $_SESSION['username'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    $foto_name = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $upload_dir = "../uploads/";
    $foto_path = $upload_dir . basename($foto_name);

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (move_uploaded_file($foto_tmp, $foto_path)) {
        $query = "INSERT INTO produk (id_petani, nama_produk, harga, stok, deskripsi, tanggal, foto) 
                  VALUES (?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdiss", $username, $nama_produk, $harga, $stok, $deskripsi, $foto_name);

        if ($stmt->execute()) {
            header("Location: ../index.php?success=1");
            exit;
        } else {
            $error = "Gagal menambahkan produk ke database.";
        }
    } else {
        $error = "Gagal mengupload foto.";
    }

    header("Location: ../produk/tambah.php?error=" . urlencode($error));
    exit;
}
?>
