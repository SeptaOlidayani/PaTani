<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

$username = $_SESSION['username'];

// Ambil data produk favorit user
$stmt = $conn->prepare("
    SELECT p.nama_produk, p.foto, p.harga, p.id_produk
    FROM favorit f
    JOIN produk p ON f.id_produk = p.id_produk
    WHERE f.id_user = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Favorit Saya</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h3 class="mb-4 text-center">❤️ Produk Favorit Saya</h3>

  <?php if ($result->num_rows > 0): ?>
    <div class="row row-cols-2 row-cols-md-4 g-3">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col">
          <div class="card h-100">
            <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nama_produk']) ?>" style="height: 150px; object-fit: cover;">
            <div class="card-body text-center">
              <h6><?= htmlspecialchars($row['nama_produk']) ?></h6>
              <p class="text-success mb-1">Rp<?= number_format($row['harga'], 0, ',', '.') ?></p>
              <a href="detail_produk.php?id=<?= $row['id_produk'] ?>" class="btn btn-sm btn-outline-primary">🔍 Lihat</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info text-center">Belum ada produk favorit.</div>
  <?php endif; ?>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
