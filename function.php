<?php
// ===================================================
// Database Connection & CRUD Functions
// swimming_course database
// ===================================================

$conn = new mysqli("localhost", "root", "", "swimming_course");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset to UTF8
$conn->set_charset("utf8mb4");

// ============= Helper Functions =============

// Function to sanitize input
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

// Function to get all registrations
function get_all_registrations() {
    global $conn;
    $sql = "SELECT * FROM registrations ORDER BY created_at DESC";
    $result = $conn->query($sql);
    return $result;
}

// Function to get registration by ID
function get_registration_by_id($id) {
    global $conn;
    $id = sanitize_input($id);
    $sql = "SELECT * FROM registrations WHERE id = '$id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Function to insert new registration
function insert_registration($data) {
    global $conn;
    
    $full_name = sanitize_input($data['full_name']);
    $age = sanitize_input($data['age']);
    $gender = sanitize_input($data['gender']);
    $whatsapp = sanitize_input($data['whatsapp']);
    $address = sanitize_input($data['address']);
    $program = sanitize_input($data['program']);
    $schedule = sanitize_input($data['schedule']);
    
    $sql = "INSERT INTO registrations (full_name, age, gender, whatsapp, address, program, schedule) 
            VALUES ('$full_name', '$age', '$gender', '$whatsapp', '$address', '$program', '$schedule')";
    
    if ($conn->query($sql)) {
        return $conn->insert_id;
    }
    return false;
}

// Function to update registration
function update_registration($id, $data) {
    global $conn;
    
    $id = sanitize_input($id);
    $full_name = sanitize_input($data['full_name']);
    $age = sanitize_input($data['age']);
    $gender = sanitize_input($data['gender']);
    $whatsapp = sanitize_input($data['whatsapp']);
    $address = sanitize_input($data['address']);
    $program = sanitize_input($data['program']);
    $schedule = sanitize_input($data['schedule']);
    $status = isset($data['status']) ? sanitize_input($data['status']) : 'Pending';
    $notes = isset($data['notes']) ? sanitize_input($data['notes']) : '';
    
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

// Function to delete registration
function delete_registration($id) {
    global $conn;
    $id = sanitize_input($id);
    $sql = "DELETE FROM registrations WHERE id = '$id'";
    return $conn->query($sql);
}

// Function to get registration count by status
function get_registration_count_by_status($status) {
    global $conn;
    $status = sanitize_input($status);
    $sql = "SELECT COUNT(*) as count FROM registrations WHERE status = '$status'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get registration statistics
function get_registration_stats() {
    global $conn;
    $stats = array();
    
    // Total registrations
    $sql = "SELECT COUNT(*) as total FROM registrations";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $stats['total'] = $row['total'];
    
    // Pending
    $stats['pending'] = get_registration_count_by_status('Pending');
    
    // Approved
    $stats['approved'] = get_registration_count_by_status('Approved');
    
    // Rejected
    $stats['rejected'] = get_registration_count_by_status('Rejected');
    
    // Completed
    $stats['completed'] = get_registration_count_by_status('Completed');
    
    return $stats;
}
?>
