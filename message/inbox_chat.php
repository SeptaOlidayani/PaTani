<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];

$sql = "
    SELECT 
        IF(pengirim = ?, penerima, pengirim) AS lawan,
        MAX(waktu) AS terakhir,
        (
            SELECT COUNT(*) FROM pesan 
            WHERE pengirim = IF(pengirim = ?, penerima, pengirim)
              AND penerima = ?
              AND sudah_dibaca = 0
        ) AS belum_dibaca
    FROM pesan 
    WHERE pengirim = ? OR penerima = ?
    GROUP BY lawan
    ORDER BY terakhir DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $username, $username, $username, $username, $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inbox - PaTani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f7f7; }
        .inbox-container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .inbox-user {
            border-bottom: 1px solid #eee;
            padding: 12px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .inbox-user:last-child {
            border-bottom: none;
        }
        .badge-unread {
            background-color: #dc3545;
            color: white;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 50px;
        }
        .user-link {
            text-decoration: none;
            color: #333;
        }
        .user-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include("../navbar/nav.php"); ?>

<div class="inbox-container">
    <h4 class="mb-4">Kotak Masuk</h4>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="inbox-user">
                <a href="chat_room.php?user=<?= urlencode($row['lawan']) ?>" class="user-link">
                    <?= htmlspecialchars($row['lawan']) ?>
                </a>
                <?php if ($row['belum_dibaca'] > 0): ?>
                    <span class="badge badge-unread"><?= $row['belum_dibaca'] ?></span>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">Belum ada pesan.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>