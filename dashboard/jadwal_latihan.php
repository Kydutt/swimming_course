<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') { header('Location: ../proses/login.php'); exit; }
require_once '../function.php';

$current_page = 'jadwal';
$errors  = [];
$success = '';

// ── Hari options ──
$hari_opts = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

// ── Handle Actions ──

// Hapus
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (hapus_jadwal((int)$_GET['id'])) {
        header('Location: jadwal_latihan.php?success=deleted'); exit;
    }
    $errors[] = 'Gagal menghapus jadwal.';
}

// Toggle aktif
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    toggle_jadwal((int)$_GET['id']);
    header('Location: jadwal_latihan.php?success=toggled'); exit;
}

// Tambah / Edit (POST)
$edit_jadwal = null;
if (isset($_GET['edit'])) {
    $edit_jadwal = ambil_jadwal_by_id((int)$_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hari          = trim($_POST['hari'] ?? '');
    $waktu_mulai   = trim($_POST['waktu_mulai'] ?? '');
    $waktu_selesai = trim($_POST['waktu_selesai'] ?? '');
    $keterangan    = trim($_POST['keterangan'] ?? '');
    $is_active     = isset($_POST['is_active']) ? 1 : 0;

    if (empty($hari))          $errors[] = 'Hari harus dipilih.';
    if (empty($waktu_mulai))   $errors[] = 'Waktu mulai harus diisi.';
    if (empty($waktu_selesai)) $errors[] = 'Waktu selesai harus diisi.';
    if (empty($keterangan))    $keterangan = "$hari, " . ($waktu_mulai ? date('H:i', strtotime($waktu_mulai)) . ' - ' . date('H:i', strtotime($waktu_selesai)) : '');

    if (empty($errors)) {
        if (isset($_POST['id_jadwal']) && (int)$_POST['id_jadwal'] > 0) {
            // UPDATE
            $ok = update_jadwal((int)$_POST['id_jadwal'], $hari, $waktu_mulai, $waktu_selesai, $keterangan, $is_active);
            if ($ok) { header('Location: jadwal_latihan.php?success=updated'); exit; }
            $errors[] = 'Gagal memperbarui data jadwal.';
        } else {
            // INSERT
            $id = tambah_jadwal($hari, $waktu_mulai, $waktu_selesai, $keterangan);
            if ($id) { header('Location: jadwal_latihan.php?success=added'); exit; }
            $errors[] = 'Gagal menambah jadwal.';
        }
    }
}

// Flash messages
$flash_map = [
    'added'   => ['Jadwal baru berhasil ditambahkan.', 'alert-success'],
    'updated' => ['Jadwal berhasil diperbarui.',       'alert-success'],
    'deleted' => ['Jadwal berhasil dihapus.',          'alert-success'],
    'toggled' => ['Status jadwal berhasil diubah.',    'alert-success'],
];
$flash = $flash_map[$_GET['success'] ?? ''] ?? null;

// Ambil semua jadwal (admin view: aktif + nonaktif)
$result = ambil_semua_jadwal_admin();
$jadwal_list = [];
while ($row = $result->fetch_assoc()) { $jadwal_list[] = $row; }

$show_panel = isset($_GET['add']) || $edit_jadwal !== null || !empty($errors);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Latihan - Admin Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/jadwal_latihan.css">

</head>
<body>
<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>
<div class="layout-body">
<main class="page-wrapper">
    <?php $breadcrumb_title = 'Jadwal Latihan'; require_once 'partials/breadcrumb.php'; ?>
    <h1 class="page-title"><?= icon('calendar',22) ?> Jadwal Latihan</h1>
    <p class="page-subtitle">Kelola jadwal latihan kursus renang</p>

    <?php if ($flash): ?>
    <div class="jl-alert alert-success" id="flashMsg">
        <?= icon('check',16) ?> <?= $flash[0] ?>
    </div>
    <?php endif; ?>

    <div class="jl-layout">
        <!-- ── Tabel Jadwal ── -->
        <div class="jl-table-col">
            <div class="card">
                <div class="card-header">
                    <h2><?= icon('calendar',18) ?> Daftar Jadwal Latihan (<?= count($jadwal_list) ?>)</h2>
                    <div class="card-header-actions">
                        <a href="jadwal_latihan.php?add=1" class="btn btn-primary"><?= icon('plus',14) ?> Tambah Jadwal</a>
                    </div>
                </div>
                <div class="table-container">
                    <?php if (!empty($jadwal_list)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=0; foreach ($jadwal_list as $jadwal):
                                $aktif = (int)($jadwal['is_active'] ?? 1) === 1;
                                $wm    = $jadwal['waktu_mulai']   ?? '';
                                $ws    = $jadwal['waktu_selesai'] ?? '';
                                $waktu_display = ($wm && $ws)
                                    ? date('H:i', strtotime($wm)) . ' – ' . date('H:i', strtotime($ws))
                                    : ($jadwal['waktu'] ?? '-');
                            ?>
                            <tr style="<?= $aktif ? '' : 'opacity:.55;' ?>">
                                <td><span class="id-badge"><?= ++$no ?></span></td>
                                <td><strong><?= htmlspecialchars($jadwal['hari'] ?? '-') ?></strong></td>
                                <td><?= htmlspecialchars($waktu_display) ?></td>
                                <td><?= htmlspecialchars($jadwal['keterangan']) ?></td>
                                <td>
                                    <?php if ($aktif): ?>
                                        <span class="status-badge" style="background:#d1fae5;color:#065f46;">
                                            <span class="status-dot dot-active"></span>Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge" style="background:#f1f5f9;color:#64748b;">
                                            <span class="status-dot dot-inactive"></span>Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="jadwal_latihan.php?edit=<?= $jadwal['id_jadwal'] ?>" class="btn btn-edit"><?= icon('pencil',13) ?> Edit</a>
                                        <a href="jadwal_latihan.php?action=toggle&id=<?= $jadwal['id_jadwal'] ?>"
                                           class="btn" style="background:#fef9c3;color:#92400e;"
                                           title="<?= $aktif ? 'Nonaktifkan' : 'Aktifkan' ?>"><?= icon('clock',13) ?> <?= $aktif ? 'OFF' : 'ON' ?></a>
                                        <a href="jadwal_latihan.php?action=delete&id=<?= $jadwal['id_jadwal'] ?>"
                                           class="btn btn-delete"
                                           onclick="return confirm('Hapus jadwal ini?')"><?= icon('trash',13) ?></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-data">
                        <div class="no-data-icon"><?= icon('inbox', 48) ?></div>
                        <p>Belum ada data jadwal.</p>
                        <a href="jadwal_latihan.php?add=1" class="btn btn-primary" style="margin-top:10px;"><?= icon('plus',14) ?> Tambah Jadwal Pertama</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ── Panel Tambah / Edit ── -->
        <?php if ($show_panel): ?>
        <div class="jl-form-col">
            <div class="jl-panel">
                <div class="jl-panel-header">
                    <span class="jl-panel-icon"><?= $edit_jadwal ? icon('pencil',20) : icon('plus',20) ?></span>
                    <span class="jl-panel-title"><?= $edit_jadwal ? 'Edit Jadwal' : 'Tambah Jadwal Baru' ?></span>
                </div>
                <div class="jl-panel-body">
                    <?php if (!empty($errors)): ?>
                    <div class="jl-errors">
                        <?= icon('warning',14) ?> <strong>Perbaiki:</strong>
                        <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="jadwal_latihan.php">
                        <input type="hidden" name="id_jadwal" value="<?= $edit_jadwal ? $edit_jadwal['id_jadwal'] : 0 ?>">

                        <div class="jl-fg">
                            <label>Hari</label>
                            <select name="hari" required>
                                <option value="">— Pilih Hari —</option>
                                <?php foreach ($hari_opts as $h): ?>
                                <option value="<?= $h ?>" <?= ($edit_jadwal['hari'] ?? '') === $h ? 'selected' : '' ?>><?= $h ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="waktu-row">
                            <div class="jl-fg">
                                <label>Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" required
                                       value="<?= $edit_jadwal ? substr($edit_jadwal['waktu_mulai'] ?? '', 0, 5) : '' ?>">
                            </div>
                            <div class="jl-fg">
                                <label>Waktu Selesai</label>
                                <input type="time" name="waktu_selesai" required
                                       value="<?= $edit_jadwal ? substr($edit_jadwal['waktu_selesai'] ?? '', 0, 5) : '' ?>">
                            </div>
                        </div>

                        <div class="jl-fg">
                            <label>Keterangan</label>
                            <input type="text" name="keterangan" placeholder="Contoh: Jumat, siang (14:00 – 16:00)"
                                   value="<?= htmlspecialchars($edit_jadwal['keterangan'] ?? '') ?>">
                            <small>Kosongkan untuk auto-generate dari hari & waktu.</small>
                        </div>

                        <div class="toggle-wrap">
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_active" value="1"
                                       <?= ($edit_jadwal ? (int)($edit_jadwal['is_active'] ?? 1) : 1) ? 'checked' : '' ?>>
                                <span class="toggle-track"></span>
                            </label>
                            <label>Jadwal Aktif</label>
                        </div>

                        <div style="display:flex; gap:10px; margin-top:20px;">
                            <a href="jadwal_latihan.php" class="btn" style="background:#f1f5f9;color:#475569;flex:1;justify-content:center;"><?= icon('x-circle',14) ?> Batal</a>
                            <button type="submit" class="btn btn-primary" style="flex:2;">
                                <?= $edit_jadwal ? icon('save',14).' Simpan' : icon('plus',14).' Tambah' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div><!-- .jl-layout -->
</main>
</div>

<?php require_once 'partials/scripts.php'; ?>
<script src="../assets/js/jadwal_latihan.js"></script>
</body>
</html>
