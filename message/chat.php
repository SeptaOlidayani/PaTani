<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];
$jenis = $_SESSION['jenis'];

// Tentukan lawan jenis chat
$lawan_jenis = $jenis === 'pembeli' ? 'petani' : 'pembeli';

$stmt = $conn->prepare("SELECT username, jenis FROM user WHERE username != ? AND jenis = ?");
$stmt->bind_param("ss", $username, $lawan_jenis);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Obrolan - PaTani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7fafc;
            font-family: 'Segoe UI', sans-serif;
        }

        .chat-container {
            max-width: 800px;
            margin: 40px auto;
        }

        .chat-header {
            font-size: 1.8rem;
            font-weight: bold;
            color: #4caf50;
            margin-bottom: 20px;
        }

        .chat-user-card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 15px;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-user-card:hover {
            background-color: #f1f8e9;
            transform: scale(1.01);
        }

        .chat-user-info strong {
            font-size: 1.1rem;
            color: #333;
        }

        .chat-user-info small {
            color: #777;
        }

        .btn-chat {
            background-color: #4caf50;
            color: #fff;
            border: none;
        }

        .btn-chat:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container chat-container">
    <div class="chat-header">
        <?= $jenis === 'pembeli' ? 'Chat dengan Petani' : 'Chat dengan Pembeli' ?>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="chat-user-card">
                <div class="chat-user-info">
                    <strong><?= htmlspecialchars($row['username']) ?></strong><br>
                    <small><?= ucfirst($row['jenis']) ?></small>
                </div>
                <a href="chat_room.php?user=<?= urlencode($row['username']) ?>" class="btn btn-chat btn-sm">Chat</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">Belum ada pengguna <?= $lawan_jenis ?> untuk diajak chat.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
