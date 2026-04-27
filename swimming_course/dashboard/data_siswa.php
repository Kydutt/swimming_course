<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') { header('Location: ../proses/login.php'); exit; }
require_once '../function.php';

$current_page = 'data_siswa';

$result = ambil_semua_pendaftaran();
$statistik = ambil_statistik_pendaftaran();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Admin Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>
<body>

<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>
<?php require_once 'partials/icons.php'; ?>

<div class="layout-body">
<main class="page-wrapper">
    <?php $breadcrumb_title = 'Data Siswa'; require_once 'partials/breadcrumb.php'; ?>
    <h1 class="page-title"><?= icon('users',22) ?> Data Siswa</h1>
    <p class="page-subtitle">Kelola seluruh data peserta kursus renang</p>

    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon"><?= icon('document',26) ?></div>
            <div class="stat-info">
                <div class="stat-number"><?= $statistik['total'] ?></div>
                <div class="stat-label">Total Siswa</div>
            </div>
        </div>
        <div class="stat-card approved">
            <div class="stat-icon"><?= icon('check',26) ?></div>
            <div class="stat-info">
                <div class="stat-number"><?= $statistik['approved'] ?></div>
                <div class="stat-label">Aktif</div>
            </div>
        </div>
        <div class="stat-card pending">
            <div class="stat-icon"><?= icon('clock',26) ?></div>
            <div class="stat-info">
                <div class="stat-number"><?= $statistik['pending'] ?></div>
                <div class="stat-label">Menunggu</div>
            </div>
        </div>
        <div class="stat-card rejected">
            <div class="stat-icon"><?= icon('x-circle',26) ?></div>
            <div class="stat-info">
                <div class="stat-number"><?= $statistik['rejected'] ?></div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
        <div class="stat-card completed">
            <div class="stat-icon"><?= icon('graduation',26) ?></div>
            <div class="stat-info">
                <div class="stat-number"><?= $statistik['completed'] ?></div>
                <div class="stat-label">Lulus</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><?= icon('document',18) ?> Daftar Semua Siswa</h2>
            <div class="card-header-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Cari nama, program...">
                </div>
                <a href="tambah_peserta.php" class="btn btn-primary">+ Tambah Siswa</a>
            </div>
        </div>
        <div class="table-container">
            <?php if ($result && $result->num_rows > 0): ?>
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Lengkap</th>
                        <th>Umur</th>
                        <th>Gender</th>
                        <th>WhatsApp</th>
                        <th>Program</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                        <th>Tgl Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 0; while ($row = $result->fetch_assoc()):
                        $init = strtoupper(substr($row['full_name'], 0, 1));
                    ?>
                    <tr>
                        <td><span class="id-badge"><?= ++$no ?></span></td>
                        <td>
                            <div class="name-cell">
                                <div class="name-avatar"><?= $init ?></div>
                                <span class="name-text"><?= htmlspecialchars($row['full_name']) ?></span>
                            </div>
                        </td>
                        <td><?= $row['age'] ?> thn</td>
                        <td><?= $row['gender'] ?></td>
                        <td><?= $row['whatsapp'] ?></td>
                        <td><?= $row['program'] ?></td>
                        <td><?= $row['schedule'] ?></td>
                        <td><span class="status-badge status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                        <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <div class="actions">
                                <a href="edit_registration.php?id_peserta=<?= $row['id_peserta'] ?>" class="btn btn-edit"><?= icon('pencil', 14) ?> Edit</a>
                                <a href="admin_dashboard.php?action=delete&id_peserta=<?= $row['id_peserta'] ?>" class="btn btn-delete"
                                   onclick="return confirm('Hapus data ini?')"><?= icon('trash', 14) ?></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <div class="no-data-icon"><?= icon('inbox', 48) ?></div>
                <p>Belum ada data siswa.</p>
                <a href="tambah_peserta.php" class="btn btn-primary" style="margin-top:10px;">+ Tambah Siswa Pertama</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
</div>

<?php require_once 'partials/scripts.php'; ?>
<script src="../assets/js/data_siswa.js"></script>
</body>
</html>
