<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') { header('Location: ../proses/login.php'); exit; }
require_once '../function.php';

$current_page = 'data_instruktur';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Instruktur - Admin Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/data_instruktur.css">
</head>
<body>
<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>
<?php require_once 'partials/icons.php'; ?>
<div class="layout-body">
<main class="page-wrapper">
    <?php $breadcrumb_title = 'Data Instruktur'; require_once 'partials/breadcrumb.php'; ?>
    <h1 class="page-title"><?= icon('swim',22) ?> Data Instruktur</h1>
    <p class="page-subtitle">Kelola data instruktur kursus renang</p>
    <div class="card">
        <div class="coming-soon-wrap">
            <div class="cs-icon" style="font-size:initial;"><?= icon('swim',80) ?></div>
            <div class="cs-title">Halaman Sedang Dikembangkan</div>
            <p class="cs-desc">Fitur manajemen data instruktur sedang dalam proses pengembangan. Akan segera tersedia untuk membantu Anda mengelola instruktur kursus renang.</p>
            <span class="cs-badge"><?= icon('clock',14) ?> Coming Soon</span>
        </div>
    </div>
</main>
</div>
<?php require_once 'partials/scripts.php'; ?>
</body>
</html>
