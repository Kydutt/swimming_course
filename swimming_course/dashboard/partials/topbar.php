<?php
require_once __DIR__ . '/icons.php';
$admin_name = $_SESSION['user_name'] ?? 'Admin';
$initial    = strtoupper(substr($admin_name, 0, 1));
?>
<body>
<header class="topbar">
    <div style="display:flex; align-items:center; gap:12px;">
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
        <a href="data_siswa.php" class="topbar-brand">
            <div class="brand-icon"><?= icon('swim', 22) ?></div>
            <span class="brand-name">Swimming Course</span>
        </a>
    </div>
    <div class="topbar-right">
        <a href="../index.php" class="btn-logout" style="background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; gap:6px;">
            <?= icon('home', 16) ?> Beranda
        </a>
        <div class="admin-chip">
            <div class="admin-avatar"><?= $initial ?></div>
            <span class="admin-name"><?= htmlspecialchars($admin_name) ?></span>
        </div>

        <a href="../proses/logout.php" class="btn-logout" style="display:inline-flex; align-items:center; gap:6px;">
            <?= icon('logout', 16) ?> Logout
        </a>
    </div>
</header>
