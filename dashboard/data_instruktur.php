<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') { header('Location: ../proses/login.php'); exit; }
require_once '../function.php';

$current_page = 'data_instruktur';
$errors = [];

// ── Handle Actions ──

// Hapus
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $instruktur_hapus = ambil_instruktur_by_id((int)$_GET['id']);
    if ($instruktur_hapus) {
        // Hapus foto jika ada
        if (!empty($instruktur_hapus['foto'])) {
            $foto_full = realpath('../' . $instruktur_hapus['foto']);
            if ($foto_full && file_exists($foto_full)) { unlink($foto_full); }
        }
        hapus_instruktur((int)$_GET['id']);
    }
    header('Location: data_instruktur.php?success=deleted'); exit;
}

// Toggle aktif
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    toggle_instruktur((int)$_GET['id']);
    header('Location: data_instruktur.php?success=toggled'); exit;
}

// Edit: ambil data yang akan diedit
$edit_instruktur = null;
if (isset($_GET['edit'])) {
    $edit_instruktur = ambil_instruktur_by_id((int)$_GET['edit']);
}

// Tambah / Edit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    if (empty($nama)) $errors[] = 'Nama instruktur harus diisi.';
    $umur = (int)($_POST['umur'] ?? 0);
    if ($umur < 18 || $umur > 80) $errors[] = 'Umur tidak valid (18-80 tahun).';

    // Handle upload foto
    $foto_path = null;
    $foto_lama = $_POST['foto_lama'] ?? '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo   = new finfo(FILEINFO_MIME_TYPE);
        $mime    = $finfo->file($_FILES['foto']['tmp_name']);
        if (!in_array($mime, $allowed)) {
            $errors[] = 'Format foto tidak didukung. Gunakan JPG, PNG, atau WebP.';
        } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ukuran foto maksimal 2MB.';
        } else {
            $ext       = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $filename  = 'instruktur_' . time() . '_' . uniqid() . '.' . strtolower($ext);
            $dest_dir  = realpath(__DIR__ . '/../assets/img/instruktur');
            $dest_path = $dest_dir . DIRECTORY_SEPARATOR . $filename;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $dest_path)) {
                $foto_path = 'assets/img/instruktur/' . $filename;
                // Hapus foto lama jika ada
                if (!empty($foto_lama)) {
                    $old_full = realpath(__DIR__ . '/../' . $foto_lama);
                    if ($old_full && file_exists($old_full)) { unlink($old_full); }
                }
            } else {
                $errors[] = 'Gagal mengupload foto. Periksa permission folder.';
            }
        }
    }

    if (empty($errors)) {
        $data = $_POST;
        if (isset($_POST['id_instruktur']) && (int)$_POST['id_instruktur'] > 0) {
            $ok = update_instruktur((int)$_POST['id_instruktur'], $data, $foto_path);
            if ($ok) { header('Location: data_instruktur.php?success=updated'); exit; }
            $errors[] = 'Gagal memperbarui data instruktur.';
            $edit_instruktur = ambil_instruktur_by_id((int)$_POST['id_instruktur']);
        } else {
            $id = tambah_instruktur($data, $foto_path);
            if ($id) { header('Location: data_instruktur.php?success=added'); exit; }
            $errors[] = 'Gagal menambah instruktur.';
        }
    } else {
        if (isset($_POST['id_instruktur']) && (int)$_POST['id_instruktur'] > 0) {
            $edit_instruktur = ambil_instruktur_by_id((int)$_POST['id_instruktur']);
        }
    }
}

// Flash messages
$flash_map = [
    'added'   => ['Instruktur baru berhasil ditambahkan.', 'alert-success'],
    'updated' => ['Data instruktur berhasil diperbarui.', 'alert-success'],
    'deleted' => ['Instruktur berhasil dihapus.',         'alert-success'],
    'toggled' => ['Status instruktur berhasil diubah.',   'alert-success'],
];
$flash = $flash_map[$_GET['success'] ?? ''] ?? null;

// Ambil semua instruktur (admin view)
$result = ambil_semua_instruktur_admin();
$instruktur_list = [];
while ($row = $result->fetch_assoc()) { $instruktur_list[] = $row; }

