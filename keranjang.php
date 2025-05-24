<?php
session_start();
require_once("config/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$query = mysqli_query($conn, "SELECT k.*, p.nama_produk, p.harga, p.foto 
                              FROM keranjang k 
                              JOIN produk p ON k.id_produk = p.id_produk 
                              WHERE k.username = '$username'");

$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Saya - Pak Tani</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="scss/style.css">
    <link rel="stylesheet" href="scss/keranjang.css">
    <link rel="stylesheet" href="scss/navbar.css">
    <style>

        .item-check {
            display: none;
            margin-top: 8px;
        }
        .jumlah-input {
            width: 60px;
            text-align: center;
        }
        .table thead th {
        color: #2e7d32;
        background-color: #e8f5e9;
    }
    </style>
</head>
<body>

<?php include('navbar/nav.php'); ?>
<?php include('navbar/bot_nav.php'); ?>

<div class="container mt-4">
    <h2>Keranjang Saya</h2>

    <?php if (mysqli_num_rows($query) > 0): ?>
        <form action="controllers/checkout_controller.php" method="POST">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total Produk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query)): 
                        $total = $row['harga'] * $row['jumlah'];
                        $grand_total += $total;
                    ?>
                    <tr class="item-row" data-id="<?= $row['id_keranjang'] ?>" data-harga="<?= $row['harga'] ?>">
                        <td>
                            <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" alt="Produk" width="70">
                            <input type="checkbox" class="form-check-input item-check" name="selected[]" value="<?= $row['id_keranjang'] ?>">
                        </td>
                        <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-minus">−</button>
                                <input type="number" class="form-control jumlah-input mx-2" name="jumlah[<?= $row['id_keranjang'] ?>]" value="<?= (int)$row['jumlah'] ?>" min="1">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-plus">+</button>
                            </div>
                        </td>
                        <td class="total-cell">Rp <?= number_format($total, 0, ',', '.') ?></td>
                        <td>
                            <a href="hapus_keranjang.php?id=<?= $row['id_keranjang'] ?>" class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total</strong></td>
                        <td colspan="2"><strong id="grandTotal">Rp <?= number_format($grand_total, 0, ',', '.') ?></strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="mb-3">
                <label for="metode_pembayaran" class="form-label">Pilih Metode Pembayaran</label>
                <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                <option value="">-- Pilih Metode Pembayaran --</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet (OVO, DANA, dsb)</option>
                        <option value="COD">Cash on Delivery (COD)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Checkout</button>
        </form>
    <?php else: ?>
        <div class="alert alert-info text-center">Keranjang kamu masih kosong.</div>
    <?php endif; ?>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
// Long press untuk tampilkan checkbox
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('.item-row');
    let pressTimer;

    rows.forEach(row => {
        row.addEventListener('mousedown', () => {
            pressTimer = setTimeout(() => {
                const checkbox = row.querySelector('.item-check');
                checkbox.style.display = 'inline-block';
                checkbox.checked = !checkbox.checked;
            }, 600);
        });

        row.addEventListener('mouseup', () => clearTimeout(pressTimer));
        row.addEventListener('mouseleave', () => clearTimeout(pressTimer));
    });

    // Tambah & kurang jumlah
    const updateTotal = () => {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const harga = parseInt(row.dataset.harga);
            const input = row.querySelector('.jumlah-input');
            const jumlah = parseInt(input.value);
            const total = harga * jumlah;
            row.querySelector('.total-cell').textContent = 'Rp ' + total.toLocaleString('id-ID');
            grandTotal += total;
        });
        document.getElementById('grandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    };

    document.querySelectorAll('.btn-plus').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('.jumlah-input');
            input.value = parseInt(input.value) + 1;
            updateTotal();
        });
    });

    document.querySelectorAll('.btn-minus').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('.jumlah-input');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateTotal();
            }
        });
    });
});
</script>
</body>
</html>
