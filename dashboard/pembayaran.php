<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') { header('Location: ../proses/login.php'); exit; }
require_once '../function.php';
global $conn;

$current_page = 'pembayaran';

// JOIN peserta dengan program untuk mendapatkan harga yang akurat dari DB
$sql = "
    SELECT
        p.id_peserta,
        p.full_name,
        p.program,
        p.schedule,
        p.status,
        p.created_at,
        COALESCE(pr.harga, 0) AS harga
    FROM peserta p
    LEFT JOIN program pr ON pr.nama_program = p.program
    ORDER BY p.created_at DESC
";
$result = $conn->query($sql);
$peserta_list = [];
$total_lunas  = 0;
$total_semua  = 0;

while ($row = $result->fetch_assoc()) {
    $row['harga'] = (int)$row['harga'];
    $row['is_lunas'] = in_array($row['status'], ['Disetujui', 'Selesai']);

    // Rejected tidak dihitung dalam angka keuangan apapun
    if ($row['status'] === 'Ditolak') {
        $row['harga'] = 0; // sembunyikan harga untuk Rejected
    } elseif ($row['is_lunas']) {
        $total_lunas += $row['harga'];
        $total_semua += $row['harga'];
    } else {
        $total_semua += $row['harga'];
    }

    $peserta_list[] = $row;
}

$total_belum = $total_semua - $total_lunas;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Admin Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/pembayaran.css">
</head>
<body>
<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>
<?php require_once 'partials/icons.php'; ?>
<div class="layout-body">
<main class="page-wrapper">
    <?php $breadcrumb_title = 'Pembayaran'; require_once 'partials/breadcrumb.php'; ?>
    <h1 class="page-title"><?= icon('credit-card',22) ?> Pembayaran</h1>
    <p class="page-subtitle">Rekap data pembayaran peserta kursus renang</p>

    <!-- Summary cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-icon si-total" style="color:#fff;"><?= icon('money',26) ?></div>
            <div class="summary-info">
                <div class="label">Potensi Total</div>
                <div class="amount">Rp <?= number_format($total_semua, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon si-lunas" style="color:#fff;"><?= icon('check',26) ?></div>
            <div class="summary-info">
                <div class="label">Total Lunas (Approved)</div>
                <div class="amount" style="color:#065f46;">Rp <?= number_format($total_lunas, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon si-belum" style="color:#fff;"><?= icon('clock',26) ?></div>
            <div class="summary-info">
                <div class="label">Belum Lunas</div>
                <div class="amount" style="color:#b45309;">Rp <?= number_format($total_belum, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card">
        <div class="card-header">
            <h2><?= icon('credit-card', 22) ?> Data Pembayaran Peserta</h2>
            <div class="card-header-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Cari nama, program...">
                </div>
            </div>
        </div>
        <div class="table-container">
            <?php if (!empty($peserta_list)): ?>
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Peserta</th>
                        <th>Program</th>
                        <th>Jadwal</th>
                        <th>Biaya Program</th>
                        <th>Status Daftar</th>
                        <th>Status Bayar</th>
                        <th>Tgl Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 0; foreach ($peserta_list as $p):
                        $init = strtoupper(substr($p['full_name'], 0, 1));
                    ?>
                    <tr>
                        <td><span class="id-badge"><?= ++$no ?></span></td>
                        <td>
                            <div class="name-cell">
                                <div class="name-avatar"><?= $init ?></div>
                                <span class="name-text"><?= htmlspecialchars($p['full_name']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($p['program']) ?></td>
                        <td><?= htmlspecialchars($p['schedule']) ?></td>
                        <td>
                            <?php if ($p['status'] === 'Ditolak'): ?>
                                <span style="font-size:.75rem; color:#dc2626; font-weight:600; background:#fee2e2; padding:3px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><?= icon('x-circle', 12) ?> Ditolak</span>
                            <?php elseif ($p['harga'] > 0): ?>
                                <strong style="color:#0f172a;">Rp <?= number_format($p['harga'], 0, ',', '.') ?></strong>
                            <?php else: ?>
                                <span style="color:#94a3b8; font-style:italic;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?= strtolower($p['status']) ?>">
                                <?= $p['status'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($p['is_lunas']): ?>
                                <span class="status-badge status-approved"><?= icon('check', 14) ?> Lunas</span>
                            <?php elseif ($p['status'] === 'Ditolak'): ?>
                                <span class="status-badge status-rejected"><?= icon('x-circle', 14) ?> Batal</span>
                            <?php else: ?>
                                <span class="status-badge status-pending">Belum Bayar</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <!-- Row total di footer tabel -->
                <tfoot>
                    <tr style="background:#f8fafc; font-weight:700;">
                        <td colspan="4" style="padding:14px 16px; color:#334155;">
                            Total Pendapatan (Disetujui/Selesai)
                        </td>
                        <td style="padding:14px 16px; color:#065f46; font-size:1rem;">
                            Rp <?= number_format($total_lunas, 0, ',', '.') ?>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
            <?php else: ?>
            <div class="no-data"><div class="no-data-icon"><?= icon('inbox', 48) ?></div><p>Belum ada data pembayaran.</p></div>
            <?php endif; ?>
        </div>
    </div>
</main>
</div>
<?php require_once 'partials/scripts.php'; ?>
<script src="../assets/js/pembayaran.js"></script>
</body>
</html>
