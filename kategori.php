<?php
session_start();
require_once("config/db.php");

$kategori = $_GET['kategori'] ?? '';

// Ambil produk berdasarkan kategori
if ($kategori) {
    $stmt = $conn->prepare("SELECT * FROM produk WHERE kategori = ? ORDER BY tanggal DESC");
    $stmt->bind_param("s", $kategori);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = mysqli_query($conn, "SELECT * FROM produk ORDER BY tanggal DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kategori Produk - PaTani</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="scss/style.css">
    <style>
        body {
            background: #e8f5e9;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        .kategori-header {
            background-color: #388e3c;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .kategori-nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1rem;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .kategori-nav a {
            text-decoration: none;
            color: #388e3c;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            background-color: #f1f8e9;
            transition: all 0.2s;
        }
        .kategori-nav a:hover,
        .kategori-nav a.active {
            background-color: #66bb6a;
            color: white;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.5rem;
            padding: 0 2rem 2rem;
        }
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            height: 180px;
            background-size: cover;
            background-position: center;
        }
        .product-info {
            padding: 1rem;
        }
        .product-name {
            font-weight: bold;
            font-size: 1.1rem;
            color: #2e7d32;
            margin-bottom: 0.3rem;
        }
        .product-price {
            color: #555;
            margin-bottom: 0.5rem;
        }
        .product-actions {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>
<body>

<div class="kategori-header">
    <h2><?= $kategori ? "Kategori: " . htmlspecialchars(ucfirst($kategori)) : "Semua Produk" ?></h2>
</div>

<div class="kategori-nav">
    <a href="kategori.php" class="<?= $kategori == '' ? 'active' : '' ?>">Semua</a>
    <a href="kategori.php?kategori=buah" class="<?= $kategori == 'buah' ? 'active' : '' ?>">Buah</a>
    <a href="kategori.php?kategori=sayur" class="<?= $kategori == 'sayur' ? 'active' : '' ?>">Sayur</a>
    <a href="kategori.php?kategori=biji" class="<?= $kategori == 'biji' ? 'active' : '' ?>">Biji-bijian</a>
    <a href="kategori.php?kategori=rempah" class="<?= $kategori == 'rempah' ? 'active' : '' ?>">Rempah-rempah</a>
</div>

<div class="products-grid">
    <?php
    if ($result && mysqli_num_rows($result) > 0):
        while ($produk = mysqli_fetch_assoc($result)):
            $foto = htmlspecialchars($produk['foto']);
            $nama = htmlspecialchars($produk['nama_produk']);
            $harga = number_format($produk['harga'], 0, ',', '.');
            $id = $produk['id_produk'];
    ?>
        <div class="product-card">
            <div class="product-image" style="background-image: url('uploads/<?= $foto ?>');"></div>
            <div class="product-info">
                <div class="product-name"><?= $nama ?></div>
                <div class="product-price">Rp <?= $harga ?></div>
                <div class="product-actions">
                    <a href="transactions/purchase.php?id_produk=<?= $id ?>" class="btn btn-success btn-sm w-50">Beli</a>
                    <form action="add_to_cart.php" method="GET" class="w-50">
                        <input type="hidden" name="id_produk" value="<?= $id ?>">
                        <button type="submit" class="btn btn-warning btn-sm w-100">🛒</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; else: ?>
        <p class="text-center w-100">Produk tidak ditemukan untuk kategori ini.</p>
    <?php endif; ?>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
