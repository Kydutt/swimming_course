<?php

include 'config/conn.php';
// ============= Fungsi-Fungsi Pembantu =============

function bersihkan_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

function ambil_semua_pendaftaran() {
    global $conn;
    $sql = "SELECT * FROM peserta ORDER BY id_peserta ASC";
    $result = $conn->query($sql);
    return $result;
}

function ambil_pendaftaran_by_id($id_peserta) {
    global $conn;
    $id_peserta = bersihkan_input($id_peserta);
    $sql = "SELECT * FROM peserta WHERE id_peserta = '$id_peserta'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function simpan_pendaftaran($data) {
    global $conn;
    
    $full_name = bersihkan_input($data['full_name']);
    $age       = bersihkan_input($data['age']);
    $gender    = bersihkan_input($data['gender']);
    $whatsapp  = bersihkan_input($data['whatsapp']);
    $address   = bersihkan_input($data['address']);
    $program   = bersihkan_input($data['program']);
    $schedule  = bersihkan_input($data['schedule']);

    $id_program = 'NULL';
    $sql_prog = "SELECT id_program FROM program WHERE nama_program = '$program' LIMIT 1";
    $res_prog = $conn->query($sql_prog);
    if ($res_prog && $row_prog = $res_prog->fetch_assoc()) {
        $id_program = $row_prog['id_program'];
    }

    $id_jadwal = 'NULL';
    $sql_jdw = "SELECT id_jadwal FROM jadwal WHERE keterangan = '$schedule' LIMIT 1";
    $res_jdw = $conn->query($sql_jdw);
    if ($res_jdw && $row_jdw = $res_jdw->fetch_assoc()) {
        $id_jadwal = $row_jdw['id_jadwal'];
    }
    
    $sql = "INSERT INTO peserta (full_name, age, gender, whatsapp, address, program, schedule, id_program, id_jadwal) 
            VALUES ('$full_name', '$age', '$gender', '$whatsapp', '$address', '$program', '$schedule', $id_program, $id_jadwal)";
    
    if ($conn->query($sql)) {
        return $conn->insert_id;
    }
    return false;
}

function perbarui_pendaftaran($id_peserta, $data) {
    global $conn;
    
    $id_peserta= bersihkan_input($id_peserta);
    $full_name = bersihkan_input($data['full_name']);
    $age       = bersihkan_input($data['age']);
    $gender    = bersihkan_input($data['gender']);
    $whatsapp  = bersihkan_input($data['whatsapp']);
    $address   = bersihkan_input($data['address']);
    $program   = bersihkan_input($data['program']);
    $schedule  = bersihkan_input($data['schedule']);
    $status    = isset($data['status']) ? bersihkan_input($data['status']) : 'Pending';
    $notes     = isset($data['notes']) ? bersihkan_input($data['notes']) : '';
    
    $sql = "UPDATE peserta SET 
            full_name = '$full_name',
            age = '$age',
            gender = '$gender',
            whatsapp = '$whatsapp',
            address = '$address',
            program = '$program',
            schedule = '$schedule',
            status = '$status',
            notes = '$notes'
            WHERE id_peserta = '$id_peserta'";
    
    return $conn->query($sql);
}

// Fungsi untuk menghapus pendaftaran
function hapus_pendaftaran($id_peserta) {
    global $conn;
    $id_peserta = bersihkan_input($id_peserta);
    return $conn->query("DELETE FROM peserta WHERE id_peserta = '$id_peserta'");
}

function hitung_pendaftaran_by_status($status) {
    global $conn;
    $status = bersihkan_input($status);
    $sql = "SELECT COUNT(*) as jumlah FROM peserta WHERE status = '$status'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['jumlah'];
}

function ambil_statistik_pendaftaran() {
    global $conn;
    $statistik = array();

    $sql = "SELECT COUNT(*) as total FROM peserta";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $statistik['total'] = $row['total'];

    $statistik['pending']   = hitung_pendaftaran_by_status('Pending');

    $statistik['approved']  = hitung_pendaftaran_by_status('Approved');

    $statistik['rejected']  = hitung_pendaftaran_by_status('Rejected');

    $statistik['completed'] = hitung_pendaftaran_by_status('Completed');
    
    return $statistik;
}

function ambil_semua_program() {
    global $conn;
    $sql = "SELECT * FROM program WHERE is_active = 1 ORDER BY id_program ASC";
    $result = $conn->query($sql);
    return $result;
}

