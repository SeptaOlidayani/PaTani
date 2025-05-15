<?php
require_once("../config/db.php");
header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$alamat   = $_POST['alamat'] ?? '';
$no_telp  = $_POST['no_telp'] ?? '';
$role     = $_POST['role'] ?? '';

if (!$username || !$password || !$alamat || !$no_telp || !$role) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap."]);
    exit;
}

$check = $conn->prepare("SELECT * FROM user WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Username sudah digunakan."]);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO user (username, password, alamat, no_telp, jenis, tambah_produk, riwayat_pembelian) VALUES (?, ?, ?, ?, ?, 0, 0)");
$stmt->bind_param("sssss", $username, $hashed_password, $alamat, $no_telp, $role);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Akun berhasil dibuat!"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan data."]);
}
