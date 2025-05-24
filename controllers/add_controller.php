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
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    $foto_name = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $upload_dir = "../uploads/";
    $foto_path = $upload_dir . basename($foto_name);

    // Buat folder upload jika belum ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Upload file
    if (move_uploaded_file($foto_tmp, $foto_path)) {
        $tanggal = date('Y-m-d H:i:s');
        $query = "INSERT INTO produk (id_petani, nama_produk, harga, kategori, stok, deskripsi, tanggal, foto) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            $error = "Kesalahan saat mempersiapkan query: " . $conn->error;
            header("Location: ../produk/tambah.php?error=" . urlencode($error));
            exit;
        }

        // Ganti 'ssdsisss' jika tipe data kategori berbeda (misalnya angka jadi 'ssdissss')
        $stmt->bind_param("ssdsisss", $username, $nama_produk, $harga, $kategori, $stok, $deskripsi, $tanggal, $foto_name);

        if ($stmt->execute()) {
            header("Location: ../index.php?success=1");
            exit;
        } else {
            $error = "Gagal menambahkan produk ke database: " . $stmt->error;
        }
    } else {
        $error = "Gagal mengupload foto.";
    }

    // Redirect jika ada error
    header("Location: ../produk/tambah.php?error=" . urlencode($error));
    exit;
}
?>
