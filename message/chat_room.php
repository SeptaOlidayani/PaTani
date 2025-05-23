<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION['username'];
$jenis = $_SESSION['jenis'];

$partner = $_GET['user'] ?? '';
if (!$partner || $partner === $username) {
    echo "<script>alert('User tidak valid!'); window.location.href='chat.php';</script>";
    exit;
}

// Cek user partner
$cek = mysqli_query($conn, "SELECT * FROM user WHERE username = '$partner'");
if (mysqli_num_rows($cek) == 0) {
    echo "<script>alert('Pengguna tidak ditemukan!'); window.location.href='chat.php';</script>";
    exit;
}
$data_partner = mysqli_fetch_assoc($cek);

// Cek role berbeda
if ($jenis === $data_partner['jenis']) {
    echo "<script>alert('Anda hanya dapat chat dengan pengguna berbeda jenis.'); window.location.href='chat.php';</script>";
    exit;
}

// Tandai semua pesan dari partner sebagai sudah dibaca
$update = $conn->prepare("UPDATE pesan SET sudah_dibaca = 1 WHERE pengirim = ? AND penerima = ?");
$update->bind_param("ss", $partner, $username);
$update->execute();

// Simpan pesan jika dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pesan'])) {
    $pesan = trim($_POST['pesan']);
    if (!empty($pesan)) {
        $stmt = $conn->prepare("INSERT INTO pesan (pengirim, penerima, isi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $partner, $pesan);
        $stmt->execute();
    }
    // Kirim notifikasi browser via script (akan dikirim di akhir HTML jika pesan dikirim)
    $triggerNotif = true;
}

// Ambil semua pesan
$stmt = $conn->prepare("
    SELECT * FROM pesan 
    WHERE (pengirim = ? AND penerima = ?) 
       OR (pengirim = ? AND penerima = ?)
    ORDER BY waktu ASC
");
$stmt->bind_param("ssss", $username, $partner, $partner, $username);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Chat Room - PaTani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9f9f9;
        }
        .chat-box {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .chat-header {
            font-weight: bold;
            font-size: 1.5rem;
            color: #4caf50;
            margin-bottom: 15px;
        }
        .chat-message {
            margin-bottom: 10px;
            padding: 10px 15px;
            border-radius: 10px;
            max-width: 70%;
            word-wrap: break-word;
        }
        .dari-saya {
            background: #e8f5e9;
            align-self: end;
            text-align: right;
        }
        .dari-dia {
            background: #eeeeee;
        }
        .chat-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 400px;
            overflow-y: auto;
            padding-bottom: 15px;
        }
    </style>
</head>
<body>


<div class="chat-box">
    <div class="chat-header">Chat dengan <?= htmlspecialchars($partner) ?></div>

    <div class="chat-list mb-3">
        <?php 
        $all = $messages->fetch_all(MYSQLI_ASSOC);
        foreach ($all as $i => $row): 
            $is_last_sent = $row['pengirim'] === $username && (!isset($all[$i + 1]) || $all[$i + 1]['pengirim'] !== $username);
        ?>
            <div class="chat-message <?= $row['pengirim'] === $username ? 'dari-saya ms-auto' : 'dari-dia' ?>">
                <?= nl2br(htmlspecialchars($row['isi'])) ?>
                <div class="text-muted small mt-1">
                    <?= date('d M H:i', strtotime($row['waktu'])) ?>
                    <?php if ($is_last_sent): ?>
                        <br><small class="text-success"><?= $row['sudah_dibaca'] ? '✓ Sudah dibaca' : '✓ Terkirim' ?></small>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" class="d-flex gap-2">
        <input type="text" name="pesan" class="form-control" placeholder="Ketik pesan..." required>
        <button type="submit" class="btn btn-success">Kirim</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if (!empty($triggerNotif)): ?>
<script>
    if ("Notification" in window && Notification.permission === "granted") {
        new Notification("Pesan terkirim ke <?= $partner ?>", {
            body: "Pesan berhasil dikirim.",
            icon: "../assets/icon-chat.png"
        });
    } else if ("Notification" in window && Notification.permission !== "denied") {
        Notification.requestPermission();
    }
</script>
<?php endif; ?>
</body>
</html>