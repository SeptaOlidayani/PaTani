<?php
require_once '../vendor/autoload.php'; // Sesuaikan dengan lokasi composer autoload
require_once '../config/db.php';       // Koneksi ke database jika perlu

\Midtrans\Config::$serverKey = 'SB-Mid-server-J8HsOi4G92jfmUrpznhC8e6I'; // Ganti dengan Server Key kamu
\Midtrans\Config::$isProduction = false; // true jika live

header('Content-Type: application/json');

// Ambil data dari JS
$input = json_decode(file_get_contents("php://input"), true);

$order_id = 'ORDER-' . time(); // Unik untuk setiap transaksi
$gross_amount = $input['total']; // Total dari harga + ongkir
$nama_produk = $input['nama_produk'];
$nama_user = $input['nama'];

$transaction = [
    'transaction_details' => [
        'order_id' => $order_id,
        'gross_amount' => $gross_amount,
    ],
    'item_details' => [[
        'id' => $input['id_produk'],
        'price' => $input['harga'],
        'quantity' => $input['jumlah'],
        'name' => $nama_produk,
    ]],
    'customer_details' => [
        'first_name' => $nama_user,
    ]
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($transaction);
    echo json_encode(['token' => $snapToken, 'order_id' => $order_id]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
