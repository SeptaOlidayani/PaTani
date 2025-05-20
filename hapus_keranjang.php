<?php
session_start();
require_once("config/db.php");

if (!isset($_SESSION['username']) || !isset($_GET['id'])) {
    header("Location: keranjang.php");
    exit;
}

$id_keranjang = intval($_GET['id']);
$username = $_SESSION['username'];

mysqli_query($conn, "DELETE FROM keranjang WHERE id_keranjang = $id_keranjang AND username = '$username'");

header("Location: keranjang.php");
exit;
?>
