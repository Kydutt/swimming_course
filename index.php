<?php
// Start session for messages
session_start();

// Include database connection
require_once 'function.php';

// Initialize variables
$errors = [];
$success = false;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and sanitize form data
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $age = isset($_POST['age']) ? trim($_POST['age']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $whatsapp = isset($_POST['whatsapp']) ? trim($_POST['whatsapp']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $program = isset($_POST['program']) ? trim($_POST['program']) : '';
    $schedule = isset($_POST['schedule']) ? trim($_POST['schedule']) : '';
    
    // Validation
    if (empty($full_name)) {
        $errors[] = "Nama lengkap harus diisi";
    }
    
    if (empty($age) || !is_numeric($age) || $age < 4 || $age > 100) {
        $errors[] = "Umur tidak valid (4-100 tahun)";
    }
    
    if (empty($gender)) {
        $errors[] = "Jenis kelamin harus dipilih";
    }
    
    if (empty($whatsapp) || !preg_match('/^[0-9]{10,13}$/', $whatsapp)) {
        $errors[] = "Nomor WhatsApp tidak valid (10-13 digit)";
    }
    
    if (empty($address) || strlen($address) < 10) {
        $errors[] = "Alamat minimal 10 karakter";
    }
    
    if (empty($program)) {
        $errors[] = "Program harus dipilih";
    }
    
    if (empty($schedule)) {
        $errors[] = "Jadwal harus dipilih";
    }
    
    // If no errors, insert to database
    if (empty($errors)) {
        $registration_data = [
            'full_name' => $full_name,
            'age' => $age,
            'gender' => $gender,
            'whatsapp' => $whatsapp,
            'address' => $address,
            'program' => $program,
            'schedule' => $schedule
        ];
        
        $insert_id = insert_registration($registration_data);
        
        if ($insert_id) {
            // Success - Create WhatsApp payment message with dynamic pricing
            $admin_whatsapp = '6285320808003'; // Ganti dengan nomor admin
            
            // Program price mapping
            $program_prices = [
                'Kelas Anak-anak' => [
                    'price' => 600000,
                    'unit' => 'bulan'
                ],
                'Kelas Remaja' => [
                    'price' => 800000,
                    'unit' => 'bulan'
                ],
                'Kelas Dewasa' => [
                    'price' => 750000,
                    'unit' => 'bulan'
                ],
                'Kelas Privat' => [
                    'price' => 250000,
                    'unit' => 'sesi'
                ]
            ];
            
            // Get price for selected program
            $selected_price = isset($program_prices[$program]) ? $program_prices[$program] : ['price' => 0, 'unit' => 'bulan'];
            $price_formatted = 'Rp ' . number_format($selected_price['price'], 0, ',', '.');
            
            // Build WhatsApp message
            $message = "*KONFIRMASI PENDAFTARAN KURSUS RENANG*\n\n";
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
            $message .= "Terima kasih! ";
            
            // Encode message for URL
            $encoded_message = urlencode($message);
            
            // Create WhatsApp URL
            $whatsapp_url = "https://wa.me/" . $admin_whatsapp . "?text=" . $encoded_message;
            
            // Redirect to WhatsApp
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
    <title>Swimming Course</title>
    <meta name="description" content="Lembaga kursus renang terpercaya dengan pelatih profesional. Program les renang untuk anak, remaja, dan dewasa. Daftar sekarang!">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/landing-complete.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="#home" class="logo">
                    <span class="logo-text">swimming Course</span>
                </a>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="#home" class="nav-link">Beranda</a></li>
                    <li><a href="#about" class="nav-link">Tentang Kami</a></li>
                    <li><a href="#programs" class="nav-link">Program</a></li>
                    <li><a href="#facilities" class="nav-link">Fasilitas</a></li>
                    <li><a href="#testimonials" class="nav-link">Testimoni</a></li>
                    <li><a href="#registration" class="nav-link nav-cta">Daftar Sekarang</a></li>
                </ul>
                <button class="mobile-toggle" id="mobileToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    Kuasai Seni Berenang Bersama <span class="highlight">Pelatih Profesional</span>
                </h1>
                <p class="hero-description">
                    Bergabunglah dengan ribuan peserta yang telah meningkatkan kemampuan renang mereka. 
                    Program khusus untuk anak-anak, remaja, dan dewasa dengan fasilitas kolam renang standar internasional.
                </p>
                <div class="hero-buttons">
                    <a href="#registration" class="btn btn-primary">Daftar Sekarang</a>
                    <a href="#programs" class="btn btn-secondary">Lihat Program</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-card">
                        <div class="stat-number">3000+</div>
                        <div class="stat-label">Peserta Aktif</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">20+</div>
                        <div class="stat-label">Pelatih Bersertifikat</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">10 Tahun</div>
                        <div class="stat-label">Pengalaman</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Tentang <span class="highlight">Swimming Course</span></h2>
                <p class="section-subtitle">Lembaga les renang terpercaya sejak 2015</p>
            </div>
            <div class="about-content">
                <div class="about-text">
                    <h3>Siapa Kami?</h3>
                    <p>
                        AquaLearn adalah lembaga kursus renang profesional yang telah berpengalaman lebih dari 10 tahun 
                        dalam mengajarkan seni berenang kepada ribuan peserta dari berbagai usia. Kami berkomitmen untuk 
                        memberikan pendidikan renang berkualitas dengan metode yang aman, efektif, dan menyenangkan.
                    </p>
                    
                    <div class="vision-mission">
                        <div class="vm-item">
                            <h4> Visi</h4>
                            <p>Menjadi lembaga kursus renang terdepan di Indonesia yang menghasilkan perenang berkualitas 
                               dan berkarakter melalui program pelatihan yang terstruktur dan profesional.</p>
                        </div>
                        <div class="vm-item">
                            <h4> Misi</h4>
                            <ul>
                                <li>Menyediakan program les renang berkualitas untuk semua usia</li>
                                <li>Mengembangkan metode pelatihan yang inovatif dan efektif</li>
                                <li>Membentuk perenang yang kompeten dan percaya diri</li>
                                <li>Memberikan pelayanan terbaik dengan fasilitas standar internasional</li>
                            </ul>
                        </div>
                    </div>

                    <div class="advantages">
                        <h4> Keunggulan Kami</h4>
                        <div class="advantage-grid">
                            <div class="advantage-item">
                                <span class="advantage-icon"></span>
                                <span>Pelatih Bersertifikat Internasional</span>
                            </div>
                            <div class="advantage-item">
                                <span class="advantage-icon"></span>
                                <span>Metode Pembelajaran Terstruktur</span>
                            </div>
                            <div class="advantage-item">
                                <span class="advantage-icon"></span>
                                <span>Prestasi di Kompetisi Nasional</span>
                            </div>
                            <div class="advantage-item">
                                <span class="advantage-icon"></span>
                                <span>Kelas Kecil (Max 6 Peserta)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section class="programs" id="programs">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Program <span class="highlight">Les Renang</span></h2>
                <p class="section-subtitle">Pilih program yang sesuai dengan kebutuhan Anda</p>
            </div>
            <div class="programs-grid">
                <!-- Kelas Anak-anak -->
                <div class="program-card">
                    <h3 class="program-title">Kelas Pemula</h3>
                    <p class="program-age">Usia 4-15 tahun</p>
                    <p class="program-description">
                        Program khusus untuk anak-anak dengan fokus pada pengenalan air, teknik dasar renang, 
                        dan pembentukan kepercayaan diri di dalam air.
                    </p>
                    <div class="program-details">
                        <div class="detail-item">
                            <strong>Jadwal:</strong>
                            <span>Senin, Rabu, Jumat (15:00 - 16:00)</span>
                        </div>
                        <div class="detail-item">
                            <strong> Durasi:</strong>
                            <span>120 menit per sesi</span>
                        </div>
                        <div class="detail-item">
                            <strong> Harga:</strong>
                            <span class="price">Rp 160.000/8 Pertemuan</span>
                        </div>
                    </div>
                    <ul class="program-features">
                        <li>  Pengenalan air dan adaptasi</li>
                        <li>  Teknik pernapasan dasar</li>
                        <li>  Gaya bebas dan gaya dada</li>
                        <li>  Sertifikat kelulusan</li>
                    </ul>
                    <a href="#registration" class="btn btn-program">Daftar Program Ini</a>
                </div>

                <!-- Kelas Remaja -->
                <div class="program-card featured">
                    <div class="badge">Populer</div>
                    <div class="program-icon"></div>
                    <h3 class="program-title">Kelas Remaja</h3>
                    <p class="program-age">Usia 13-18 tahun</p>
                    <p class="program-description">
                        Program untuk remaja yang ingin meningkatkan teknik renang, kekuatan, dan kecepatan 
                        dengan pelatihan yang lebih intensif.
                    </p>
                    <div class="program-details">
                        <div class="detail-item">
                            <strong> Jadwal:</strong>
                            <span>Selasa, Kamis, Sabtu (16:00 - 17:30)</span>
                        </div>
                        <div class="detail-item">
                            <strong> Durasi:</strong>
                            <span>90 menit per sesi</span>
                        </div>
                        <div class="detail-item">
                            <strong> Harga:</strong>
                            <span class="price">Rp 800.000/bulan</span>
                        </div>
                    </div>
                    <ul class="program-features">
                        <li>  Penyempurnaan 4 gaya renang</li>
                        <li>  Teknik start dan turn</li>
                        <li>  Peningkatan stamina</li>
                        <li>  Persiapan kompetisi</li>
                    </ul>
                    <a href="#registration" class="btn btn-program">Daftar Program Ini</a>
                </div>

                <!-- Kelas Dewasa -->
                <div class="program-card">
                    <div class="program-icon"></div>
                    <h3 class="program-title">Kelas Recovery</h3>
                    <p class="program-age">Semua Kalangan</p>
                    <p class="program-description">
                        Program untuk dewasa yang ingin belajar renang dari nol atau meningkatkan kemampuan 
                        dengan pendekatan yang fleksibel.
                    </p>
                    <div class="program-details">
                        <div class="detail-item">
                            <strong> Jadwal:</strong>
                            <span>Senin-Sabtu (06:00 - 07:00 / 18:00 - 19:00)</span>
                        </div>
                        <div class="detail-item">
                            <strong> Durasi:</strong>
                            <span>120 menit per sesi</span>
                        </div>
                        <div class="detail-item">
                            <strong> Harga:</strong>
                            <span class="price">Rp 750.000/bulan</span>
                        </div>
                    </div>
                    <ul class="program-features">
                        <li>  Pembelajaran dari dasar</li>
                        <li>  Jadwal fleksibel</li>
                        <li>  Teknik renang untuk fitness</li>
                        <li>  Konsultasi pribadi</li>
                    </ul>
                    <a href="#registration" class="btn btn-program">Daftar Program Ini</a>
                </div>

                <!-- Kelas Privat -->
                <div class="program-card premium">
                    <div class="badge premium-badge">Premium</div>
                    <div class="program-icon"></div>Profesional</h3>
                    <p class="program-age">Semua Usia</p>
                    <p class="program-description">
                        Program Profesional one-on-one dengan pelatih profesional untuk pembelajaran yang lebih 
                        personal dan hasil maksimal.
                    </p>
                    <div class="program-details">
                        <div class="detail-item">
                            <strong> Jadwal:</strong>
                            <span>Fleksibel (sesuai kesepakatan)</span>
                        </div>
                        <div class="detail-item">
                            <strong> Durasi:</strong>
                            <span>60-90 menit per sesi</span>
                        </div>
                        <div class="detail-item">
                            <strong> Harga:</strong>
                            <span class="price">Rp.350.000/8x pertemuan</span>
                        </div>
                    </div>
                    <ul class="program-features">
                        <li>  Pembelajaran 1-on-1</li>
                        <li>  Program disesuaikan kebutuhan</li>
                        <li>  Progress tracking detail</li>
                        <li>  Prioritas jadwal</li>
                    </ul>
                    <a href="#registration" class="btn btn-program">Daftar Program Ini</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="facilities" id="facilities">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Fasilitas <span class="highlight">Terbaik</span></h2>
                <p class="section-subtitle">Kami menyediakan fasilitas lengkap untuk kenyamanan Anda</p>
            </div>
            <div class="facilities-grid">
                <div class="facility-card">
                    <div class="facility-icon"></div>
                    <h3>Kolam Renang Standar</h3>
                    <p>Kolam renang berukuran 25m x 12m dengan kedalaman bervariasi (1.2m - 2m) yang sesuai standar internasional</p>
                </div>
                <div class="facility-card">
                    <div class="facility-icon"></div>
                    <h3>Pelatih Profesional</h3>
                    <p>Tim pelatih bersertifikat nasional dan internasional dengan pengalaman lebih dari 5 tahun</p>
                </div>
                <div class="facility-card">
                    <div class="facility-icon"></div>
                    <h3>Peralatan Latihan</h3>
                    <p>Kickboard, pull buoy, fins, dan peralatan latihan modern untuk optimalisasi pembelajaran</p>
                </div>
                <div class="facility-card"
                    <div class="facility-icon"></div>
                    <h3>Ruang Ganti</h3>
                    <p>Ruang ganti bersih dengan shower air hangat, locker, dan fasilitas toilet yang memadai</p>
                </div>
                <div class="facility-card">
                    <div class="facility-icon"></div>
                    <h3>Area Parkir Luas</h3>
                    <p>Parkir mobil dan motor yang aman dengan kapasitas besar dan sistem keamanan 24 jam</p>
                </div>
                <div class="facility-card">
                    <div class="facility-icon"></div>
                    <h3>Ruang Tunggu</h3>
                    <p>Ruang tunggu nyaman ber-AC dengan WiFi gratis untuk orang tua menunggu</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Testimoni <span class="highlight">Peserta</span></h2>
                <p class="section-subtitle">Apa kata mereka yang telah bergabung dengan AquaLearn</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-rating"></div>
                    <p class="testimonial-text">
                        "Anak saya yang awalnya takut air, sekarang sudah bisa berenang dengan percaya diri. 
                        Pelatihnya sangat sabar dan metode pembelajarannya sangat bagus!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">BP</div>
                        <div class="author-info">
                            <div class="author-name">Budi Prasetyo</div>
                            <div class="author-role">Orang tua peserta kelas anak</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating"></div>
                    <p class="testimonial-text">
                        "Program remaja di AquaLearn sangat membantu saya meningkatkan teknik renang. 
                        Sekarang saya sudah bisa 4 gaya dan siap ikut kompetisi!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">SA</div>
                        <div class="author-info">
                            <div class="author-name">Siti Aminah</div>
                            <div class="author-role">Peserta kelas remaja</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating"></div>
                    <p class="testimonial-text">
                        "Di usia 35 tahun, saya akhirnya bisa berenang! Pelatih privat sangat memahami kebutuhan 
                        saya dan membuat saya nyaman belajar. Highly recommended!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">RH</div>
                        <div class="author-info">
                            <div class="author-name">Rina Hartati</div>
                            <div class="author-role">Peserta kelas dewasa</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating"></div>
                    <p class="testimonial-text">
                        "Fasilitas kolam renangnya bersih dan terawat. Pelatihnya profesional dan ramah. 
                        Anak saya sangat senang ikut les di sini!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">DW</div>
                        <div class="author-info">
                            <div class="author-name">Dian Wulandari</div>
                            <div class="author-role">Orang tua peserta</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating"></div>
                    <p class="testimonial-text">
                        "Kelas privat memberikan perhatian penuh. Dalam 2 bulan saya sudah mahir renang gaya bebas 
                        dan gaya punggung. Worth it banget!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">AP</div>
                        <div class="author-info">
                            <div class="author-name">Agus Purnomo</div>
                            <div class="author-role">Peserta kelas privat</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating"></div>
                    <p class="testimonial-text">
                        "Jadwalnya fleksibel, cocok untuk saya yang sibuk kerja. Fasilitasnya lengkap dan bersih. 
                        Pelayanannya juga sangat memuaskan!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">MF</div>
                        <div class="author-info">
                            <div class="author-name">Maya Fitriani</div>
                            <div class="author-role">Peserta kelas dewasa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Form Section -->
    <section class="registration" id="registration">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Daftar <span class="highlight">Sekarang</span></h2>
                <p class="section-subtitle">Isi formulir di bawah untuk mendaftar program les renang</p>
            </div>
            <div class="registration-wrapper">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #ef4444;">
                        <strong> Terjadi Kesalahan:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="registration-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Nama Lengkap <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="full_name" 
                                name="full_name" 
                                class="form-input"
                                placeholder="Masukkan nama lengkap"
                                value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="age">Umur <span class="required">*</span></label>
                            <input 
                                type="number" 
                                id="age" 
                                name="age" 
                                class="form-input"
                                placeholder="Masukkan umur"
                                min="4"
                                max="100"
                                value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender">Jenis Kelamin <span class="required">*</span></label>
                            <select id="gender" name="gender" class="form-input" required>
                                <option value="">Pilih jenis kelamin</option>
                                <option value="Laki-laki" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="whatsapp">Nomor WhatsApp <span class="required">*</span></label>
                            <input 
                                type="tel" 
                                id="whatsapp" 
                                name="whatsapp" 
                                class="form-input"
                                placeholder="08xxxxxxxxxx"
                                pattern="[0-9]{10,13}"
                                value="<?php echo isset($_POST['whatsapp']) ? htmlspecialchars($_POST['whatsapp']) : ''; ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Alamat <span class="required">*</span></label>
                        <textarea 
                            id="address" 
                            name="address" 
                            class="form-input"
                            placeholder="Masukkan alamat lengkap"
                            rows="3"
                            required
                        ><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="program">Pilihan Program <span class="required">*</span></label>
                            <select id="program" name="program" class="form-input" required>
                                <option value="">Pilih program</option>
                                <option value="Kelas Anak-anak" <?php echo (isset($_POST['program']) && $_POST['program'] == 'Kelas Anak-anak') ? 'selected' : ''; ?>>Kelas Anak-anak (4-12 tahun)</option>
                                <option value="Kelas Remaja" <?php echo (isset($_POST['program']) && $_POST['program'] == 'Kelas Remaja') ? 'selected' : ''; ?>>Kelas Remaja (13-18 tahun)</option>
                                <option value="Kelas Dewasa" <?php echo (isset($_POST['program']) && $_POST['program'] == 'Kelas Dewasa') ? 'selected' : ''; ?>>Kelas Dewasa (18+ tahun)</option>
                                <option value="Kelas Privat" <?php echo (isset($_POST['program']) && $_POST['program'] == 'Kelas Privat') ? 'selected' : ''; ?>>Kelas Privat (Semua usia)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="schedule">Pilihan Jadwal <span class="required">*</span></label>
                            <select id="schedule" name="schedule" class="form-input" required>
                                <option value="">Pilih jadwal</option>
                                <option value="Pagi (06:00 - 08:00)" <?php echo (isset($_POST['schedule']) && $_POST['schedule'] == 'Pagi (06:00 - 08:00)') ? 'selected' : ''; ?>>Pagi (06:00 - 08:00)</option>
                                <option value="Siang (15:00 - 17:00)" <?php echo (isset($_POST['schedule']) && $_POST['schedule'] == 'Siang (15:00 - 17:00)') ? 'selected' : ''; ?>>Siang (15:00 - 17:00)</option>
                                <option value="Sore (17:00 - 19:00)" <?php echo (isset($_POST['schedule']) && $_POST['schedule'] == 'Sore (17:00 - 19:00)') ? 'selected' : ''; ?>>Sore (17:00 - 19:00)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-submit">
                             Kirim Pendaftaran
                        </button>
                        <button type="reset" class="btn btn-reset">Reset Form</button>
                    </div>
                </form>

                <div class="registration-info">
                    <div class="info-card">
                        <h3> Informasi Pendaftaran</h3>
                        <ul>
                            <li>  Pendaftaran dapat dilakukan secara online</li>
                            <li>  Kami akan menghubungi Anda via WhatsApp dalam 1x24 jam</li>
                            <li>  Biaya pendaftaran Rp 100.000 (sekali bayar)</li>
                            <li>  RINCIAN BIAYA dapat dilakukan via transfer atau tunai</li>
                            <li>  Sertakan foto KTP/Kartu Pelajar saat konfirmasi</li>
                        </ul>
                    </div>
                    <div class="info-card">
                        <h3> Kontak Kami</h3>
                        <p><strong>WhatsApp:</strong> +62 812-3456-7890</p>
                        <p><strong>Email:</strong> info@aqualear.id</p>
                        <p><strong>Alamat:</strong> Jl. Aquatic Center No. 123, Jakarta</p>
                        <p><strong>Jam Operasional:</strong> Senin - Sabtu (06:00 - 20:00)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <span class="logo-icon"></span>
                        <span class="logo-text">swimming course</span>
                    </div>
                    <p>Lembaga kursus renang profesional untuk semua usia dengan pelatih bersertifikat dan fasilitas terbaik.</p>
                </div>
                <div class="footer-section">
                    <h4>Program</h4>
                    <ul>
                        <li><a href="#programs">Kelas Anak-anak</a></li>
                        <li><a href="#programs">Kelas Remaja</a></li>
                        <li><a href="#programs">Kelas Dewasa</a></li>
                        <li><a href="#programs">Kelas Privat</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Informasi</h4>
                    <ul>
                        <li><a href="#about">Tentang Kami</a></li>
                        <li><a href="#facilities">Fasilitas</a></li>
                        <li><a href="#testimonials">Testimoni</a></li>
                        <li><a href="#registration">Pendaftaran</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Hubungi Kami</h4>
                    <p> Jl. Aquatic Center No. 123, Jakarta</p>
                    <p> +62 812-3456-7890</p>
                    <p> info@aqualear.id</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 AquaLearn. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Notification Toast -->
    <div id="notification" class="notification"></div>

    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top"></button>
    
    <!-- // sistem role
    switch ($role) {
        case 'admin' :
            return redirect()->route('admin_dashboard.php');
        case 'user' :
            return redirect()->route('index.php');
    } -->
</body>
</html>