$show_panel = isset($_GET['add']) || $edit_instruktur !== null || !empty($errors);
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
<div class="layout-body">
<main class="page-wrapper">
    <?php $breadcrumb_title = 'Data Instruktur'; require_once 'partials/breadcrumb.php'; ?>
    <h1 class="page-title"><?= icon('id-card', 22) ?> Data Instruktur</h1>
    <p class="page-subtitle">Kelola biodata pelatih kursus renang</p>

    <?php if ($flash): ?>
    <div class="di-alert alert-success" id="flashMsg">
        <?= icon('check', 16) ?> <?= $flash[0] ?>
    </div>
    <?php endif; ?>

    <div class="di-layout">
        <!-- ── Tabel Instruktur ── -->
        <div class="di-table-col">
            <div class="card">
                <div class="card-header">
                    <h2><?= icon('swim', 18) ?> Daftar Instruktur (<?= count($instruktur_list) ?>)</h2>
                    <div class="card-header-actions">
                        <a href="data_instruktur.php?add=1" class="btn btn-primary">
                            <?= icon('plus', 14) ?> Tambah Instruktur
                        </a>
                    </div>
                </div>
                <div class="table-container">
                    <?php if (!empty($instruktur_list)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Spesialisasi</th>
                                <th>Sertifikasi</th>
                                <th>Telepon</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($instruktur_list as $i => $inst):
                                $aktif = (int)($inst['is_active'] ?? 1) === 1;
                            ?>
                            <tr class="<?= $aktif ? '' : 'row-nonaktif' ?>">
                                <td><span class="id-badge"><?= $i + 1 ?></span></td>
                                <td>
                                    <div class="di-avatar">
                                        <?php if (!empty($inst['foto']) && file_exists('../' . $inst['foto'])): ?>
                                            <img src="../<?= htmlspecialchars($inst['foto']) ?>"
                                                 alt="<?= htmlspecialchars($inst['nama']) ?>">
                                        <?php else: ?>
                                            <div class="di-avatar-initial">
                                                <?= strtoupper(substr($inst['nama'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="di-name-cell">
                                        <span class="name-text"><?= htmlspecialchars($inst['nama']) ?></span>
                                        <?php if ($inst['umur']): ?>
                                            <span class="di-age"><?= $inst['umur'] ?> thn · <?= htmlspecialchars($inst['jenis_kelamin']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($inst['spesialisasi'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($inst['sertifikasi'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($inst['telepon'] ?: '-') ?></td>
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
                                        <a href="data_instruktur.php?edit=<?= $inst['id_instruktur'] ?>"
                                           class="btn btn-edit"><?= icon('pencil', 13) ?> Edit</a>
                                        <a href="data_instruktur.php?action=toggle&id=<?= $inst['id_instruktur'] ?>"
                                           class="btn" style="background:#fef9c3;color:#92400e;"
                                           title="<?= $aktif ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <?= icon('clock', 13) ?> <?= $aktif ? 'OFF' : 'ON' ?>
                                        </a>
                                        <a href="data_instruktur.php?action=delete&id=<?= $inst['id_instruktur'] ?>"
                                           class="btn btn-delete"
                                           onclick="return confirm('Hapus instruktur ini? Foto juga akan dihapus.')">
                                            <?= icon('trash', 13) ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-data">
                        <div class="no-data-icon"><?= icon('swim', 48) ?></div>
                        <p>Belum ada data instruktur.</p>
                        <a href="data_instruktur.php?add=1" class="btn btn-primary" style="margin-top:10px;">
                            <?= icon('plus', 14) ?> Tambah Instruktur Pertama
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ── Panel Tambah / Edit ── -->
        <?php if ($show_panel): ?>
        <div class="di-form-col">
            <div class="di-panel">
                <div class="di-panel-header">
                    <span class="di-panel-icon">
                        <?= $edit_instruktur ? icon('pencil', 20) : icon('plus', 20) ?>
                    </span>
                    <span class="di-panel-title">
                        <?= $edit_instruktur ? 'Edit Instruktur' : 'Tambah Instruktur Baru' ?>
                    </span>
                </div>
                <div class="di-panel-body">
                    <?php if (!empty($errors)): ?>
                    <div class="di-errors">
                        <?= icon('warning', 14) ?> <strong>Perbaiki:</strong>
                        <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="data_instruktur.php" enctype="multipart/form-data" id="formInstruktur">
                        <input type="hidden" name="id_instruktur"
                               value="<?= $edit_instruktur ? $edit_instruktur['id_instruktur'] : 0 ?>">
                        <input type="hidden" name="foto_lama"
                               value="<?= htmlspecialchars($edit_instruktur['foto'] ?? '') ?>">

                        <!-- Preview Foto -->
                        <div class="di-foto-upload">
                            <div class="di-foto-preview" id="fotoPreview">
                                <?php if ($edit_instruktur && !empty($edit_instruktur['foto']) && file_exists('../' . $edit_instruktur['foto'])): ?>
                                    <img id="previewImg" src="../<?= htmlspecialchars($edit_instruktur['foto']) ?>"
                                         alt="Preview">
                                <?php else: ?>
                                    <div class="di-foto-placeholder" id="fotoPlaceholder">
                                        <?= icon('user', 40) ?>
                                        <span>Belum ada foto</span>
                                    </div>
                                    <img id="previewImg" src="" alt="Preview" style="display:none;">
                                <?php endif; ?>
                            </div>
                            <div class="di-foto-actions">
                                <label for="fotoInput" class="btn" style="background:#eff6ff;color:#2563eb;cursor:pointer;">
                                    <?= icon('plus', 14) ?> Pilih Foto
                                </label>
                                <input type="file" id="fotoInput" name="foto" accept="image/*" style="display:none;">
                                <p class="di-foto-hint">JPG, PNG, atau WebP · Maks 2MB</p>
                            </div>
                        </div>

                        <div class="di-fg">
                            <label>Nama Lengkap <span class="di-req">*</span></label>
                            <input type="text" name="nama" placeholder="Contoh: Muhammad Fajri Yusuf" required
                                   value="<?= htmlspecialchars($edit_instruktur['nama'] ?? $_POST['nama'] ?? '') ?>">
                        </div>

                        <div class="di-fg-row">
                            <div class="di-fg">
                                <label>Umur <span class="di-req">*</span></label>
                                <input type="number" name="umur" min="18" max="80" placeholder="28" required
                                       value="<?= htmlspecialchars($edit_instruktur['umur'] ?? $_POST['umur'] ?? '') ?>">
                            </div>
                            <div class="di-fg">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin">
                                    <?php $jk = $edit_instruktur['jenis_kelamin'] ?? $_POST['jenis_kelamin'] ?? 'Laki-laki'; ?>
                                    <option value="Laki-laki" <?= $jk === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= $jk === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="di-fg">
                            <label>Nomor Telepon / WhatsApp</label>
                            <input type="text" name="telepon" placeholder="08xx-xxxx-xxxx"
                                   value="<?= htmlspecialchars($edit_instruktur['telepon'] ?? $_POST['telepon'] ?? '') ?>">
                        </div>

                        <div class="di-fg">
                            <label>Spesialisasi</label>
                            <input type="text" name="spesialisasi" placeholder="Contoh: Gaya Bebas, Gaya Dada"
                                   value="<?= htmlspecialchars($edit_instruktur['spesialisasi'] ?? $_POST['spesialisasi'] ?? '') ?>">
                        </div>

                        <div class="di-fg">
                            <label>Sertifikasi / Lisensi</label>
                            <input type="text" name="sertifikasi" placeholder="Contoh: Lisensi Provinsi PRSI"
                                   value="<?= htmlspecialchars($edit_instruktur['sertifikasi'] ?? $_POST['sertifikasi'] ?? '') ?>">
                        </div>

                        <div class="di-fg">
                            <label><?= icon('certificate', 14) ?> Pengalaman Wasit</label>
                            <textarea name="pengalaman_wasit" rows="4"
                                      placeholder="Satu pengalaman per baris&#10;Contoh:&#10;PON JABAR 2016&#10;POMNAS 2018"
                                      ><?= htmlspecialchars($edit_instruktur['pengalaman_wasit'] ?? $_POST['pengalaman_wasit'] ?? '') ?></textarea>
                            <p class="di-fg-hint">Tulis satu pengalaman per baris</p>
                        </div>

                        <div class="di-fg">
                            <label><?= icon('swim', 14) ?> Pengalaman Melatih</label>
                            <textarea name="pengalaman_melatih" rows="4"
                                      placeholder="Satu tempat per baris&#10;Contoh:&#10;Tirta Wiralodra Swimming Club&#10;Metamorfosa Swimming Club"
                                      ><?= htmlspecialchars($edit_instruktur['pengalaman_melatih'] ?? $_POST['pengalaman_melatih'] ?? '') ?></textarea>
                            <p class="di-fg-hint">Tulis satu tempat per baris</p>
                        </div>

                        <div class="di-fg">
                            <label>Bio Singkat</label>
                            <textarea name="bio" rows="3"
                                      placeholder="Deskripsi singkat tentang instruktur..."
                                      ><?= htmlspecialchars($edit_instruktur['bio'] ?? $_POST['bio'] ?? '') ?></textarea>
                        </div>

                        <div class="toggle-wrap">
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_active" value="1"
                                       <?= ($edit_instruktur ? (int)($edit_instruktur['is_active'] ?? 1) : 1) ? 'checked' : '' ?>>
                                <span class="toggle-track"></span>
                            </label>
                            <label>Instruktur Aktif</label>
                        </div>

                        <div style="display:flex; gap:10px; margin-top:20px;">
                            <a href="data_instruktur.php" class="btn"
                               style="background:#f1f5f9;color:#475569;flex:1;justify-content:center;">
                                <?= icon('x-circle', 14) ?> Batal
                            </a>
                            <button type="submit" class="btn btn-primary" style="flex:2;">
                                <?= $edit_instruktur ? icon('save', 14) . ' Simpan' : icon('plus', 14) . ' Tambah' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div><!-- .di-layout -->
</main>
</div>
<?php require_once 'partials/scripts.php'; ?>
<script src="../assets/js/data_instruktur.js"></script>
</body>
</html>
