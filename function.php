<?php

include 'config/database.php';
// ============= Fungsi-Fungsi Pembantu =============

// Fungsi untuk membersihkan input
function bersihkan_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

// Fungsi untuk mengambil semua pendaftaran
function ambil_semua_pendaftaran() {
    global $conn;
    $sql = "SELECT * FROM registrations ORDER BY created_at DESC";
    $result = $conn->query($sql);
    return $result;
}

// Fungsi untuk mengambil pendaftaran berdasarkan ID
function ambil_pendaftaran_by_id($id) {
    global $conn;
    $id = bersihkan_input($id);
    $sql = "SELECT * FROM registrations WHERE id = '$id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Fungsi untuk menyimpan pendaftaran baru
function simpan_pendaftaran($data) {
    global $conn;
    
    $full_name = bersihkan_input($data['full_name']);
    $age       = bersihkan_input($data['age']);
    $gender    = bersihkan_input($data['gender']);
    $whatsapp  = bersihkan_input($data['whatsapp']);
    $address   = bersihkan_input($data['address']);
    $program   = bersihkan_input($data['program']);
    $schedule  = bersihkan_input($data['schedule']);
    
    $sql = "INSERT INTO registrations (full_name, age, gender, whatsapp, address, program, schedule) 
            VALUES ('$full_name', '$age', '$gender', '$whatsapp', '$address', '$program', '$schedule')";
    
    if ($conn->query($sql)) {
        return $conn->insert_id;
    }
    return false;
}

// Fungsi untuk memperbarui pendaftaran
function perbarui_pendaftaran($id, $data) {
    global $conn;
    
    $id        = bersihkan_input($id);
    $full_name = bersihkan_input($data['full_name']);
    $age       = bersihkan_input($data['age']);
    $gender    = bersihkan_input($data['gender']);
    $whatsapp  = bersihkan_input($data['whatsapp']);
    $address   = bersihkan_input($data['address']);
    $program   = bersihkan_input($data['program']);
    $schedule  = bersihkan_input($data['schedule']);
    $status    = isset($data['status']) ? bersihkan_input($data['status']) : 'Pending';
    $notes     = isset($data['notes']) ? bersihkan_input($data['notes']) : '';
    
    $sql = "UPDATE registrations SET 
            full_name = '$full_name',
            age = '$age',
            gender = '$gender',
            whatsapp = '$whatsapp',
            address = '$address',
            program = '$program',
            schedule = '$schedule',
            status = '$status',
            notes = '$notes'
            WHERE id = '$id'";
    
    return $conn->query($sql);
}

// Fungsi untuk menghapus pendaftaran
function hapus_pendaftaran($id) {
    global $conn;
    $id = bersihkan_input($id);
    $sql = "DELETE FROM registrations WHERE id = '$id'";
    return $conn->query($sql);
}

// Fungsi untuk menghitung pendaftaran berdasarkan status
function hitung_pendaftaran_by_status($status) {
    global $conn;
    $status = bersihkan_input($status);
    $sql = "SELECT COUNT(*) as jumlah FROM registrations WHERE status = '$status'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['jumlah'];
}

// Fungsi untuk mengambil statistik pendaftaran
function ambil_statistik_pendaftaran() {
    global $conn;
    $statistik = array();
    
    // Total pendaftaran
    $sql = "SELECT COUNT(*) as total FROM registrations";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $statistik['total'] = $row['total'];
    
    // Menunggu
    $statistik['pending']   = hitung_pendaftaran_by_status('Pending');
    
    // Disetujui
    $statistik['approved']  = hitung_pendaftaran_by_status('Approved');
    
    // Ditolak
    $statistik['rejected']  = hitung_pendaftaran_by_status('Rejected');
    
    // Selesai
    $statistik['completed'] = hitung_pendaftaran_by_status('Completed');
    
    return $statistik;
}
?>
