<?php
require_once("../config/db.php");
session_start();

function getUserByUsername($conn, $username) {
    $query = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (in_array($_FILES['foto']['type'], $allowedTypes)) {
            $fotoName = time() . '_' . basename($_FILES['foto']['name']);
            $targetPath = "../uploads/" . $fotoName;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath)) {
                $stmtFoto = $conn->prepare("UPDATE user SET foto = ? WHERE username = ?");
                $stmtFoto->bind_param("ss", $targetPath, $username);
                $stmtFoto->execute();
            }
        }
    }
    $update = "UPDATE user SET alamat = ?, no_telp = ? WHERE username = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("sss", $alamat, $no_telp, $username);

    if ($stmt->execute()) {
        header("Location: ../user/profile.php?success=1");
        exit();
    } else {
        echo "Gagal memperbarui profil.";
    }
}