function ambil_program_by_id($id_program) {
    global $conn;
    $id_program = bersihkan_input($id_program);
    $sql = "SELECT * FROM program WHERE id_program = '$id_program'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function ambil_semua_jadwal() {
    global $conn;
    $sql = "SELECT * FROM jadwal WHERE is_active = 1 ORDER BY id_jadwal ASC";
    $result = $conn->query($sql);
    return $result;
}

function ambil_jadwal_by_id($id_jadwal) {
    global $conn;
    $id_jadwal = bersihkan_input($id_jadwal);
    $sql = "SELECT * FROM jadwal WHERE id_jadwal = '$id_jadwal'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Tambah jadwal baru
function tambah_jadwal($hari, $waktu_mulai, $waktu_selesai, $keterangan) {
    global $conn;
    $hari        = $conn->real_escape_string(trim($hari));
    $waktu_mulai = $conn->real_escape_string(trim($waktu_mulai));
    $waktu_selesai = $conn->real_escape_string(trim($waktu_selesai));
    $ket         = $conn->real_escape_string(trim($keterangan));
    $sql = "INSERT INTO jadwal (hari, waktu_mulai, waktu_selesai, keterangan, is_active)
            VALUES ('$hari', '$waktu_mulai', '$waktu_selesai', '$ket', 1)";
    return $conn->query($sql) ? $conn->insert_id : false;
}

// Update jadwal
function update_jadwal($id, $hari, $waktu_mulai, $waktu_selesai, $keterangan, $is_active) {
    global $conn;
    $id          = (int)$id;
    $hari        = $conn->real_escape_string(trim($hari));
    $waktu_mulai = $conn->real_escape_string(trim($waktu_mulai));
    $waktu_selesai = $conn->real_escape_string(trim($waktu_selesai));
    $ket         = $conn->real_escape_string(trim($keterangan));
    $is_active   = (int)$is_active;
    $sql = "UPDATE jadwal SET hari='$hari', waktu_mulai='$waktu_mulai', waktu_selesai='$waktu_selesai',
            keterangan='$ket', is_active=$is_active WHERE id_jadwal=$id";
    return $conn->query($sql);
}

// Hapus jadwal
function hapus_jadwal($id) {
    global $conn;
    $id = (int)$id;
    return $conn->query("DELETE FROM jadwal WHERE id_jadwal=$id");
}

// Toggle aktif jadwal
function toggle_jadwal($id) {
    global $conn;
    $id = (int)$id;
    return $conn->query("UPDATE jadwal SET is_active = IF(is_active=1,0,1) WHERE id_jadwal=$id");
}

// ============= Fungsi Program CRUD =============

// Tambah program baru
function tambah_program($nama, $harga, $jumlah_pertemuan, $deskripsi) {
    global $conn;
    $nama             = $conn->real_escape_string(trim($nama));
    $harga            = (int)$harga;
    $jumlah_pertemuan = (int)$jumlah_pertemuan;
    $deskripsi        = $conn->real_escape_string(trim($deskripsi));
    $sql = "INSERT INTO program (nama_program, harga, jumlah_pertemuan, deskripsi, is_active)
            VALUES ('$nama', $harga, $jumlah_pertemuan, '$deskripsi', 1)";
    return $conn->query($sql) ? $conn->insert_id : false;
}

// Update program
function update_program($id, $nama, $harga, $jumlah_pertemuan, $deskripsi, $is_active) {
    global $conn;
    $id               = (int)$id;
    $nama             = $conn->real_escape_string(trim($nama));
    $harga            = (int)$harga;
    $jumlah_pertemuan = (int)$jumlah_pertemuan;
    $deskripsi        = $conn->real_escape_string(trim($deskripsi));
    $is_active        = (int)$is_active;
    $sql = "UPDATE program SET nama_program='$nama', harga=$harga, jumlah_pertemuan=$jumlah_pertemuan,
            deskripsi='$deskripsi', is_active=$is_active WHERE id_program=$id";
    return $conn->query($sql);
}

// Hapus program
function hapus_program($id) {
    global $conn;
    $id = (int)$id;
    return $conn->query("DELETE FROM program WHERE id_program=$id");
}

// Toggle aktif program
function toggle_program($id) {
    global $conn;
    $id = (int)$id;
    return $conn->query("UPDATE program SET is_active = IF(is_active=1,0,1) WHERE id_program=$id");
}

// Ambil semua program (termasuk nonaktif, untuk admin)
function ambil_semua_program_admin() {
    global $conn;
    $sql = "SELECT * FROM program ORDER BY id_program ASC";
    $result = $conn->query($sql);
    return $result;
}

// Ambil semua jadwal (termasuk nonaktif, untuk admin)
function ambil_semua_jadwal_admin() {
    global $conn;
    $sql = "SELECT * FROM jadwal ORDER BY id_jadwal ASC";
    $result = $conn->query($sql);
    return $result;
}


// ============= Fungsi Feedback / Testimoni =============

// Simpan feedback baru
function simpan_feedback($id_user, $rating, $komentar) {
    global $conn;
    $rating   = max(1, min(5, (int)$rating));
    $komentar = trim($komentar);
    if (empty($komentar)) return ['success' => false, 'message' => 'Komentar tidak boleh kosong.'];

    // Cek apakah user sudah pernah memberi feedback sebelumnya
    $cek = $conn->prepare("SELECT id_feedback FROM feedback WHERE id_user = ?");
    $cek->bind_param("i", $id_user);
    $cek->execute();
    $cek->store_result();
    if ($cek->num_rows > 0) {
        // Update feedback yang sudah ada
        $stmt = $conn->prepare("UPDATE feedback SET rating=?, komentar=?, is_approved=1, created_at=NOW() WHERE id_user=?");
        $stmt->bind_param("isi", $rating, $komentar, $id_user);
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (id_user, rating, komentar) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_user, $rating, $komentar);
    }
    $cek->close();

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Terima kasih! Feedback kamu berhasil disimpan.'];
    }
    $stmt->close();
    return ['success' => false, 'message' => 'Gagal menyimpan feedback.'];
}

