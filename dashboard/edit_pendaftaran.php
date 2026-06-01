<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: ../proses/login.php');
    exit;
}

require_once '../function.php';

$current_page = 'data_siswa';

if (!isset($_GET['id_peserta'])) {
    header('Location: data_siswa.php');
    exit;
}

$id_peserta  = (int)$_GET['id_peserta'];
$pendaftaran = ambil_pendaftaran_by_id($id_peserta);

if (!$pendaftaran) {
    header('Location: data_siswa.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_perbarui = [
        'full_name' => $_POST['full_name'] ?? '',
        'age'       => $_POST['age']       ?? '',
        'gender'    => $_POST['gender']    ?? '',
        'whatsapp'  => $_POST['whatsapp']  ?? '',
        'address'   => $_POST['address']   ?? '',
        'program'   => $_POST['program']   ?? '',
        'schedule'  => $_POST['schedule']  ?? '',
        'status'    => $_POST['status']    ?? 'Menunggu',
        'notes'     => $_POST['notes']     ?? '',
        'level'     => $_POST['level']     ?? 'Pemula',
        'kehadiran' => $_POST['kehadiran'] ?? 0,
    ];

    if (empty($data_perbarui['full_name'])) $errors[] = 'Nama lengkap harus diisi.';
    if (empty($data_perbarui['age']) || !is_numeric($data_perbarui['age'])) $errors[] = 'Umur tidak valid.';

    if (empty($errors)) {
        if (perbarui_pendaftaran($id_peserta, $data_perbarui)) {
            header('Location: data_siswa.php?success=updated');
            exit;
        }
        $errors[] = 'Gagal memperbarui data ke database.';
    }
    // Isi ulang data dari POST agar tidak hilang saat error
    $pendaftaran = array_merge($pendaftaran, $data_perbarui);
}

$programs_result = ambil_semua_program();
$jadwal_result   = ambil_semua_jadwal();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peserta - Admin Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/tambah_peserta.css">
</head>
<body>
<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<div class="layout-body">
<main class="page-wrapper">

    <!-- Breadcrumb -->
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:.85rem; color:#94a3b8;">
        <a href="data_siswa.php" style="color:#2563eb; text-decoration:none; font-weight:600;"><?= icon('users', 14) ?> Data Siswa</a>
        <span>›</span>
        <span style="color:#334155; font-weight:600;">Edit Peserta</span>
    </div>

    <div class="form-wrap">
        <div class="form-card">

            <div class="form-card-header">
                <div class="form-card-icon"><?= icon('pencil', 32) ?></div>
                <div>
                    <div class="form-card-title">Edit Data Peserta</div>
                    <div class="form-card-sub">Perbarui informasi peserta: <?= htmlspecialchars($pendaftaran['full_name']) ?></div>
                </div>
            </div>

            <div class="form-body">

                <?php if (!empty($errors)): ?>
                <div class="errors-box">
                    <strong><?= icon('warning', 16) ?> Harap perbaiki kesalahan berikut:</strong>
                    <ul>
                        <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" id="editForm">

                    <!-- Data Pribadi -->
                    <div class="form-section">
                        <div class="section-label"><?= icon('user', 18) ?> Data Pribadi</div>
                        <div class="fg-grid">
                            <div class="fg full">
                                <label for="full_name">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="full_name" name="full_name"
                                       value="<?= htmlspecialchars($pendaftaran['full_name']) ?>" required>
                            </div>
                            <div class="fg">
                                <label for="age">Umur <span style="color:#ef4444;">*</span></label>
                                <input type="number" id="age" name="age" min="4" max="100"
                                       value="<?= (int)$pendaftaran['age'] ?>" required>
                            </div>
                            <div class="fg">
                                <label for="gender">Jenis Kelamin</label>
                                <select id="gender" name="gender" required>
                                    <option value="Laki-laki"  <?= $pendaftaran['gender'] === 'Laki-laki'  ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan"  <?= $pendaftaran['gender'] === 'Perempuan'  ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="fg">
                                <label for="whatsapp">Nomor WhatsApp</label>
                                <input type="tel" id="whatsapp" name="whatsapp"
                                       value="<?= htmlspecialchars($pendaftaran['whatsapp']) ?>">
                            </div>
                            <div class="fg">
                                <label for="address">Alamat</label>
                                <textarea id="address" name="address" rows="3"><?= htmlspecialchars($pendaftaran['address']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Program & Jadwal -->
                    <div class="form-section">
                        <div class="section-label"><?= icon('swim', 18) ?> Program & Jadwal</div>
                        <div class="fg-grid">
                            <div class="fg">
                                <label for="program">Program Kursus</label>
                                <select id="program" name="program" required>
                                    <?php while ($p = $programs_result->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($p['nama_program']) ?>"
                                        <?= $pendaftaran['program'] === $p['nama_program'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nama_program']) ?> — Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="fg">
                                <label for="schedule">Jadwal Latihan</label>
                                <select id="schedule" name="schedule" required>
                                    <?php while ($s = $jadwal_result->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($s['keterangan']) ?>"
                                        <?= $pendaftaran['schedule'] === $s['keterangan'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['keterangan']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Level & Kehadiran -->
                    <div class="form-section">
                        <div class="section-label"><?= icon('graduation', 18) ?> Level & Kehadiran</div>
                        <div class="fg-grid">
                            <div class="fg">
                                <label for="level">Level Kemampuan</label>
                                <select id="level" name="level">
                                    <?php foreach (['Pemula','Menengah','Mahir'] as $lv): ?>
                                    <option value="<?= $lv ?>" <?= ($pendaftaran['level'] ?? 'Pemula') === $lv ? 'selected' : '' ?>><?= $lv ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="fg">
                                <label for="kehadiran">Persentase Kehadiran (%)</label>
                                <input type="number" id="kehadiran" name="kehadiran"
                                       min="0" max="100" placeholder="0–100"
                                       value="<?= (int)($pendaftaran['kehadiran'] ?? 0) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Status & Catatan -->
                    <div class="form-section" style="margin-bottom:0;">
                        <div class="section-label"><?= icon('pin', 18) ?> Status Pendaftaran</div>
                        <input type="hidden" name="status" id="statusInput" value="<?= htmlspecialchars($pendaftaran['status']) ?>">
                        <div class="status-preview">
                            <?php
                            $pills = [
                                'Menunggu'  => ['pill-menunggu',  icon('clock', 14)],
                                'Disetujui' => ['pill-disetujui', icon('check', 14)],
                                'Ditolak'   => ['pill-ditolak',   icon('x-circle', 14)],
                                'Selesai'   => ['pill-selesai',   icon('graduation', 14)],
                            ];
                            foreach ($pills as $st => [$cls, $icn]):
                                $sel = ($pendaftaran['status'] === $st) ? 'selected' : '';
                            ?>
                            <span class="status-pill <?= $cls ?> <?= $sel ?>"
                                  onclick="selectStatus('<?= $st ?>')">
                                <?= $icn ?> <?= $st ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <div class="fg" style="margin-top:16px;">
                            <label for="notes">Catatan</label>
                            <textarea id="notes" name="notes" rows="3"
                                      placeholder="Catatan tambahan (opsional)"><?= htmlspecialchars($pendaftaran['notes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="data_siswa.php" class="btn-cancel-form"><?= icon('x-circle', 16) ?> Batal</a>
                        <button type="submit" class="btn-save">
                            <?= icon('save', 16) ?> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</main>
</div>

<?php require_once 'partials/scripts.php'; ?>
<script src="../assets/js/tambah_peserta.js"></script>
</body>
</html>
