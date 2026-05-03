<?php
session_start();
require '../function.php';

header('Content-Type: application/json; charset=utf-8');

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
    exit;
}

// Pastikan user sudah login
if (empty($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['role'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk mengirim feedback.']);
    exit;
}

$id_user  = (int)$_SESSION['user_id'];
$rating   = isset($_POST['rating'])   ? (int)$_POST['rating']       : 5;
$komentar = isset($_POST['komentar']) ? trim($_POST['komentar'])     : '';

if (empty($komentar)) {
    echo json_encode(['success' => false, 'message' => 'Komentar tidak boleh kosong.']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating tidak valid.']);
    exit;
}

$result = simpan_feedback($id_user, $rating, $komentar);

if ($result['success']) {
    echo json_encode([
        'success' => true,
        'message' => $result['message'],
        'data'    => [
            'name'       => htmlspecialchars($_SESSION['user_name']),
            'rating'     => $rating,
            'komentar'   => htmlspecialchars($komentar),
            'created_at' => date('d M Y'),
        ],
    ]);
} else {
    echo json_encode(['success' => false, 'message' => $result['message']]);
}
exit;
