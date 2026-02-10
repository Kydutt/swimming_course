<?php
// ===================================================
// Database Connection Configuration
// ===================================================

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Default Laragon password is empty
define('DB_NAME', 'swimming_course');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF8
mysqli_set_charset($conn, "utf8mb4");

// Function to sanitize input
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// Function to get all registrations
function get_all_registrations() {
    global $conn;
    $sql = "SELECT * FROM registrations ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    return $result;
}

// Function to get registration by ID
function get_registration_by_id($id) {
    global $conn;
    $id = sanitize_input($id);
    $sql = "SELECT * FROM registrations WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
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
    
    if (mysqli_query($conn, $sql)) {
        return mysqli_insert_id($conn);
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
    
    return mysqli_query($conn, $sql);
}

// Function to delete registration
function delete_registration($id) {
    global $conn;
    $id = sanitize_input($id);
    $sql = "DELETE FROM registrations WHERE id = '$id'";
    return mysqli_query($conn, $sql);
}

// Function to get registration count by status
function get_registration_count_by_status($status) {
    global $conn;
    $status = sanitize_input($status);
    $sql = "SELECT COUNT(*) as count FROM registrations WHERE status = '$status'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// Function to get registration statistics
function get_registration_stats() {
    global $conn;
    $stats = array();
    
    // Total registrations
    $sql = "SELECT COUNT(*) as total FROM registrations";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
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
