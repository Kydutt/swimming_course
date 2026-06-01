<?php

session_start();

require_once 'function.php';

$errors = [];
$success = false;

$programs_data = ambil_semua_program();
$jadwal_data   = ambil_semua_jadwal();

$programs_list = [];
while ($row = $programs_data->fetch_assoc()) {
    $programs_list[] = $row;
}
$jadwal_list = [];
while ($row = $jadwal_data->fetch_assoc()) {
    $jadwal_list[] = $row;
}
// Ambil data feedback/testimoni
$feedback_result = ambil_feedback_approved(6);
$feedback_list   = [];
while ($fb = $feedback_result->fetch_assoc()) {
    $feedback_list[] = $fb;
}

// Ambil semua instruktur untuk slider biodata
$instruktur_result = ambil_semua_instruktur();
$instruktur_list   = [];
if ($instruktur_result) {
    while ($row = $instruktur_result->fetch_assoc()) {
        $instruktur_list[] = $row;
    }
}
$instruktur_data = $instruktur_list[0] ?? null; // backward compat

// Flash message dari proses feedback
$feedback_flash      = '';
$feedback_flash_type = '';
if (isset($_GET['feedback'])) {
    if ($_GET['feedback'] === 'success') {
        $feedback_flash      = icon('check', 16).' Terima kasih! Feedback kamu berhasil disimpan.';
        $feedback_flash_type = 'success';
    } else {
        $feedback_flash      = icon('warning', 16).' ' . (isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Gagal menyimpan feedback.');
        $feedback_flash_type = 'error';
    }
}

// Proses form pendaftaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $age       = isset($_POST['age'])       ? trim($_POST['age'])       : '';
    $gender    = isset($_POST['gender'])    ? trim($_POST['gender'])    : '';
    $whatsapp  = isset($_POST['whatsapp'])  ? trim($_POST['whatsapp'])  : '';
    $address   = isset($_POST['address'])   ? trim($_POST['address'])   : '';
    $program   = isset($_POST['program'])   ? trim($_POST['program'])   : '';
    $schedule  = isset($_POST['schedule'])  ? trim($_POST['schedule'])  : '';

    if (empty($full_name))                                        $errors[] = "Nama lengkap harus diisi";
    if (empty($age) || !is_numeric($age) || $age < 4 || $age > 100) $errors[] = "Umur tidak valid (4-100 tahun)";
    if (empty($gender))                                           $errors[] = "Jenis kelamin harus dipilih";
    if (empty($whatsapp) || !preg_match('/^[0-9]{10,13}$/', $whatsapp)) $errors[] = "Nomor WhatsApp tidak valid (10-13 digit)";
    if (empty($address) || strlen($address) < 10)                 $errors[] = "Alamat minimal 10 karakter";
    if (empty($program))                                          $errors[] = "Program harus dipilih";
    if (empty($schedule))                                         $errors[] = "Jadwal harus dipilih";

    if (empty($errors)) {
        $registration_data = [
            'full_name' => $full_name,
            'age'       => $age,
            'gender'    => $gender,
            'whatsapp'  => $whatsapp,
            'address'   => $address,
            'program'   => $program,
            'schedule'  => $schedule,
        ];

        $insert_id = simpan_pendaftaran($registration_data);

        if ($insert_id) {
            $admin_whatsapp = '6289654627672';

            $program_prices = [];
            foreach ($programs_list as $prog) {
                $program_prices[$prog['nama_program']] = [
                    'price' => $prog['harga'],
                    'unit'  => $prog['jumlah_pertemuan'] . 'x pertemuan',
                ];
            }

            $selected_price  = isset($program_prices[$program]) ? $program_prices[$program] : ['price' => 0, 'unit' => 'bulan'];
            $price_formatted = 'Rp ' . number_format($selected_price['price'], 0, ',', '.');

            $message  = "*KONFIRMASI PENDAFTARAN KURSUS RENANG*\n\n";
            $message .= "Halo Admin Swimming Course,\n\n";
            $message .= "Saya ingin melakukan RINCIAN BIAYA untuk pendaftaran:\n\n";
            $message .= "*DATA PENDAFTARAN*\n";
            $message .= "ID Pendaftaran: #" . $insert_id . "\n";
            $message .= "Nama: " . $full_name . "\n";
            $message .= "Umur: " . $age . " tahun\n";
            $message .= "Jenis Kelamin: " . $gender . "\n";
            $message .= "No. WhatsApp: " . $whatsapp . "\n";
            $message .= "Alamat: " . $address . "\n\n";
            $message .= "*PROGRAM YANG DIPILIH*\n";
            $message .= "Program: " . $program . "\n";
            $message .= "Jadwal: " . $schedule . "\n\n";
            $message .= "*RINCIAN BIAYA*\n";
            $message .= "Biaya Program: " . $price_formatted . "/" . $selected_price['unit'] . "\n";
            $message .= "Total Biaya: " . $price_formatted . "\n";
            $message .= "Mohon informasi rekening untuk transfer pembayaran.\n\n";
            $message .= "Terima kasih!";

            $encoded_message = urlencode($message);
            $whatsapp_url    = "https://wa.me/" . $admin_whatsapp . "?text=" . $encoded_message;

            header('Location: ' . $whatsapp_url);
            exit;
        } else {
            $errors[] = "Gagal menyimpan data ke database. Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swimming Course - Kursus Renang Profesional</title>
    <meta name="description" content="Belajar renang lebih mudah dan profesional bersama SwimPro. Instruktur berpengalaman, jadwal fleksibel, dan metode pembelajaran modern.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing-complete.css">
</head>
<body>

    <?php require_once 'sections/navbar.php'; ?>

    <?php require_once 'sections/hero.php'; ?>

    <?php require_once 'sections/features.php'; ?>

    <?php require_once 'sections/biodata.php'; ?>

    <?php require_once 'sections/programs.php'; ?>

    <?php require_once 'sections/registration.php'; ?>

    <?php require_once 'sections/testimonial.php'; ?>

    <?php require_once 'sections/footer.php'; ?>

    


    <script src="js/landing-complete.js"></script>
    <script src="assets/js/landing-complete.js"></script>

</body>
</html>
