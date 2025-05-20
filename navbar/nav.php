<nav class="navbar">
    <div class="logo">
    <img src="/PaTani/img/PaTani.png" alt="Logo">
        <span>PAK TANI</span>
    </div>
    <form action="index.php" method="GET" class="search-container">
    <input type="text" name="cari" placeholder="Cari produk..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
    <button type="submit">🔍</button>
</form>

    <div class="d-flex align-items-center gap-2" style="display: flex; gap: 10px;">
    <div class="cart">
            <a href="keranjang.php" style="text-decoration: none; background: white; color: #4aff9e; width: 40px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; position: relative;">
                🛒
                <span class="badge bg-danger text-white" style="position: absolute; top: -5px; right: -8px; font-size: 0.7rem; padding: 2px 5px; border-radius: 50%;">
            <?php
            $jumlah = 0;
            if (isset($_SESSION['username'])) {
                $u = $_SESSION['username'];
                $result = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keranjang WHERE username='$u'");
                $row = mysqli_fetch_assoc($result);
                $jumlah = $row['total'] ?? 0;
            }
            echo $jumlah;
            ?>
        </span>
    </a>
</div>
    <div class="logout">
            <a href="/PaTani/logout.php" title="Logout" style="text-decoration: none; background: white; color: #4aff9e; width: 40px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                🔓
            </a>
        </div>
    </div>
</nav>
<div class="submenu">
    <a href="katalog.php">Katalog</a>
    <a href="kategori.php">Kategori</a>
    <a href="konfirmasi.php">Konfirmasi Pembayaran</a>
</div>
<script src="/PaTani/js/script.js"></script>



