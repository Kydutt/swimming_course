<?php

// Partial: sidebar admin
// $current_page harus didefinisikan sebelum include
require_once __DIR__ . '/icons.php';
$current_page = $current_page ?? '';
?>
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <div class="sidebar-section-label">MENU UTAMA</div>
        <a href="data_siswa.php" class="sidebar-link <?= $current_page === 'data_siswa' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('users', 18) ?></span><span>Data Siswa</span>
        </a>

        <div class="sidebar-section-label">SISTEM</div>
        </a>
        <a href="data_instruktur.php" class="sidebar-link <?= $current_page === 'data_instruktur' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('swim', 18) ?></span><span>Data Instruktur</span>
        </a>
        <a href="data_kelas.php" class="sidebar-link <?= $current_page === 'data_kelas' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('book', 18) ?></span><span>Data Kelas</span>
        </a>

        <div class="sidebar-section-label">OPERASIONAL</div>
        <a href="tambah_peserta.php" class="sidebar-link <?= $current_page === 'pendaftaran' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('clipboard', 18) ?></span><span>Pendaftaran</span>
        </a>
        <a href="jadwal_latihan.php" class="sidebar-link <?= $current_page === 'jadwal' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('calendar', 18) ?></span><span>Jadwal Latihan</span>
        </a>
        <a href="pembayaran.php" class="sidebar-link <?= $current_page === 'pembayaran' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('credit-card', 18) ?></span><span>Pembayaran</span>
        </a>

        <div class="sidebar-section-label">SISTEM</div>
        <a href="laporan.php" class="sidebar-link <?= $current_page === 'laporan' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('chart-bar', 18) ?></span><span>Laporan</span>
        </a>
        <a href="manajemen_user.php" class="sidebar-link <?= $current_page === 'manajemen_user' ? 'active' : '' ?>">
            <span class="sidebar-icon"><?= icon('user', 18) ?></span><span>Manajemen User</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../proses/logout.php" class="sidebar-logout">
            <span><?= icon('logout', 18) ?></span><span>Logout</span>
        </a>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
