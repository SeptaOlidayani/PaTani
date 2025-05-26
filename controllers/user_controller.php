<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../config/db.php");

// Fungsi ambil user
function getUserByUsername($conn, $username) {
    $query = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// =====================
// Update Profil Umum
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'], $_POST['alamat'], $_POST['no_telp'])) {
    $username = $_SESSION['username'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];

    // Proses foto jika ada
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

    // Update alamat dan telepon
    $stmt = $conn->prepare("UPDATE user SET alamat = ?, no_telp = ? WHERE username = ?");
    $stmt->bind_param("sss", $alamat, $no_telp, $username);

    if ($stmt->execute()) {
        header("Location: ../user/settings.php?success=1");
        exit();
    } else {
        header("Location: ../user/settings.php?error=Gagal memperbarui profil.");
        exit();
    }
}

// =====================
// Ganti Password
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $username = $_SESSION['username'];
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        header("Location: ../user/settings.php?error=Konfirmasi password tidak cocok.");
        exit();
    }

    // Ambil password lama
    $stmt = $conn->prepare("SELECT password FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    if (!$userData || !password_verify($old, $userData['password'])) {
        header("Location: ../user/settings.php?error=Password lama salah.");
        exit();
    }

    // Simpan password baru
    $hashed = password_hash($new, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE user SET password = ? WHERE username = ?");
    $update->bind_param("ss", $hashed, $username);
    $update->execute();

    header("Location: ../user/settings.php?success=1");
    exit();
}
