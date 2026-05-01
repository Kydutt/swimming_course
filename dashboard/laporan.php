<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') { header('Location: ../proses/login.php'); exit; }
require_once '../function.php';
global $conn;

$current_page = 'laporan';

$conn->set_charset('utf8mb4');

// ── Statistik Peserta ──────────────────────────────────────────
$statistik_q = $conn->query("
    SELECT
        COUNT(*)                   AS total,
        SUM(status='Pending')      AS pending,
        SUM(status='Approved')     AS approved,
        SUM(status='Completed')    AS completed,
        SUM(status='Rejected')     AS rejected
    FROM peserta
");
$statistik = $statistik_q->fetch_assoc();
foreach (['total','pending','approved','completed','rejected'] as $k) {
    $statistik[$k] = (int)($statistik[$k] ?? 0);
}

// ── Rekap keuangan ─────────────────────────────────────────────
$keuangan_q = $conn->query("
    SELECT
        COALESCE(SUM(CASE WHEN p.status <> 'Rejected' THEN COALESCE(pr.harga,0) ELSE 0 END),0)               AS total_semua,
        COALESCE(SUM(CASE WHEN p.status IN ('Approved','Completed') THEN COALESCE(pr.harga,0) ELSE 0 END),0) AS total_masuk,
        COALESCE(SUM(CASE WHEN p.status = 'Pending'   THEN COALESCE(pr.harga,0) ELSE 0 END),0)               AS total_pending,
        COUNT(CASE WHEN p.status IN ('Approved','Completed') THEN 1 END) AS jml_lunas,
        COUNT(CASE WHEN p.status = 'Pending'  THEN 1 END)                AS jml_pending,
        COUNT(CASE WHEN p.status = 'Rejected' THEN 1 END)                AS jml_batal
    FROM peserta p LEFT JOIN program pr ON pr.nama_program = p.program
");
$keu = $keuangan_q->fetch_assoc();
foreach ($keu as $k => $v) { $keu[$k] = (int)$v; }

// ── Rekap per program ──────────────────────────────────────────
$prog_q = $conn->query("
    SELECT
        p.program,
        COUNT(*) AS jumlah,
        COALESCE(SUM(CASE WHEN p.status IN ('Approved','Completed') THEN COALESCE(pr.harga,0) ELSE 0 END),0) AS pendapatan
    FROM peserta p LEFT JOIN program pr ON pr.nama_program = p.program
    WHERE p.program IS NOT NULL AND p.program <> ''
    GROUP BY p.program
    ORDER BY jumlah DESC
");
$prog_data = [];
while ($r = $prog_q->fetch_assoc()) { $prog_data[] = $r; }

// ── Pendaftaran & pendapatan 6 bulan terakhir ──────────────────
$bulan_q = $conn->query("
    SELECT
        DATE_FORMAT(tgl, '%b %Y') AS bulan,
        DATE_FORMAT(tgl, '%Y-%m') AS bulan_key,
        COUNT(*) AS jumlah,
        COALESCE(SUM(CASE WHEN status IN ('Approved','Completed') THEN harga ELSE 0 END),0) AS pendapatan
    FROM (
        SELECT p.created_at AS tgl,
               CONVERT(p.status USING utf8mb4) AS status,
               COALESCE(pr.harga,0) AS harga
        FROM peserta p LEFT JOIN program pr ON pr.nama_program = p.program
        WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    ) AS bulanan
    GROUP BY DATE_FORMAT(tgl, '%Y-%m'), DATE_FORMAT(tgl, '%b %Y')
    ORDER BY bulan_key ASC
");
$bulan_data = [];
while ($r = $bulan_q->fetch_assoc()) { $bulan_data[] = $r; }

// ── Riwayat transaksi (50 terbaru) ────────────────────────────
$transaksi_q = $conn->query("
    SELECT CONVERT(p.full_name USING utf8mb4) AS full_name,
           CONVERT(p.program   USING utf8mb4) AS program,
           COALESCE(pr.harga,0)               AS harga,
           CONVERT(p.status    USING utf8mb4) AS status,
           p.created_at
    FROM peserta p LEFT JOIN program pr ON pr.nama_program = p.program
    ORDER BY p.created_at DESC
    LIMIT 50
");
$transaksi = [];
while ($r = $transaksi_q->fetch_assoc()) { $transaksi[] = $r; }

// ── Data untuk Chart.js ────────────────────────────────────────
$chart_bulan_labels   = json_encode(array_column($bulan_data, 'bulan'));
$chart_bulan_jumlah   = json_encode(array_map('intval', array_column($bulan_data, 'jumlah')));
$chart_bulan_pendapatan = json_encode(array_map('intval', array_column($bulan_data, 'pendapatan')));

$chart_prog_labels    = json_encode(array_column($prog_data, 'program'));
$chart_prog_jumlah    = json_encode(array_map('intval', array_column($prog_data, 'jumlah')));
$chart_prog_pendapatan = json_encode(array_map('intval', array_column($prog_data, 'pendapatan')));

$chart_status_data    = json_encode([
    $statistik['pending'],
    $statistik['approved'],
    $statistik['completed'],
    $statistik['rejected'],
]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Admin Swimming Course</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/laporan.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>
<?php require_once 'partials/icons.php'; ?>
<div class="layout-body">
<main class="page-wrapper">
    <?php $breadcrumb_title = 'Laporan'; require_once 'partials/breadcrumb.php'; ?>
    <h1 class="page-title"><?= icon('chart-bar',22) ?> Laporan & Statistik</h1>
    <p class="page-subtitle">Pemantauan perkembangan peserta dan keuangan secara real-time</p>

    <!-- Statistik Peserta -->
    <div class="stats-grid" style="margin-bottom:28px;">
        <div class="stat-card total">   <div class="stat-icon"><?= icon('document',26) ?></div><div class="stat-info"><div class="stat-number"><?= $statistik['total'] ?></div><div class="stat-label">Total Peserta</div></div></div>
        <div class="stat-card approved"><div class="stat-icon"><?= icon('check',26) ?></div><div class="stat-info"><div class="stat-number"><?= $statistik['approved'] ?></div><div class="stat-label">Disetujui</div></div></div>
        <div class="stat-card pending"> <div class="stat-icon"><?= icon('clock',26) ?></div><div class="stat-info"><div class="stat-number"><?= $statistik['pending'] ?></div><div class="stat-label">Menunggu</div></div></div>
        <div class="stat-card completed"><div class="stat-icon"><?= icon('graduation',26) ?></div><div class="stat-info"><div class="stat-number"><?= $statistik['completed'] ?></div><div class="stat-label">Selesai</div></div></div>
        <div class="stat-card rejected"><div class="stat-icon"><?= icon('x-circle',26) ?></div><div class="stat-info"><div class="stat-number"><?= $statistik['rejected'] ?></div><div class="stat-label">Ditolak</div></div></div>
    </div>

    <!-- Rekap Keuangan 4 Kartu -->
    <h2 class="section-heading"><?= icon('money',18) ?> Rekap Keuangan</h2>
    <div class="keu-grid">
        <div class="keu-card masuk">
            <div class="keu-label"><?= icon('check',13) ?> Pendapatan Masuk</div>
            <div class="keu-amount">Rp <?= number_format($keu['total_masuk'],0,',','.') ?></div>
            <div class="keu-count"><?= $keu['jml_lunas'] ?> peserta Approved/Completed</div>
        </div>
        <div class="keu-card pending">
            <div class="keu-label"><?= icon('clock',13) ?> Potensi Pending</div>
            <div class="keu-amount">Rp <?= number_format($keu['total_pending'],0,',','.') ?></div>
            <div class="keu-count"><?= $keu['jml_pending'] ?> peserta menunggu konfirmasi</div>
        </div>
        <div class="keu-card batal">
            <div class="keu-label"><?= icon('x-circle',13) ?> Ditolak Admin</div>
            <div class="keu-amount" style="font-size:.95rem; color:#dc2626;"><?= $keu['jml_batal'] ?> Peserta</div>
            <div class="keu-count">Tidak dihitung dalam pendapatan</div>
        </div>
        <div class="keu-card potensi">
            <div class="keu-label"><?= icon('chart-bar',13) ?> Total Potensi Seluruh</div>
            <div class="keu-amount">Rp <?= number_format($keu['total_semua'],0,',','.') ?></div>
            <div class="keu-count">Seluruh pendaftaran aktif</div>
        </div>
    </div>

    <!-- Income Banner -->
    <div class="income-banner">
        <div class="income-card ic-lunas">
            <div class="ic-icon"><?= icon('money',32) ?></div>
            <div>
                <div class="ic-label">Total Pendapatan Masuk (Approved + Completed)</div>
                <div class="ic-amount">Rp <?= number_format($keu['total_masuk'],0,',','.') ?></div>
            </div>
        </div>
        <div class="income-card ic-pending">
            <div class="ic-icon"><?= icon('clock',32) ?></div>
            <div>
                <div class="ic-label">Potensi Belum Dikonfirmasi (Pending)</div>
                <div class="ic-amount">Rp <?= number_format($keu['total_pending'],0,',','.') ?></div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════ -->
    <!-- ═══  CHART SECTION  ═══════════════════════════════ -->
    <!-- ═══════════════════════════════════════════════════ -->
    <h2 class="section-heading"><?= icon('chart-bar',18) ?> Grafik Perkembangan</h2>

    <!-- Row 1: Line chart pendaftaran + Doughnut status -->
    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="chart-card-head">
                <div>
                    <div class="chart-card-title"><?= icon('chart-bar', 16) ?> Perkembangan Peserta (6 Bulan)</div>
                    <div class="chart-card-sub">Jumlah pendaftaran masuk per bulan</div>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas id="chartPesertaBulan"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <div class="chart-card-head">
                <div>
                    <div class="chart-card-title"><?= icon('target', 16) ?> Distribusi Status Peserta</div>
                    <div class="chart-card-sub">Komposisi status seluruh peserta aktif</div>
                </div>
            </div>
            <div class="chart-wrap chart-wrap--doughnut">
                <div style="width: 100%; height: 100%; max-width: 250px; max-height: 250px; margin: 0 auto; position: relative;">
                    <canvas id="chartStatusPeserta"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Bar keuangan per bulan + Bar program -->
    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="chart-card-head">
                <div>
                    <div class="chart-card-title"><?= icon('money', 16) ?> Pendapatan Keuangan (6 Bulan)</div>
                    <div class="chart-card-sub">Total pendapatan masuk (Approved + Completed) per bulan</div>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas id="chartKeuanganBulan"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <div class="chart-card-head">
                <div>
                    <div class="chart-card-title"><?= icon('swim', 16) ?> Rekap per Program</div>
                    <div class="chart-card-sub">Jumlah peserta & pendapatan berdasarkan program</div>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas id="chartProgram"></canvas>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="card">
        <div class="card-header">
            <h2><?= icon('document', 22) ?> Riwayat Transaksi (50 Terakhir)</h2>
            <div class="card-header-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Cari nama, program...">
                </div>
            </div>
        </div>
        <div class="table-container">
            <?php if (!empty($transaksi)): ?>
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Program</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Tgl Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksi as $t):
                        $init = strtoupper(substr($t['full_name'],0,1));
                        $is_lunas = in_array($t['status'], ['Approved','Completed']);
                    ?>
                    <tr>
                        <td>
                            <div class="name-cell">
                                <div class="name-avatar" style="background: linear-gradient(135deg,#2563eb,#06b6d4); color:#fff;">
                                    <?= $init ?>
                                </div>
                                <span class="name-text"><?= htmlspecialchars($t['full_name']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($t['program']) ?></td>
                        <td>
                            <?php if ($t['status'] === 'Rejected'): ?>
                                <span style="font-size:.75rem; color:#dc2626; font-weight:600; background:#fee2e2; padding:3px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><?= icon('x-circle', 12) ?> Ditolak Admin</span>
                            <?php elseif ((int)$t['harga'] > 0): ?>
                                <strong style="color:<?= $is_lunas ? '#065f46' : '#0f172a' ?>">
                                    Rp <?= number_format((int)$t['harga'],0,',','.') ?>
                                </strong>
                            <?php else: ?><span style="color:#94a3b8;">-</span><?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?= strtolower($t['status']) ?>">
                                <?= $t['status'] ?>
                            </span>
                        </td>
                        <td><?= $t['created_at'] ? date('d/m/Y', strtotime($t['created_at'])) : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data"><div class="no-data-icon"><?= icon('inbox', 48) ?></div><p>Belum ada data transaksi.</p></div>
            <?php endif; ?>
        </div>
    </div>

</main>
</div>
<?php require_once 'partials/scripts.php'; ?>
<script src="../assets/js/laporan.js"></script>
<script>
window.laporanData = {
    bulanLabels:     <?= $chart_bulan_labels ?>,
    bulanJumlah:     <?= $chart_bulan_jumlah ?>,
    bulanPendapatan: <?= $chart_bulan_pendapatan ?>,
    progLabels:      <?= $chart_prog_labels ?>,
    progJumlah:      <?= $chart_prog_jumlah ?>,
    progPendapatan:  <?= $chart_prog_pendapatan ?>,
    statusData:      <?= $chart_status_data ?>
};
</script>
</body>
</html>
