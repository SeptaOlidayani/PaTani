<?php
session_start();
require_once("config/db.php");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pak Tani - Katalog Produk</title>
    <link rel="stylesheet" href="scss/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #e8f5e9;
            margin: 0;
            padding: 0;
        }

        .filters {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .filter-section {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-group label {
        font-weight: 500;
        color: #2e7d32;
    }

    .filter-group select {
        padding: 0.5rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.3s;
    }

    .filter-group select:focus {
        border-color: #4caf50;
    }


        .catalog-header {
            text-align: center;
            background-color: #4caf50;
            color: white;
            padding: 2rem 1rem;
        }
        .catalog-header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        .catalog-header p {
            margin-top: 0.5rem;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: auto;
        }
        .product-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }
        .product-info {
            padding: 1rem;
        }
        .product-name {
            font-size: 1.2rem;
            color: #2e7d32;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .product-description {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 1rem;
        }
        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .price {
            color:rgb(19, 18, 18);
            font-weight: bold;
        }
        .stock {
            font-size: 0.85rem;
            color: #388e3c;
        }
        .product-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }
        .btn {
            padding: 0.6rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            flex: 1;
            text-align: center;
            text-decoration: none;
        }
        .btn-primary {
            background-color: #4caf50;
            color: #4caf50;
        }
        .btn-secondary {
            background-color: #ff9800;
            color: white;
        }
    </style>
</head>
<body>

    <div class="catalog-header">
        <h1>Katalog Produk Segar</h1>
        <p>Produk pilihan langsung dari petani terbaik</p>
    </div>
    <div class="filters">
        <div class="filter-section">
            <div class="filter-group">
                <label for="category">Kategori:</label>
                <select id="category">
                    <option value="">Semua Kategori</option>
                    <option value="buah">Buah-buahan</option>
                    <option value="sayur">Sayuran</option>
                    <option value="biji">Biji-bijian</option>
                    <option value="rempah">Rempah-rempah</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="price">Harga:</label>
                <select id="price">
                    <option value="">Semua Harga</option>
                    <option value="0-20000">< Rp 20.000</option>
                    <option value="20000-50000">Rp 20.000 - 50.000</option>
                    <option value="50000+">Rp 50.000 +</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="sort">Urutkan:</label>
                <select id="sort">
                    <option value="newest">Terbaru</option>
                    <option value="price-low">Harga Terendah</option>
                    <option value="price-high">Harga Tertinggi</option>
                    <option value="popular">Terpopuler</option>
                </select>
            </div>
        </div>
    </div>

    <div class="products-grid">
        <?php
        $query = "SELECT * FROM produk ORDER BY tanggal DESC";
        $result = mysqli_query($conn, $query);
        while ($produk = mysqli_fetch_assoc($result)) {
            $foto = htmlspecialchars($produk['foto']);
            $nama = htmlspecialchars($produk['nama_produk']);
            $harga = number_format($produk['harga'], 0, ',', '.');
            $stok = htmlspecialchars($produk['stok']);
            $deskripsi = htmlspecialchars($produk['deskripsi']);
            $id = $produk['id_produk'];

            echo "
            <div class='product-card'>
                <div class='product-image' style='background-image: url(\"uploads/$foto\")'></div>
                <div class='product-info'>
                    <div class='product-name'>$nama</div>
                    <div class='product-description'>$deskripsi</div>
                    <div class='product-details'>
                        <div class='price'>Rp $harga</div>
                        <div class='stock'>Stok: $stok</div>
                    </div>
                    <div class='product-actions' style='display: flex; gap: 10px;'>
    <a href='transactions/purchase.php?id_produk=$id' class='btn btn-success btn-sm' style='flex: 1;'>Beli</a>
    <form action='add_to_cart.php' method='GET' style='flex: 1;'>
        <input type='hidden' name='id_produk' value='$id'>
        <button type='submit' class='btn btn-warning btn-sm w-100'>🛒</button>
    </form>
</div>

                </div>
            </div>
            ";
        }
        ?>
    </div>

</body>
</html>
