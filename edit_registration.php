<?php
// ===================================================
// Edit Registration Form (UPDATE)
// ===================================================

require_once 'function.php';

// Get registration ID from URL
if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$id = $_GET['id'];
$registration = get_registration_by_id($id);

if (!$registration) {
    header('Location: admin_dashboard.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update_data = [
        'full_name' => $_POST['full_name'],
        'age' => $_POST['age'],
        'gender' => $_POST['gender'],
        'whatsapp' => $_POST['whatsapp'],
        'address' => $_POST['address'],
        'program' => $_POST['program'],
        'schedule' => $_POST['schedule'],
        'status' => $_POST['status'],
        'notes' => $_POST['notes']
    ];
    
    if (update_registration($id, $update_data)) {
        header('Location: admin_dashboard.php?success=updated');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pendaftaran - Swimming Course</title>
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
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2rem;
            color: #2563eb;
            margin-bottom: 0.5rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 2rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .required {
            color: #ef4444;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Edit Pendaftaran</h1>
            <p>Update data pendaftaran peserta</p>
        </div>

        <div class="card">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Nama Lengkap <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name" 
                            value="<?php echo htmlspecialchars($registration['full_name']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="age">Umur <span class="required">*</span></label>
                        <input 
                            type="number" 
                            id="age" 
                            name="age" 
                            value="<?php echo $registration['age']; ?>"
                            min="4" 
                            max="100"
                            required
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Jenis Kelamin <span class="required">*</span></label>
                        <select id="gender" name="gender" required>
                            <option value="Laki-laki" <?php echo $registration['gender'] === 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo $registration['gender'] === 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp">Nomor WhatsApp <span class="required">*</span></label>
                        <input 
                            type="tel" 
                            id="whatsapp" 
                            name="whatsapp" 
                            value="<?php echo $registration['whatsapp']; ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat <span class="required">*</span></label>
                    <textarea 
                        id="address" 
                        name="address" 
                        rows="3"
                        required
                    ><?php echo htmlspecialchars($registration['address']); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="program">Program <span class="required">*</span></label>
                        <select id="program" name="program" required>
                            <option value="Kelas Anak-anak" <?php echo $registration['program'] === 'Kelas Anak-anak' ? 'selected' : ''; ?>>Kelas Anak-anak</option>
                            <option value="Kelas Remaja" <?php echo $registration['program'] === 'Kelas Remaja' ? 'selected' : ''; ?>>Kelas Remaja</option>
                            <option value="Kelas Dewasa" <?php echo $registration['program'] === 'Kelas Dewasa' ? 'selected' : ''; ?>>Kelas Dewasa</option>
                            <option value="Kelas Privat" <?php echo $registration['program'] === 'Kelas Privat' ? 'selected' : ''; ?>>Kelas Privat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="schedule">Jadwal <span class="required">*</span></label>
                        <select id="schedule" name="schedule" required>
                            <option value="Pagi (06:00 - 08:00)" <?php echo $registration['schedule'] === 'Pagi (06:00 - 08:00)' ? 'selected' : ''; ?>>Pagi (06:00 - 08:00)</option>
                            <option value="Siang (15:00 - 17:00)" <?php echo $registration['schedule'] === 'Siang (15:00 - 17:00)' ? 'selected' : ''; ?>>Siang (15:00 - 17:00)</option>
                            <option value="Sore (17:00 - 19:00)" <?php echo $registration['schedule'] === 'Sore (17:00 - 19:00)' ? 'selected' : ''; ?>>Sore (17:00 - 19:00)</option>
                            <option value="Senin, Rabu, Jumat (15:00 - 16:00)" <?php echo $registration['schedule'] === 'Senin, Rabu, Jumat (15:00 - 16:00)' ? 'selected' : ''; ?>>Senin, Rabu, Jumat (15:00 - 16:00)</option>
                            <option value="Selasa, Kamis, Sabtu (16:00 - 17:30)" <?php echo $registration['schedule'] === 'Selasa, Kamis, Sabtu (16:00 - 17:30)' ? 'selected' : ''; ?>>Selasa, Kamis, Sabtu (16:00 - 17:30)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" required>
                        <option value="Pending" <?php echo $registration['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?php echo $registration['status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Rejected" <?php echo $registration['status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                        <option value="Completed" <?php echo $registration['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        rows="3"
                        placeholder="Catatan tambahan (opsional)"
                    ><?php echo htmlspecialchars($registration['notes'] ?? ''); ?></textarea>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">← Kembali</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
