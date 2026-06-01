<?php
require_once __DIR__ . '/icons.php';
$current_page = $current_page ?? '';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="../index.php" class="sidebar-brand-link">
            <img src="../assets/img/logo.png" alt="Logo"
                 class="sidebar-brand-logo">
            <div class="sidebar-brand-info">
                <span class="sidebar-brand-name">Swimming Course</span>
                <span class="sidebar-brand-role">Admin Panel</span>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <a href="data_siswa.php" class="sidebar-link <?= $current_page === 'data_siswa' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('users', 18) ?></span><span>Data Siswa</span>
        </a>
        <a href="data_instruktur.php" class="sidebar-link <?= $current_page === 'data_instruktur' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('swim', 18) ?></span><span>Data Instruktur</span>
        </a>
        <a href="data_kelas.php" class="sidebar-link <?= $current_page === 'data_kelas' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('book', 18) ?></span><span>Data Kelas</span>
        </a>
        <a href="jadwal_latihan.php" class="sidebar-link <?= $current_page === 'jadwal' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('calendar', 18) ?></span><span>Jadwal Latihan</span>
        </a>
        <a href="pembayaran.php" class="sidebar-link <?= $current_page === 'pembayaran' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('credit-card', 18) ?></span><span>Pembayaran</span>
        </a>
        <a href="laporan.php" class="sidebar-link <?= $current_page === 'laporan' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('chart-bar', 18) ?></span><span>Laporan</span>
        </a>
        <a href="manajemen_user.php" class="sidebar-link <?= $current_page === 'manajemen_user' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('user', 18) ?></span><span>Manajemen User</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="data_kelas.php" class="sidebar-cta-btn">
            <?= icon('plus', 16) ?> Tambah Program
        </a>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
