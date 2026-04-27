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
<<<<<<< HEAD
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <style>
        
        .form-wrap {
            max-width: 780px;
            margin: 0 auto;
        }

        .form-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,.07);
            overflow: hidden;
        }

        .form-card-header {
            background: linear-gradient(135deg, #1e40af 0%, #0891b2 100%);
            padding: 28px 36px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .form-card-icon {
            width: 52px;
            height: 52px;
            background: rgba(255,255,255,.18);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
        }

        .form-card-title { font-size: 1.3rem; font-weight: 800; margin-bottom: 2px; }
        .form-card-sub   { font-size: .85rem; opacity: .82; }

        .form-body { padding: 32px 36px; }

        .errors-box {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            font-size: .875rem;
            color: #991b1b;
        }
        .errors-box ul { margin: 8px 0 0 18px; }
        .errors-box li { margin-bottom: 4px; }

        .form-section {
            margin-bottom: 28px;
        }

        .section-label {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .fg-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .fg-grid .full { grid-column: 1 / -1; }

        .fg {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .fg label {
            font-size: .75rem;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .fg input,
        .fg select,
        .fg textarea {
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: .9rem;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            width: 100%;
        }

        .fg input:focus,
        .fg select:focus,
        .fg textarea:focus {
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,.1);
        }

        .fg textarea { resize: vertical; min-height: 90px; }

        .status-preview {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 600;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all .2s;
            user-select: none;
        }

        .status-pill.selected { border-color: currentColor; transform: scale(1.05); }
        .pill-pending   { background:#fef3c7; color:#b45309; }
        .pill-approved  { background:#d1fae5; color:#065f46; }
        .pill-rejected  { background:#fee2e2; color:#991b1b; }
        .pill-completed { background:#ede9fe; color:#5b21b6; }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
            margin-top: 4px;
        }

        .btn-cancel-form {
            padding: 11px 22px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
            color: #475569;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s, border-color .2s;
            cursor: pointer;
        }

        .btn-cancel-form:hover { background: #f1f5f9; border-color: #cbd5e1; }

        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 28px;
            background: linear-gradient(135deg, #1e40af, #0891b2);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 700;
            font-family: 'Segoe UI', system-ui, sans-serif;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37,99,235,.35);
        }

        @media (max-width: 600px) {
            .fg-grid { grid-template-columns: 1fr; }
            .form-body { padding: 24px 20px; }
            .form-card-header { padding: 22px 20px; }
        }
    </style>
=======
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/tambah_peserta.css">
>>>>>>> 658b3ae8dfc1f178ded7dc61b43108045607f608
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
<<<<<<< HEAD
<script>
    function selectStatus(val) {
        document.getElementById('statusInput').value = val;
        document.querySelectorAll('.status-pill').forEach(pill => {
            pill.classList.toggle('selected', pill.textContent.trim().includes(val));
        });
    }

    const curStatus = document.getElementById('statusInput').value;
    document.querySelectorAll('.status-pill').forEach(pill => {
        if (pill.textContent.trim().includes(curStatus)) pill.classList.add('selected');
    });
</script>
=======
<script src="../assets/js/tambah_peserta.js"></script>
>>>>>>> 658b3ae8dfc1f178ded7dc61b43108045607f608
</body>
</html>
