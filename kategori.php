<?php
session_start();
require_once("config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get kategori dari parameter URL
$kategori_selected = $_GET['kategori'] ?? 'semua';

// Daftar kategori yang tersedia
$kategori_list = [
    'semua' => 'Semua Produk',
    'sayuran' => 'Sayuran',
    'buah' => 'Buah-buahan', 
    'beras' => 'Beras & Biji-bijian',
    'rempah' => 'Rempah-rempah',
    'umbi' => 'Umbi-umbian',
    'kacang' => 'Kacang-kacangan'
];

// Query produk berdasarkan kategori
if ($kategori_selected === 'semua') {
    $query = "SELECT * FROM produk ORDER BY tanggal DESC";
} else {
    $query = "SELECT * FROM produk WHERE kategori = ? ORDER BY tanggal DESC";
}

$stmt = $conn->prepare($query);
if ($kategori_selected !== 'semua') {
    $stmt->bind_param("s", $kategori_selected);
}
$stmt->execute();
$result = $stmt->get_result();

$username = $_SESSION['username'];
$user_query = mysqli_query($conn, "SELECT jenis FROM user WHERE username = '$username'");
$user_data = mysqli_fetch_assoc($user_query);
$jenis_user = $user_data['jenis'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Produk - PaTani</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="scss/style.css">
    <link rel="stylesheet" href="scss/kategori.css">
</head>
<body>

<?php 
include('navbar/nav.php');
include('navbar/bot_nav.php');
?>

<div class="container my-4">
    <div class="kategori-header">
        <h2 class="kategori-title">
            <span class="icon">🏷️</span>
            Kategori Produk
        </h2>
        <p class="kategori-subtitle">Temukan produk pertanian terbaik berdasarkan kategori pilihan Anda</p>
    </div>

    <!-- Filter Kategori -->
    <div class="kategori-filter">
        <div class="filter-container">
            <?php foreach ($kategori_list as $key => $label): ?>
                <a href="?kategori=<?= $key ?>" 
                   class="filter-btn <?= $kategori_selected === $key ? 'active' : '' ?>">
                    <?php
                    $icons = [
                        'semua' => '🌾',
                        'sayuran' => '🥬',
                        'buah' => '🍎',
                        'beras' => '🌾',
                        'rempah' => '🌿',
                        'umbi' => '🥔',
                        'kacang' => '🥜'
                    ];
                    echo $icons[$key] ?? '📦';
                    ?>
                    <span><?= $label ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Informasi Kategori Terpilih -->
    <div class="kategori-info">
        <div class="info-card">
            <h3><?= $kategori_list[$kategori_selected] ?></h3>
            <p>Menampilkan <strong><?= $result->num_rows ?></strong> produk</p>
        </div>
    </div>

    <!-- Grid Produk -->
    <div class="produk-section">
        <?php if ($result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($produk = $result->fetch_assoc()): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="produk-card">
                            <div class="produk-image">
                                <img src="uploads/<?= htmlspecialchars($produk['foto']); ?>" 
                                     alt="<?= htmlspecialchars($produk['nama_produk']); ?>">
                                <div class="produk-badge">
                                    <?php
                                    $kategori_icons = [
                                        'sayuran' => '🥬',
                                        'buah' => '🍎',
                                        'beras' => '🌾',
                                        'rempah' => '🌿',
                                        'umbi' => '🥔',
                                        'kacang' => '🥜'
                                    ];
                                    echo $kategori_icons[$produk['kategori']] ?? '📦';
                                    ?>
                                </div>
                            </div>
                            <div class="produk-content">
                                <h5 class="produk-title"><?= htmlspecialchars($produk['nama_produk']); ?></h5>
                                <p class="produk-price">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></p>
                                <div class="produk-info">
                                    <span class="stok">Stok: <?= $produk['stok']; ?> kg</span>
                                    <span class="tanggal"><?= date('d M Y', strtotime($produk['tanggal'])); ?></span>
                                </div>
                                <?php if ($jenis_user === 'pembeli'): ?>
                                    <div class="produk-actions">
                                        <a href="transactions/purchase.php?id_produk=<?= $produk['id_produk']; ?>" 
                                           class="btn-beli">
                                            <span>🛒</span> Beli Sekarang
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3>Belum Ada Produk</h3>
                <p>Maaf, belum ada produk dalam kategori "<?= $kategori_list[$kategori_selected] ?>" saat ini.</p>
                <a href="?kategori=semua" class="btn-back">← Lihat Semua Produk</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>