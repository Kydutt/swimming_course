<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Get admin info from session
$admin_name = $_SESSION['user_name'] ?? 'Admin';
$admin_email = $_SESSION['user_email'] ?? '';

require_once 'function.php';

// Get all registrations
$registrations = get_all_registrations();
$stats = get_registration_stats();

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    if (delete_registration($delete_id)) {
        header('Location: admin_dashboard.php?success=deleted');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Swimming Course</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>
    <div class="header">
        <div style="max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1> Admin Dashboard - Swimming Course</h1>
                <p>Kelola data pendaftaran peserta kursus renang</p>
            </div>
            <div style="text-align: right;">
                <p style="margin-bottom: 0.5rem; font-size: 0.9rem;">
                     <strong><?php echo htmlspecialchars($admin_name); ?></strong><br>
                    <span style="font-size: 0.8rem; opacity: 0.9;"><?php echo htmlspecialchars($admin_email); ?></span>
                </p>
                <a href="logout.php" class="btn" style="background: #ef4444; color: white; padding: 0.5rem 1rem; font-size: 0.9rem; display: inline-block; margin-top: 0.5rem;">
                     Logout
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                    if ($_GET['success'] === 'deleted') {
                        echo '✓ Data berhasil dihapus!';
                    } elseif ($_GET['success'] === 'updated') {
                        echo '✓ Data berhasil diupdate!';
                    }
                ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Pendaftaran</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number"><?php echo $stats['pending']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card approved">
                <div class="stat-number"><?php echo $stats['approved']; ?></div>
                <div class="stat-label">Disetujui</div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-number"><?php echo $stats['rejected']; ?></div>
                <div class="stat-label">Ditolak</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-number"><?php echo $stats['completed']; ?></div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>

        <!-- Registrations Table -->
        <div class="card">
            <div class="card-header">
                <h2>Daftar Pendaftaran</h2>
                <a href="index.php#registration" class="btn btn-primary">+ Tambah Pendaftaran</a>
            </div>
            
            <div class="table-container">
                <?php if ($registrations && $registrations->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Lengkap</th>
                                <th>Umur</th>
                                <th>Gender</th>
                                <th>WhatsApp</th>
                                <th>Program</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $registrations->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                                    <td><?php echo $row['age']; ?> tahun</td>
                                    <td><?php echo $row['gender']; ?></td>
                                    <td><?php echo $row['whatsapp']; ?></td>
                                    <td><?php echo $row['program']; ?></td>
                                    <td><?php echo $row['schedule']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="edit_registration.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-edit">Edit</a>
                                            <a href="?action=delete&id=<?php echo $row['id']; ?>" 
                                               class="btn btn-delete"
                                               onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <p>Belum ada data pendaftaran.</p>
                        <a href="index.php#registration" class="btn btn-primary" style="margin-top: 1rem;">Tambah Pendaftaran Pertama</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
