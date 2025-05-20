<?php
require_once("../config/db.php");

// Fungsi untuk mendapatkan semua kategori yang tersedia
function getAvailableCategories($conn) {
    $query = "SELECT DISTINCT kategori FROM produk WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori";
    $result = mysqli_query($conn, $query);
    $categories = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['kategori'];
    }
    
    return $categories;
}

// Fungsi untuk mendapatkan produk berdasarkan kategori
function getProductsByCategory($conn, $category = null, $search = null, $limit = null) {
    $query = "SELECT p.*, u.alamat as alamat_petani FROM produk p 
              LEFT JOIN user u ON p.id_petani = u.username 
              WHERE 1=1";
    $params = [];
    $types = "";
    
    if ($category && $category != '') {
        $query .= " AND p.kategori = ?";
        $params[] = $category;
        $types .= "s";
    }
    
    if ($search && $search != '') {
        $query .= " AND (p.nama_produk LIKE ? OR p.deskripsi LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "ss";
    }
    
    $query .= " ORDER BY p.tanggal DESC";
    
    if ($limit) {
        $query .= " LIMIT ?";
        $params[] = $limit;
        $types .= "i";
    }
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    return $products;
}

// Fungsi untuk menghitung jumlah produk per kategori
function getProductCountByCategory($conn) {
    $query = "SELECT kategori, COUNT(*) as count FROM produk 
              WHERE kategori IS NOT NULL AND kategori != '' 
              GROUP BY kategori 
              ORDER BY count DESC";
    
    $result = mysqli_query($conn, $query);
    $counts = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $counts[$row['kategori']] = $row['count'];
    }
    
    return $counts;
}

// Fungsi untuk mendapatkan produk terpopuler per kategori
function getPopularProductsByCategory($conn, $category, $limit = 5) {
    $query = "SELECT p.*, COUNT(t.id_transaksi) as sales_count 
              FROM produk p 
              LEFT JOIN transaksi t ON p.id_produk = t.id_produk 
              WHERE p.kategori = ? 
              GROUP BY p.id_produk 
              ORDER BY sales_count DESC, p.tanggal DESC 
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $category, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    return $products;
}

// Fungsi untuk mendapatkan produk terbaru per kategori
function getLatestProductsByCategory($conn, $category, $limit = 5) {
    $query = "SELECT * FROM produk 
              WHERE kategori = ? 
              ORDER BY tanggal DESC 
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $category, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    return $products;
}

// Fungsi untuk mendapatkan statistik kategori
function getCategoryStats($conn) {
    $query = "SELECT 
                kategori,
                COUNT(*) as total_products,
                AVG(harga) as avg_price,
                SUM(stok) as total_stock,
                MAX(tanggal) as latest_update
              FROM produk 
              WHERE kategori IS NOT NULL AND kategori != ''
              GROUP BY kategori
              ORDER BY total_products DESC";
    
    $result = mysqli_query($conn, $query);
    $stats = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $stats[$row['kategori']] = $row;
    }
    
    return $stats;
}

// Fungsi untuk memvalidasi kategori
function isValidCategory($category) {
    $validCategories = ['sayuran', 'buah', 'rempah', 'biji', 'umbi'];
    return in_array($category, $validCategories);
}

// Fungsi untuk mendapatkan nama kategori yang lebih friendly
function getCategoryDisplayName($category) {
    $categoryNames = [
        'sayuran' => 'Sayuran',
        'buah' => 'Buah-buahan',
        'rempah' => 'Rempah-rempah',
        'biji' => 'Biji-bijian',
        'umbi' => 'Umbi-umbian'
    ];
    
    return isset($categoryNames[$category]) ? $categoryNames[$category] : ucfirst($category);
}

// Fungsi untuk mendapatkan icon kategori
function getCategoryIcon($category) {
    $categoryIcons = [
        'sayuran' => '🥬',
        'buah' => '🍎',
        'rempah' => '🌶️',
        'biji' => '🌽',
        'umbi' => '🥔'
    ];
    
    return isset($categoryIcons[$category]) ? $categoryIcons[$category] : '🌾';
}

// API endpoint untuk AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_categories':
            $categories = getAvailableCategories($conn);
            echo json_encode(['success' => true, 'categories' => $categories]);
            break;
            
        case 'get_products':
            $category = $_GET['category'] ?? null;
            $search = $_GET['search'] ?? null;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;
            
            $products = getProductsByCategory($conn, $category, $search, $limit);
            echo json_encode(['success' => true, 'products' => $products]);
            break;
            
        case 'get_category_stats':
            $stats = getCategoryStats($conn);
            echo json_encode(['success' => true, 'stats' => $stats]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
?>