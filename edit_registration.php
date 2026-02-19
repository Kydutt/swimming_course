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
$pendaftaran = ambil_pendaftaran_by_id($id);

if (!$pendaftaran) {
    header('Location: admin_dashboard.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_perbarui = [
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
    
    if (perbarui_pendaftaran($id, $data_perbarui)) {
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
    <link rel="stylesheet" href="css/edit_registration.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> Edit Pendaftaran</h1>
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
                            value="<?php echo htmlspecialchars($pendaftaran['full_name']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="age">Umur <span class="required">*</span></label>
                        <input 
                            type="number" 
                            id="age" 
                            name="age" 
                            value="<?php echo $pendaftaran['age']; ?>"
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
                            <option value="Laki-laki" <?php echo $pendaftaran['gender'] === 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="Perempuan" <?php echo $pendaftaran['gender'] === 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp">Nomor WhatsApp <span class="required">*</span></label>
                        <input 
                            type="tel" 
                            id="whatsapp" 
                            name="whatsapp" 
                            value="<?php echo $pendaftaran['whatsapp']; ?>"
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
                    ><?php echo htmlspecialchars($pendaftaran['address']); ?></textarea>
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
                    ><?php echo htmlspecialchars($pendaftaran['notes'] ?? ''); ?></textarea>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary"> Simpan Perubahan</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">← Kembali</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
