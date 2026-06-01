<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: ../proses/login.php');
    exit;
}
require_once '../function.php';

$current_page = 'data_siswa';

// ── Hapus Peserta ────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id_peserta'])) {
    $del_id = (int)$_GET['id_peserta'];
    if (hapus_pendaftaran($del_id)) {
        header('Location: data_siswa.php?success=deleted'); exit;
    }
}

$result    = ambil_semua_pendaftaran();
$statistik = ambil_statistik_pendaftaran();

$flash_map = [
    'deleted' => ['Data peserta berhasil dihapus.',         'alert-success'],
    'updated' => ['Data peserta berhasil diperbarui.',      'alert-success'],
    'added'   => ['Peserta baru berhasil ditambahkan.',     'alert-success'],
];
$flash = $flash_map[$_GET['success'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Admin Swimming Course</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/data_siswa.css">
</head>
<body>
<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<div class="layout-body">
<main class="page-wrapper">

    <?php $breadcrumb_title = 'Data Siswa'; require_once 'partials/breadcrumb.php'; ?>
    <h1 class="page-title"><?= icon('users', 22) ?> Data Siswa</h1>
    <p class="page-subtitle">Kelola data pendaftaran, level, dan kehadiran peserta kursus renang</p>

    <?php if ($flash): ?>
    <div class="alert alert-success" id="successAlert">
        <?= icon('check', 16) ?> <?= $flash[0] ?>
    </div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="stats-grid" style="margin-bottom:24px;">
        <div class="stat-card total">
            <div class="stat-icon"><?= icon('clipboard', 26) ?></div>
            <div class="stat-info">
                <div class="stat-number"><?= $statistik['total'] ?></div>
                <div class="stat-label">Total Daftar</div>
            </div>
        </div>
        <div class="stat-card menunggu">
            <div class="stat-icon"><?= icon('clock', 26) ?></div>
            <div class="stat-info">
                <div class="stat-number"><?= $statistik['menunggu'] ?></div>
                <div class="stat-label">Menunggu</div>
            </div>
        </div>
        <div class="stat-card disetujui">
            <div class="stat-icon"><?= icon('check', 26) ?></div>
            <div class="stat-info">
                <div class="stat-number"><?= $statistik['disetujui'] ?></div>
                <div class="stat-label">Disetujui</div>
            </div>
        </div>
        <div class="stat-card ditolak">
            <div class="stat-icon"><?= icon('x-circle', 26) ?></div>
            <div class="stat-info">
                <div class="stat-number"><?= $statistik['ditolak'] ?></div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
        <div class="stat-card selesai">
            <div class="stat-icon"><?= icon('graduation', 26) ?></div>
            <div class="stat-info">
                <div class="stat-number"><?= $statistik['selesai'] ?></div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>
    </div>

    <!-- Tabel Siswa -->
    <div class="card">
        <div class="card-header">
            <h2><?= icon('users', 20) ?> Daftar Peserta</h2>
            <div class="card-header-actions">
                <div class="filter-select-wrap">
                    <select id="filterLevel">
                        <option value="">Semua Level</option>
                        <option value="Pemula">Pemula</option>
                        <option value="Menengah">Menengah</option>
                        <option value="Mahir">Mahir</option>
                    </select>
                </div>
                <div class="filter-select-wrap">
                    <select id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div class="search-box">
                    <input type="text" id="globalSearch" placeholder="Cari nama, program...">
                </div>
                <a href="tambah_peserta.php" class="btn btn-primary"><?= icon('plus', 16) ?> Tambah Siswa</a>
            </div>
        </div>

        <div class="table-container">
            <?php if ($result && $result->num_rows > 0): ?>
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>Kontak</th>
                        <th>Level</th>
                        <th>Program & Jadwal</th>
                        <th>Kehadiran</th>
                        <th>Status</th>
                        <th>Tgl Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 0; while ($row = $result->fetch_assoc()):
                        $init        = strtoupper(substr($row['full_name'], 0, 1));
                        $level       = $row['level'] ?? 'Beginner';
                        $kehadiran   = (int)($row['kehadiran'] ?? 0);
                        $att_color   = $kehadiran >= 85 ? 'att-high' : ($kehadiran >= 70 ? 'att-mid' : 'att-low');
                    ?>
                    <tr data-level="<?= htmlspecialchars($level) ?>" data-status="<?= htmlspecialchars($row['status']) ?>">
                        <td><span class="id-badge"><?= ++$no ?></span></td>
                        <td>
                            <div class="student-cell">
                                <div class="student-avatar"><?= $init ?></div>
                                <div class="student-info">
                                    <span class="student-name"><?= htmlspecialchars($row['full_name']) ?></span>
                                    <span class="student-id">ID: ST-<?= str_pad($row['id_peserta'], 4, '0', STR_PAD_LEFT) ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="contact-cell">
                                <span class="contact-wa"><?= htmlspecialchars($row['whatsapp']) ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="level-badge level-<?= strtolower($level) ?>"><?= $level ?></span>
                        </td>
                        <td>
                            <div class="class-cell">
                                <span class="class-name"><?= htmlspecialchars($row['program']) ?></span>
                                <span class="class-schedule"><?= htmlspecialchars($row['schedule']) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="attendance-cell">
                                <div class="attendance-bar <?= $att_color ?>">
                                    <div class="attendance-fill" style="width:<?= $kehadiran ?>%"></div>
                                </div>
                                <span class="attendance-text"><?= $kehadiran ?>%</span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?= strtolower($row['status']) ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <div class="actions">
                                <a href="edit_pendaftaran.php?id_peserta=<?= $row['id_peserta'] ?>"
                                   class="action-btn action-edit" title="Edit">
                                    <?= icon('pencil', 14) ?>
                                </a>
                                <a href="data_siswa.php?action=delete&id_peserta=<?= $row['id_peserta'] ?>"
                                   class="action-btn action-delete" title="Hapus"
                                   onclick="return confirm('Hapus data peserta <?= htmlspecialchars($row['full_name'], ENT_QUOTES) ?>?')">
                                    <?= icon('trash', 14) ?>
                                </a>
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
                <a href="tambah_peserta.php" class="btn btn-primary" style="margin-top:10px;">
                    <?= icon('plus', 14) ?> Tambah Siswa Pertama
                </a>
            </div>
            <?php endif; ?>
        </div>

        <div class="table-footer">
            <div class="table-info">Showing <span id="showingInfo">0</span> of <span id="totalEntries">0</span> entries</div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

</main>
</div>

<?php require_once 'partials/scripts.php'; ?>
<script src="../assets/js/data_siswa.js"></script>
</body>
</html>
