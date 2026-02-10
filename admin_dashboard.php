<?php
// ===================================================
// Admin Dashboard - View All Registrations (READ)
// ===================================================

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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #2c3e50;
        }

        .header {
            background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
            color: white;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #2563eb;
        }

        .stat-card.pending { border-left-color: #f59e0b; }
        .stat-card.approved { border-left-color: #10b981; }
        .stat-card.rejected { border-left-color: #ef4444; }
        .stat-card.completed { border-left-color: #8b5cf6; }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.95rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 1.5rem;
            color: #1f2937;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-edit {
            background: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f9fafb;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending { background: #fef3c7; color: #b45309; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-completed { background: #e0e7ff; color: #4338ca; }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .no-data {
            padding: 3rem;
            text-align: center;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .table-container {
                font-size: 0.875rem;
            }

            th, td {
                padding: 0.75rem 0.5rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏊 Admin Dashboard - Swimming Course</h1>
        <p>Kelola data pendaftaran peserta kursus renang</p>
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
