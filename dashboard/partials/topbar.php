<?php
require_once __DIR__ . '/icons.php';
$admin_name = $_SESSION['user_name'] ?? 'Admin';
$initial    = strtoupper(substr($admin_name, 0, 1));
?>
<header class="topbar">
    <div class="topbar-left">
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <div class="topbar-right">
        <a href="../index.php" class="topbar-icon-btn" title="Beranda"><?= icon('home', 18) ?></a>
        <button class="topbar-icon-btn" title="Notifikasi"><?= icon('bell', 18) ?></button>
        <button class="topbar-icon-btn" title="Bantuan"><?= icon('help-circle', 18) ?></button>
        <div class="topbar-avatar">
            <img src="../assets/img/logo.png" alt="Indramayu Swimming Courses"
             style="height:45px; width:45px; object-fit:contain; border-radius:50%;">
        </div>
    </div>
</header>
