<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') { header('Location: ../proses/login.php'); exit; }
require_once '../function.php';

$current_page = 'data_kelas';
$errors  = [];

// ── Handle Actions ──

// Hapus
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (hapus_program((int)$_GET['id'])) {
        header('Location: data_kelas.php?success=deleted'); exit;
    }
    $errors[] = 'Gagal menghapus program.';
}

// Toggle aktif
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    toggle_program((int)$_GET['id']);
    header('Location: data_kelas.php?success=toggled'); exit;
}

// Tambah / Edit (POST)
$edit_prog = null;
if (isset($_GET['edit'])) {
    $edit_prog = ambil_program_by_id((int)$_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama             = trim($_POST['nama_program'] ?? '');
    $harga            = (int)str_replace(['.', ',', ' '], '', $_POST['harga'] ?? 0);
    $jumlah_pertemuan = (int)($_POST['jumlah_pertemuan'] ?? 0);
    $deskripsi        = trim($_POST['deskripsi'] ?? '');
    $is_active        = isset($_POST['is_active']) ? 1 : 0;

    if (empty($nama))                   $errors[] = 'Nama program harus diisi.';
    if ($harga < 0)                     $errors[] = 'Harga tidak valid.';
    if ($jumlah_pertemuan < 1)          $errors[] = 'Jumlah pertemuan harus minimal 1.';

    if (empty($errors)) {
        if (isset($_POST['id_program']) && (int)$_POST['id_program'] > 0) {
            $ok = update_program((int)$_POST['id_program'], $nama, $harga, $jumlah_pertemuan, $deskripsi, $is_active);
            if ($ok) { header('Location: data_kelas.php?success=updated'); exit; }
            $errors[] = 'Gagal memperbarui data program.';
        } else {
            $id = tambah_program($nama, $harga, $jumlah_pertemuan, $deskripsi);
            if ($id) { header('Location: data_kelas.php?success=added'); exit; }
            $errors[] = 'Gagal menambah program.';
        }
    }
}

// Flash messages
$flash_map = [
    'added'   => ['Program baru berhasil ditambahkan.', 'alert-success'],
    'updated' => ['Program berhasil diperbarui.',       'alert-success'],
    'deleted' => ['Program berhasil dihapus.',          'alert-success'],
    'toggled' => ['Status program berhasil diubah.',    'alert-success'],
];
$flash = $flash_map[$_GET['success'] ?? ''] ?? null;

// Ambil semua program (admin view)
$result = ambil_semua_program_admin();
$kelas_list = [];
while ($row = $result->fetch_assoc()) { $kelas_list[] = $row; }

$show_panel = isset($_GET['add']) || $edit_prog !== null || !empty($errors);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas - Admin Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/data_kelas.css">
</head>
<body>
<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>
<div class="layout-body">
<main class="page-wrapper">
    <?php $breadcrumb_title = 'Data Kelas'; require_once 'partials/breadcrumb.php'; ?>
    <h1 class="page-title"><?= icon('book',22) ?> Data Kelas</h1>
    <p class="page-subtitle">Kelola paket program / kelas renang — nama, harga, dan pertemuan</p>

    <?php if ($flash): ?>
    <div class="dk-alert alert-success" id="flashMsg">
        <?= icon('check',16) ?> <?= $flash[0] ?>
    </div>
    <?php endif; ?>

    <div class="dk-layout">
        <!-- ── Tabel Program ── -->
        <div class="dk-table-col">
            <div class="card">
                <div class="card-header">
                    <h2><?= icon('book',18) ?> Daftar Kelas / Program (<?= count($kelas_list) ?>)</h2>
                    <div class="card-header-actions">
                        <a href="data_kelas.php?add=1" class="btn btn-primary"><?= icon('plus',14) ?> Tambah Program</a>
                    </div>
                </div>
                <div class="table-container">
                    <?php if (!empty($kelas_list)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Program</th>
                                <th>Harga</th>
                                <th>Pertemuan</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kelas_list as $i => $kelas):
                                $aktif = (int)($kelas['is_active'] ?? 1) === 1;
                            ?>
                            <tr style="<?= $aktif ? '' : 'opacity:.55;' ?>">
                                <td><span class="id-badge"><?= $i + 1 ?></span></td>
                                <td><span class="name-text"><?= htmlspecialchars($kelas['nama_program']) ?></span></td>
                                <td><span class="price-big">Rp <?= number_format($kelas['harga'], 0, ',', '.') ?></span></td>
                                <td><span class="pertemuan-badge"><?= $kelas['jumlah_pertemuan'] ?>x</span></td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                    title="<?= htmlspecialchars($kelas['deskripsi']) ?>">
                                    <?= htmlspecialchars($kelas['deskripsi']) ?>
                                </td>
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
                                        <a href="data_kelas.php?edit=<?= $kelas['id_program'] ?>" class="btn btn-edit"><?= icon('pencil',13) ?> Edit</a>
                                        <a href="data_kelas.php?action=toggle&id=<?= $kelas['id_program'] ?>"
                                           class="btn" style="background:#fef9c3;color:#92400e;"
                                           title="<?= $aktif ? 'Nonaktifkan' : 'Aktifkan' ?>"><?= icon('clock',13) ?> <?= $aktif ? 'OFF' : 'ON' ?></a>
                                        <a href="data_kelas.php?action=delete&id=<?= $kelas['id_program'] ?>"
                                           class="btn btn-delete"
                                           onclick="return confirm('Hapus program ini? Data siswa yang terkait tidak akan terhapus.')"><?= icon('trash',13) ?></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-data">
                        <div class="no-data-icon"><?= icon('inbox', 48) ?></div>
                        <p>Belum ada data kelas/program.</p>
                        <a href="data_kelas.php?add=1" class="btn btn-primary" style="margin-top:10px;"><?= icon('plus',14) ?> Tambah Program Pertama</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ── Panel Tambah / Edit ── -->
        <?php if ($show_panel): ?>
        <div class="dk-form-col">
            <div class="dk-panel">
                <div class="dk-panel-header">
                    <span class="dk-panel-icon"><?= $edit_prog ? icon('pencil',20) : icon('plus',20) ?></span>
                    <span class="dk-panel-title"><?= $edit_prog ? 'Edit Program' : 'Tambah Program Baru' ?></span>
                </div>
                <div class="dk-panel-body">
                    <?php if (!empty($errors)): ?>
                    <div class="dk-errors">
                        <?= icon('warning',14) ?> <strong>Perbaiki:</strong>
                        <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="data_kelas.php">
                        <input type="hidden" name="id_program" value="<?= $edit_prog ? $edit_prog['id_program'] : 0 ?>">

                        <div class="dk-fg">
                            <label>Nama Program</label>
                            <input type="text" name="nama_program" placeholder="Contoh: Kelas Anak-anak" required
                                   value="<?= htmlspecialchars($edit_prog['nama_program'] ?? $_POST['nama_program'] ?? '') ?>">
                        </div>

                        <div class="dk-fg">
                            <label>Harga (Rp)</label>
                            <div class="input-prefix-wrap">
                                <span class="prefix">Rp</span>
                                <input type="number" name="harga" min="0" step="1000" placeholder="0" required
                                       value="<?= $edit_prog ? $edit_prog['harga'] : ($_POST['harga'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="dk-fg">
                            <label>Jumlah Pertemuan</label>
                            <input type="number" name="jumlah_pertemuan" min="1" placeholder="Contoh: 12" required
                                   value="<?= $edit_prog ? $edit_prog['jumlah_pertemuan'] : ($_POST['jumlah_pertemuan'] ?? '') ?>">
                        </div>

                        <div class="dk-fg">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" placeholder="Deskripsi singkat program..."><?= htmlspecialchars($edit_prog['deskripsi'] ?? $_POST['deskripsi'] ?? '') ?></textarea>
                        </div>

                        <div class="toggle-wrap">
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_active" value="1"
                                       <?= ($edit_prog ? (int)($edit_prog['is_active'] ?? 1) : 1) ? 'checked' : '' ?>>
                                <span class="toggle-track"></span>
                            </label>
                            <label>Program Aktif</label>
                        </div>

                        <div style="display:flex; gap:10px; margin-top:20px;">
                            <a href="data_kelas.php" class="btn" style="background:#f1f5f9;color:#475569;flex:1;justify-content:center;"><?= icon('x-circle',14) ?> Batal</a>
                            <button type="submit" class="btn btn-primary" style="flex:2;">
                                <?= $edit_prog ? icon('save',14).' Simpan' : icon('plus',14).' Tambah' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div><!-- .dk-layout -->
</main>
</div>

<?php require_once 'partials/scripts.php'; ?>
<script src="../assets/js/data_kelas.js"></script>
</body>
</html>
