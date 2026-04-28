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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>
<body>

<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<div class="layout-body">
<main class="page-wrapper">

    <div class="page-header-row">
        <div class="page-header-text">
            <h1 class="page-title">Data Siswa</h1>
            <p class="page-subtitle">Kelola data pendaftaran, level, dan informasi kontak siswa</p>
        </div>
        <div class="page-header-actions">
            <div class="filter-select-wrap">
                <select id="filterLevel">
                    <option value="">Semua Level</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>
            </div>
            <div class="filter-select-wrap">
                <select id="filterStatus">
                    <option value="">Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <a href="tambah_peserta.php" class="btn btn-primary"><?= icon('plus', 16) ?> Tambah Siswa Baru</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" id="successAlert">
            <?= icon('check', 16) ?>
            <?php if ($_GET['success'] === 'deleted'): ?> Data berhasil dihapus.
            <?php elseif ($_GET['success'] === 'updated'): ?> Data berhasil diperbarui.
            <?php elseif ($_GET['success'] === 'added'): ?> Siswa baru berhasil ditambahkan.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="table-container">
            <?php if ($result && $result->num_rows > 0): ?>
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>NAMA SISWA</th>
                        <th>KONTAK</th>
                        <th>LEVEL</th>
                        <th>PROGRAM & JADWAL</th>
                        <th>KEHADIRAN</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()):
                        $init = strtoupper(substr($row['full_name'], 0, 1));
                        $levels = ['Beginner', 'Intermediate', 'Advanced'];
                        $level = $levels[$row['id_peserta'] % 3];
                        $attendance = 60 + (($row['id_peserta'] * 17) % 41);
                        $att_color = $attendance >= 85 ? 'att-high' : ($attendance >= 70 ? 'att-mid' : 'att-low');
                    ?>
                    <tr data-level="<?= $level ?>" data-status="<?= $row['status'] ?>">
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
                                <span class="contact-wa"><?= $row['whatsapp'] ?></span>
                            </div>
                        </td>
                        <td><span class="level-badge level-<?= strtolower($level) ?>"><?= $level ?></span></td>
                        <td>
                            <div class="class-cell">
                                <span class="class-name"><?= htmlspecialchars($row['program']) ?></span>
                                <span class="class-schedule"><?= htmlspecialchars($row['schedule']) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="attendance-cell">
                                <div class="attendance-bar <?= $att_color ?>">
                                    <div class="attendance-fill" style="width: <?= $attendance ?>%"></div>
                                </div>
                                <span class="attendance-text"><?= $attendance ?>%</span>
                            </div>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="edit_registration.php?id_peserta=<?= $row['id_peserta'] ?>" class="action-btn action-edit" title="Edit"><?= icon('pencil', 14) ?></a>
                                <a href="admin_dashboard.php?action=delete&id_peserta=<?= $row['id_peserta'] ?>" class="action-btn action-delete" title="Hapus"
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
