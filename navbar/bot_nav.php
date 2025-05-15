<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$jenis_user = $_SESSION['jenis'] ?? null;
?>
<div class="bottom-nav">
    <button onclick="goTo('beranda')">
        <span class="icon">🏠</span>
        <span class="label">Beranda</span>
    </button>
    <button onclick="goTo('chat')">
        <span class="icon">💬</span>
        <span class="label">Chat</span>
    </button>
    <button onclick="goTo('pasar')">
        <span class="icon">🛒</span>
        <span class="label">Pasar</span>
    </button>
    <button onclick="goTo('profil')">
        <span class="icon">👤</span>
        <span class="label">Profil</span>
    </button>
    <?php if ($jenis_user === 'petani'): ?>
    <button onclick="goTo('tambah')">
        <span class="icon">➕</span>
        <span class="label">Tambah</span>
    </button>
    <?php endif; ?>
</div>

<script src="../js/script.js"></script>
