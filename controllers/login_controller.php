<?php
require_once("../config/db.php");
header('Content-Type: application/json');
session_start();
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';
if (!$username || !$password || !$role) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
    exit;
}
$stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND jenis = ?");
$stmt->bind_param("ss", $username, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['jenis'] = $user['jenis'];
        echo json_encode(["success" => true, "message" => "Selamat datang, {$user['username']}!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Password salah"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Pengguna tidak ditemukan"]);
}
$stmt->close();
$conn->close();
?>
