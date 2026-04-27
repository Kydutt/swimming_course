<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: ../proses/login.php');
    exit;
}

$admin_name  = $_SESSION['user_name'] ?? 'Admin';
$admin_email = $_SESSION['user_email'] ?? '';
$initial     = strtoupper(substr($admin_name, 0, 1));

require_once '../function.php';

$pendaftaran = ambil_semua_pendaftaran();
$statistik   = ambil_statistik_pendaftaran();

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id_peserta'])) {
    $delete_id = $_GET['id_peserta'];
    if (hapus_pendaftaran($delete_id)) {
        header('Location: admin_dashboard.php?success=deleted');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>
<body>
<?php $current_page = 'dashboard'; ?>
<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

    <div class="layout-body">
    <main class="page-wrapper">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Kelola data pendaftaran peserta kursus renang</p>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" id="successAlert">
                <?php if ($_GET['success'] === 'deleted'): ?>
                    <?= icon('check', 16) ?> Data berhasil dihapus.
                <?php elseif ($_GET['success'] === 'updated'): ?>
                    <?= icon('check', 16) ?> Data berhasil diperbarui.
                <?php elseif ($_GET['success'] === 'added'): ?>
                    <?= icon('check', 16) ?> Peserta baru berhasil ditambahkan.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon"><?= icon('clipboard', 26) ?></div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $statistik['total']; ?></div>
                    <div class="stat-label">Total Daftar</div>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon"><?= icon('clock', 26) ?></div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $statistik['pending']; ?></div>
                    <div class="stat-label">Menunggu</div>
                </div>
            </div>
            <div class="stat-card approved">
                <div class="stat-icon"><?= icon('check', 26) ?></div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $statistik['approved']; ?></div>
                    <div class="stat-label">Disetujui</div>
                </div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-icon"><?= icon('x-circle', 26) ?></div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $statistik['rejected']; ?></div>
                    <div class="stat-label">Ditolak</div>
                </div>
            </div>
            <div class="stat-card completed">
                <div class="stat-icon"><?= icon('graduation', 26) ?></div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $statistik['completed']; ?></div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><?= icon('clipboard', 22) ?> Daftar Pendaftaran</h2>
                <div class="card-header-actions">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Cari nama, program...">
                    </div>
                    <a href="tambah_peserta.php" class="btn btn-primary">+ Tambah Peserta</a>
                </div>
            </div>

            <div class="table-container">
                <?php if ($pendaftaran && $pendaftaran->num_rows > 0): ?>
                    <table id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Lengkap</th>
                                <th>Umur</th>
                                <th>Jenis Kelamin</th>
                                <th>WhatsApp</th>
                                <th>Program</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                                <th>Tgl Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; while ($baris = $pendaftaran->fetch_assoc()):
                                $initial_peserta = strtoupper(substr($baris['full_name'], 0, 1));
                            ?>
                                <tr>
                                    <td><span class="id-badge"><?php echo ++$no; ?></span></td>
                                    <td>
                                        <div class="name-cell">
                                            <div class="name-avatar"><?php echo $initial_peserta; ?></div>
                                            <span class="name-text"><?php echo htmlspecialchars($baris['full_name']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo $baris['age']; ?> thn</td>
                                    <td><?php echo $baris['gender']; ?></td>
                                    <td><?php echo $baris['whatsapp']; ?></td>
                                    <td><?php echo $baris['program']; ?></td>
                                    <td><?php echo $baris['schedule']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($baris['status']); ?>">
                                            <?php echo $baris['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($baris['created_at'])); ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="edit_registration.php?id_peserta=<?php echo $baris['id_peserta']; ?>"
                                               class="btn btn-edit"><?= icon('pencil', 12) ?> Edit</a>
                                            <a href="?action=delete&id_peserta=<?php echo $baris['id_peserta']; ?>"
                                               class="btn btn-delete"
                                               onclick="return confirm('Yakin ingin menghapus data ini?')"><?= icon('trash', 14) ?></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <div class="no-data-icon"><?= icon('inbox', 48) ?></div>
                        <p>Belum ada data pendaftaran.</p>
                        <a href="tambah_peserta.php" class="btn btn-primary" style="margin-top: 10px;">+ Tambah Pendaftaran Pertama</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    </div>

    <script src="../assets/js/admin_dashboard.js"></script>
</body>
</html>
