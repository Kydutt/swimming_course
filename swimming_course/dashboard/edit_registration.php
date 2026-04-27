<?php

require_once '../function.php';

if (!isset($_GET['id_peserta'])) {
    header('Location: ../dashboard/admin_dashboard.php');
    exit;
}

$id_peserta = $_GET['id_peserta'];
$pendaftaran = ambil_pendaftaran_by_id($id_peserta);

if (!$pendaftaran) {
    header('Location: ../dashboard/admin_dashboard.php');
    exit;
}

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
    
    if (perbarui_pendaftaran($id_peserta, $data_perbarui)) {
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
    <link rel="stylesheet" href="../assets/css/edit_registration.css">
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
                            <?php
                            $programs_result = ambil_semua_program();
                            while ($p = $programs_result->fetch_assoc()):
                            ?>
                            <option value="<?php echo htmlspecialchars($p['nama_program']); ?>" <?php echo $pendaftaran['program'] === $p['nama_program'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nama_program']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="schedule">Jadwal <span class="required">*</span></label>
                        <select id="schedule" name="schedule" required>
                            <?php
                            $jadwal_result = ambil_semua_jadwal();
                            while ($s = $jadwal_result->fetch_assoc()):
                            ?>
                            <option value="<?php echo htmlspecialchars($s['keterangan']); ?>" <?php echo $pendaftaran['schedule'] === $s['keterangan'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['keterangan']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" required>
                        <?php
                        $statuses = ['Pending', 'Approved', 'Rejected', 'Completed'];
                        foreach ($statuses as $st):
                        ?>
                        <option value="<?php echo $st; ?>" <?php echo $pendaftaran['status'] === $st ? 'selected' : ''; ?>>
                            <?php echo $st; ?>
                        </option>
                        <?php endforeach; ?>
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
                    <a href="../dashboard/admin_dashboard.php" class="btn btn-secondary">← Kembali</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
