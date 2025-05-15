function goTo(page) {
    const base = '/PaTani/';
    switch (page) {
        case 'beranda':
            window.location.href = base + 'index.php';
            break;
        case 'chat':
            window.location.href = base + 'message/chat.php';
            break;
        case 'pasar':
            window.location.href = base + 'shop/market.php';
            break;
        case 'profil':
            window.location.href = base + 'user/profile.php';
            break;
        case 'tambah':
            window.location.href = base + 'product/add_product.php';
            break;
        case 'produk_saya':
            window.location.href = base + 'product/list_product.php';
            break;
        default:
            console.warn("Halaman tidak dikenali:", page);
    }
}
