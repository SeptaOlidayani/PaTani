<?php
require_once("../config/db.php");

function getProdukById($id_produk, $username) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM produk WHERE id_produk = ? AND id_petani = ?");
    $stmt->bind_param("is", $id_produk, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function updateProduk($id_produk, $username, $nama_produk, $harga, $stok, $deskripsi, $foto_baru) {
    global $conn;
    $update = $conn->prepare("UPDATE produk SET nama_produk = ?, harga = ?, stok = ?, deskripsi = ?, foto = ? WHERE id_produk = ? AND id_petani = ?");
    $update->bind_param("sdissis", $nama_produk, $harga, $stok, $deskripsi, $foto_baru, $id_produk, $username);
    $update->execute();
}

function uploadFoto($foto_lama) {
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "../uploads/";
        $foto_baru = time() . "_" . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $foto_baru;

        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $valid_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $valid_types) && move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            if ($foto_lama && file_exists("../uploads/" . $foto_lama)) {
                unlink("../uploads/" . $foto_lama);
            }
            return $foto_baru;
        } else {
            echo "<script>alert('Gagal mengunggah foto baru!');</script>";
            return $foto_lama;
        }
    }
    return $foto_lama;
}
?>