<?php
// ===================================================
// Registration Form Handler API (CREATE)
// ===================================================

// Include database connection and functions
require_once '../function.php';

// Set header for JSON response
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON data from request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Validate required fields
$required_fields = ['fullName', 'age', 'gender', 'whatsapp', 'address', 'program', 'schedule'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
    ]);
    exit;
}

// Prepare data for database
$registration_data = [
    'full_name' => $data['fullName'],
    'age' => $data['age'],
    'gender' => $data['gender'],
    'whatsapp' => $data['whatsapp'],
    'address' => $data['address'],
    'program' => $data['program'],
    'schedule' => $data['schedule']
];

// Insert to database
$insert_id = insert_registration($registration_data);

if ($insert_id) {
    // Success - log untuk debugging
    error_log("Registration successful - ID: " . $insert_id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Pendaftaran berhasil disimpan!',
        'registration_id' => $insert_id,
        'data' => $registration_data
    ]);
} else {
    // Failed - get error from global $conn
    global $conn;
    $error_message = $conn ? $conn->error : 'Connection not available';
    
    // Log error untuk debugging
    error_log("Registration failed: " . $error_message);
    error_log("Data: " . json_encode($registration_data));
    
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan pendaftaran ke database.',
        'error' => $error_message,
        'debug_data' => $registration_data
    ]);
}
?>
