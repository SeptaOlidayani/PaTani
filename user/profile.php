<?php
session_start();
require_once("../config/db.php");
require_once("../controllers/user_controller.php");

if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'];
$user = getUserByUsername($conn, $username);
$_SESSION['jenis'] = $user['jenis'];

$jumlah_produk = 0;
$transaksi = 0;
$rating = 4.8;

if ($user['jenis'] === 'petani') {
    // Hitung produk petani
    $stmt1 = $conn->prepare("SELECT COUNT(*) as total FROM produk WHERE id_petani = ?");
    $stmt1->bind_param("s", $username);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $jumlah_produk = $result1->fetch_assoc()['total'];

    // Hitung transaksi petani
    $stmt2 = $conn->prepare("SELECT COUNT(*) as total FROM transaksi WHERE id_petani = ?");
    $stmt2->bind_param("s", $username);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $transaksi = $result2->fetch_assoc()['total'];
} else {
    // Hitung transaksi pembeli (perbaikan: id_konsumen)
    $stmt3 = $conn->prepare("SELECT COUNT(*) as total FROM transaksi WHERE id_konsumen = ?");
    $stmt3->bind_param("s", $username);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    $transaksi = $result3->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Saya</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <style>
    .profile-header {
      background-color: #c8f5d2;
      padding: 20px;
      text-align: center;
      position: relative;
    }
    .profile-photo-container {
      position: relative;
      display: inline-block;
    }
    .profile-photo-container img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
    }
    .edit-icon-bottom {
      position: absolute;
      bottom: 0;
      right: -10px;
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 50%;
      padding: 4px 6px;
      cursor: pointer;
      font-size: 14px;
    }
    .stat-card {
      background: #4caf50;
      color: white;
      padding: 15px 20px;
      border-radius: 10px;
      text-align: center;
      font-weight: bold;
      flex: 1 1 30%;
      min-width: 100px;
    }
    .btn-produk-saya {
      background-color: #2e7d32;
      color: white;
      padding: 12px 15px;
      display: block;
      text-align: center;
      border-radius: 6px;
      margin-top: 10px;
      text-decoration: none;
      font-weight: 500;
    }
  </style>
</head>
<body>

<!-- HEADER PROFIL -->
<div class="profile-header">
  <div class="profile-photo-container">
    <img src="<?= $user['foto'] ?? '../assets/default-profile.png' ?>" alt="Foto Profil">
    <div class="edit-icon-bottom" data-bs-toggle="modal" data-bs-target="#editProfilModal">✏️</div>
  </div>
  <div class="profile-info mt-2">
    <h4><?= htmlspecialchars($user['username']) ?></h4>
    <div>
      <?= $user['jenis'] === 'petani' ? 'Petani - ' . htmlspecialchars($user['keterangan'] ?? 'Beras dan Sayuran') : 'Pembeli' ?>
    </div>
    <small><?= htmlspecialchars($user['alamat']) ?></small>
  </div>
</div>

<!-- STATISTIK & GALERI HANYA UNTUK PETANI -->
<?php if ($user['jenis'] === 'petani'): ?>
  <div class="container my-4">
    <div class="d-flex justify-content-center flex-wrap gap-3">
      <div class="stat-card"><?= $jumlah_produk ?><br><small>Hasil Panen</small></div>
      <div class="stat-card"><?= $rating ?><br><small>Rating</small></div>
      <div class="stat-card"><?= $transaksi ?><br><small>Transaksi</small></div>
    </div>
  </div>

  <?php
  $produkQuery = $conn->prepare("SELECT nama_produk, foto FROM produk WHERE id_petani = ? ORDER BY tanggal DESC LIMIT 8");
  $produkQuery->bind_param("s", $username);
  $produkQuery->execute();
  $produkResult = $produkQuery->get_result();
  if ($produkResult->num_rows > 0): ?>
    <div class="container mb-4">
      <h5 class="text-center">🖼️ Produk Saya</h5>
      <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php while ($row = $produkResult->fetch_assoc()): ?>
          <div class="col">
            <div class="card h-100">
              <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nama_produk']) ?>" style="height: 120px; object-fit: cover;">
              <div class="card-body p-2 text-center">
                <small class="fw-bold"><?= htmlspecialchars($row['nama_produk']) ?></small>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  <?php endif; ?>
<?php endif; ?>

<!-- TOMBOL AKSI -->
<div class="container">
  <?php if ($user['jenis'] === 'petani'): ?>
    <a href="../product/list_product.php" class="btn-produk-saya">📦 Lihat Produk Saya</a>
    <a href="../transactions/pesanan_masuk.php" class="btn-produk-saya">📥 Pesanan Masuk</a>
    <a href="../transactions/riwayat.php" class="btn-produk-saya">🕓 Riwayat Transaksi</a>
    <a href="../user/settings.php" class="btn-produk-saya">⚙️ Pengaturan Akun</a>
  <?php else: ?>
    <a href="../transactions/riwayat.php" class="btn-produk-saya">🧾 Riwayat Pembelian</a>
    <a href="../product/favorit.php" class="btn-produk-saya">❤️ Favorit Saya</a>
    <a href="../user/settings.php" class="btn-produk-saya">⚙️ Pengaturan Akun</a>
  <?php endif; ?>
</div>

<!-- MODAL EDIT PROFIL -->
<div class="modal fade" id="editProfilModal" tabindex="-1" aria-labelledby="editProfilModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="../controllers/user_controller.php" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfilModalLabel">Edit Profil</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-3">
            <img src="<?= $user['foto'] ?? '../assets/default-profile.png' ?>" id="previewFoto" class="rounded-circle" width="120" height="120" alt="Foto Profil">
            <input type="file" name="foto" id="fotoInput" class="form-control mt-2" accept="image/*">
          </div>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['jenis']) ?>" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" required><?= htmlspecialchars($user['alamat']) ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($user['no_telp']) ?>" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="update_profile" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