// Ambil semua feedback yang sudah diapprove (untuk landing page)
function ambil_feedback_approved($limit = 6) {
    global $conn;
    $limit = (int)$limit;
    $sql = "SELECT f.*, u.name 
            FROM feedback f 
            JOIN user u ON f.id_user = u.id_user 
            WHERE f.is_approved = 1 
            ORDER BY f.created_at DESC 
            LIMIT $limit";
    return $conn->query($sql);
}

// ============= Fungsi SVG / Icons =============
function icon(string $name, int $size = 20, string $cls = ''): string {
    $paths = [
        // Navigation & UI
        'dashboard'   => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'users'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>',
        'user'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>',
        'book'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>',
        'clipboard'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>',
        'calendar'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>',
        'credit-card' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>',
        'chart-bar'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>',
        'logout'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>',
        'check'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'clock'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'x-circle'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'graduation'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>',
        'money'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'document'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>',
        'save'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
        'swim'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5a2 2 0 100 4 2 2 0 000-4zM5 14c2 0 3-1.5 5-1.5S13 14 15 14s3-1.5 5-1.5M5 18c2 0 3-1.5 5-1.5s3 1.5 5 1.5 3-1.5 4-1.5M7 12l3-3 2 2 3-4"/>',
        'plus'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>',
        'pencil'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>',
        'trash'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>',
        'key'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>',
        'archive'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>',
        'inbox'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.235 2.235 0 00-.1.661z"/>',
        'home'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>',
        'shield-user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>',
        'bell'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>' ,
        'search'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>',
        'help-circle' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>',
        // NEW ICONS added for emoji replacement
        'chat'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.139 2.25 6.741v6.018Z"/>',
        'warning'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z"/>',
        'lock'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>',
        'wave'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.998c0-.795.539-1.464 1.253-1.621 1.12-.246 2.298-.246 3.418 0 .714.157 1.253.826 1.253 1.621 0 .795.539 1.464 1.253 1.621 1.12.246 2.298.246 3.418 0 .714-.157 1.253-.826 1.253-1.621 0-.795.539-1.464 1.253-1.621 1.12-.246 2.298-.246 3.418 0 .714.157 1.253.826 1.253 1.621M3 19.998c0-.795.539-1.464 1.253-1.621 1.12-.246 2.298-.246 3.418 0 .714.157 1.253.826 1.253 1.621 0 .795.539 1.464 1.253 1.621 1.12.246 2.298.246 3.418 0 .714-.157 1.253-.826 1.253-1.621 0-.795.539-1.464 1.253-1.621 1.12-.246 2.298-.246 3.418 0 .714.157 1.253.826 1.253 1.621"/>',
        'eye'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.638 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />',
        'eye-off'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />',
        'phone'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.25-3.95-6.847-6.847l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />',
        'pin'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />',
        'star'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />',
        'lightning'   => '<path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />',
        'target'      => '<circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round" /><circle cx="12" cy="12" r="5" stroke-linecap="round" stroke-linejoin="round" /><circle cx="12" cy="12" r="1.5" stroke-linecap="round" stroke-linejoin="round" />',
        'file-text'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />'
    ];

    $p = $paths[$name] ?? '<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>';
    $c = trim("svg-icon $cls");
    return "<svg class=\"$c\" width=\"$size\" height=\"$size\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">$p</svg>";
}
