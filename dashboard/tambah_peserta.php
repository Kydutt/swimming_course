<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: ../proses/login.php'); exit;
}

require_once '../function.php';

$errors  = [];
$success = false;
$current_page = 'pendaftaran';

$v = [
    'full_name' => '',
    'age'       => '',
    'gender'    => '',
    'whatsapp'  => '',
    'address'   => '',
    'program'   => '',
    'schedule'  => '',
    'status'    => 'Pending',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) {
        $v[$k] = trim($_POST[$k] ?? '');
    }

    if (empty($v['full_name'])) $errors[] = 'Nama lengkap harus diisi.';
    if (empty($v['age']) || !is_numeric($v['age']) || $v['age'] < 1) $errors[] = 'Umur tidak valid.';
    if (empty($v['gender']))   $errors[] = 'Jenis kelamin harus dipilih.';
    if (empty($v['whatsapp'])) $errors[] = 'Nomor WhatsApp harus diisi.';
    if (empty($v['address']))  $errors[] = 'Alamat harus diisi.';
    if (empty($v['program']))  $errors[] = 'Program harus dipilih.';
    if (empty($v['schedule'])) $errors[] = 'Jadwal harus dipilih.';

    if (empty($errors)) {
        $insert_id = simpan_pendaftaran($v);
        if ($insert_id) {
            header('Location: admin_dashboard.php?success=added'); exit;
        } else {
            $errors[] = 'Gagal menyimpan data ke database.';
        }
    }
}

$programs_result = ambil_semua_program();
$programs = [];
while ($p = $programs_result->fetch_assoc()) { $programs[] = $p; }

$jadwal_result = ambil_semua_jadwal();
$jadwals = [];
while ($j = $jadwal_result->fetch_assoc()) { $jadwals[] = $j; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peserta - Admin Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/tambah_peserta.css">
</head>
<body>
<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<div class="layout-body">
<main class="page-wrapper">

    <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:.85rem; color:#94a3b8;">
        <a href="admin_dashboard.php" style="color:#2563eb; text-decoration:none; font-weight:600;">Dashboard</a>
        <span>›</span>
        <span style="color:#334155; font-weight:600;">Tambah Peserta</span>
    </div>

    <div class="form-wrap">
        <div class="form-card">

            <div class="form-card-header">
                <div class="form-card-icon"><?= icon('document', 32) ?></div>
                <div>
                    <div class="form-card-title">Tambah Peserta Baru</div>
                    <div class="form-card-sub">Isi data peserta kursus renang dengan lengkap dan benar</div>
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

                <form method="POST" action="" id="addForm">

                    <div class="form-section">
                        <div class="section-label"><?= icon('user', 18) ?> Data Pribadi</div>
                        <div class="fg-grid">
                            <div class="fg full">
                                <label for="full_name">Nama Lengkap</label>
                                <input type="text" id="full_name" name="full_name"
                                       placeholder="Contoh: Budi Santoso"
                                       value="<?= htmlspecialchars($v['full_name']) ?>" required>
                            </div>
                            <div class="fg">
                                <label for="age">Umur</label>
                                <input type="number" id="age" name="age" min="1" max="99"
                                       placeholder="Tahun" value="<?= htmlspecialchars($v['age']) ?>" required>
                            </div>
                            <div class="fg">
                                <label for="gender">Jenis Kelamin</label>
                                <select id="gender" name="gender" required>
                                    <option value="">— Pilih —</option>
                                    <option value="Laki-laki"  <?= $v['gender']==='Laki-laki'  ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan"  <?= $v['gender']==='Perempuan'  ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="fg">
                                <label for="whatsapp">Nomor WhatsApp</label>
                                <input type="tel" id="whatsapp" name="whatsapp"
                                       placeholder="Contoh: 08123456789"
                                       value="<?= htmlspecialchars($v['whatsapp']) ?>" required>
                            </div>
                            <div class="fg">
                                <label for="address">Alamat</label>
                                <textarea id="address" name="address" placeholder="Alamat lengkap peserta" required><?= htmlspecialchars($v['address']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-label"><?= icon('swim', 18) ?> Program & Jadwal</div>
                        <div class="fg-grid">
                            <div class="fg">
                                <label for="program">Program Kursus</label>
                                <select id="program" name="program" required>
                                    <option value="">— Pilih Program —</option>
                                    <?php foreach ($programs as $p): ?>
                                    <option value="<?= htmlspecialchars($p['nama_program']) ?>"
                                        <?= $v['program']===$p['nama_program'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nama_program']) ?>
                                        — Rp <?= number_format($p['harga'],0,',','.') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="fg">
                                <label for="schedule">Jadwal Latihan</label>
                                <select id="schedule" name="schedule" required>
                                    <option value="">— Pilih Jadwal —</option>
                                    <?php foreach ($jadwals as $j): ?>
                                    <option value="<?= htmlspecialchars($j['keterangan']) ?>"
                                        <?= $v['schedule']===$j['keterangan'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($j['keterangan']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section" style="margin-bottom:0;">
                        <div class="section-label"><?= icon('pin', 18) ?> Status Pendaftaran</div>
                        <input type="hidden" name="status" id="statusInput" value="<?= htmlspecialchars($v['status']) ?>">
                        <div class="status-preview">
                            <?php
                            $pills = [
                                'Pending'   => ['pill-pending',   icon('clock', 14)],
                                'Approved'  => ['pill-approved',  icon('check', 14)],
                                'Rejected'  => ['pill-rejected',  icon('x-circle', 14)],
                                'Completed' => ['pill-completed', icon('graduation', 14)],
                            ];
                            foreach ($pills as $st => [$cls, $icon]):
                                $sel = $v['status'] === $st ? 'selected' : '';
                            ?>
                            <span class="status-pill <?= $cls ?> <?= $sel ?>"
                                  onclick="selectStatus('<?= $st ?>')">
                                <?= $icon ?> <?= $st ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-actions">
                            <a href="admin_dashboard.php" class="btn-cancel-form"><?= icon('x-circle', 16) ?> Batal</a>
                        <button type="submit" class="btn-save">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Simpan Peserta
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
