<?php
// Start session for authentication
session_start();

// Include database connection and functions
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
        
        $insert_id = simpan_pendaftaran($registration_data);
        
        if ($insert_id) {
            // Success - Create WhatsApp payment message with dynamic pricing
            $admin_whatsapp = '6285320808003'; // Ganti dengan nomor admin
            
            // Program price mapping
            $program_prices = [
                'Kelas Pemula' => [
                    'price' => 160000,
                    'unit' => '8x pertemuan'
                ],
                'Kelas Recovery' => [
                    'price' => 200000,
                    'unit' => '8x pertemuan'
                ],
                'Kelas Profesional' => [
                    'price' => 350000,
                    'unit' => '8x pertemuan'
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
            $message .= "Terima kasih!";
            
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
    <title>SwimPro - Kursus Renang Profesional</title>
    <meta name="description" content="Belajar renang lebih mudah dan profesional bersama SwimPro. Instruktur berpengalaman, jadwal fleksibel, dan metode pembelajaran modern.">
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
                    <span class="logo-text">Swimming Course</span>
                </a>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="#home" class="nav-link">Home</a></li>
                    <li><a href="#features" class="nav-link">Features</a></li>
                    <li><a href="#registration" class="nav-link">Contact</a></li>
                    
                    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                        <li><span class="nav-link">Hai, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
                        <li><a href="logout.php" class="nav-link nav-cta" style="background: #ef4444;">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="nav-link">Login</a></li>
                        <li><a href="register.php" class="nav-link nav-cta">Daftar Sekarang</a></li>
                    <?php endif; ?>
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
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <h1 class="hero-title">
                        Belajar Renang Lebih <span class="highlight">Mudah</span> dan Profesional
                    </h1>
                    <p class="hero-description">
                        Kuasai teknik renang bersama instruktur berpengalaman. Fasilitas modern, jadwal fleksibel, dan metode pembelajaran yang terbukti efektif untuk semua usia.
                    </p>
                    <div class="hero-buttons">
                        <a href="#registration" class="btn btn-primary">Mulai Sekarang</a>
                        <a href="#programs" class="btn btn-secondary">Lihat Program</a>
                    </div>
                    <div class="hero-trust">
                        <div class="trust-item">
                            <span class="trust-icon">⭐</span>
                            <span class="trust-text">4.9/5 Rating</span>
                        </div>
                        <div class="trust-item">
                            <span class="trust-icon">👥</span>
                            <span class="trust-text">3000+ Peserta</span>
                        </div>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="image-placeholder">
                        <img src="https://images.unsplash.com/photo-1530549387789-4c1017266635?w=600&h=400&fit=crop" alt="Swimming" style="width: 100%; height: 100%; object-fit: cover; border-radius: 20px;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Mengapa Memilih SwimPro?</h2>
                <p class="section-desc">Kami menyediakan layanan terbaik dengan fasilitas modern dan instruktur berpengalaman untuk memastikan Anda belajar renang dengan cara yang paling efektif.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Pelatih Bersertifikat</h3>
                    <p class="feature-desc">Kami memiliki tim pelatih profesional yang berpengalaman dan bersertifikat internasional untuk membimbing Anda.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="feature-title">Jadwal Fleksibel</h3>
                    <p class="feature-desc">Pilih jadwal yang sesuai dengan rutinitas Anda. Tersedia kelas pagi, siang, dan sore hari untuk kemudahan Anda.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <h3 class="feature-title">Metode Terpercaya</h3>
                    <p class="feature-desc">Menggunakan metode pembelajaran yang terbukti efektif dan aman untuk semua tingkat kemampuan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section class="programs" id="programs">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">💰 Paket Program Renang</h2>
                <p class="section-desc">Pilih paket yang sesuai dengan kebutuhan dan usia Anda. Semua paket dilengkapi dengan instruktur profesional dan fasilitas terbaik.</p>
            </div>
            
            <div class="programs-grid">
                <!-- Kelas Pemula -->
                <div class="program-card">
                    <div class="program-header">
                        <h3 class="program-name">Kelas Pemula</h3>
                        <p class="program-age">Usia 4-15 tahun</p>
                    </div>
                    <div class="program-price">
                        <span class="price-amount">Rp 160.000</span>
                        <span class="price-period">/8x pertemuan</span>
                    </div>
                    <ul class="program-features">
                        <li>✓ Pengenalan air dan adaptasi</li>
                        <li>✓ Teknik pernapasan dasar</li>
                        <li>✓ Gaya bebas dan gaya dada</li>
                        <li>✓ Sertifikat kelulusan</li>
                        <li>✓ Jadwal: Senin, Rabu, Jumat</li>
                    </ul>
                    <a href="#registration" class="btn-program">Daftar Sekarang</a>
                </div>
                
                <!-- Kelas Recovery -->
                <div class="program-card featured-program">
                    <div class="program-badge">Populer</div>
                    <div class="program-header">
                        <h3 class="program-name">Kelas Recovery</h3>
                        <p class="program-age">Semua Kalangan</p>
                    </div>
                    <div class="program-price">
                        <span class="price-amount">Rp 200.000</span>
                        <span class="price-period">/8x pertemuan</span>
                    </div>
                    <ul class="program-features">
                        <li>✓ Pembelajaran dari dasar</li>
                        <li>✓ Jadwal fleksibel</li>
                        <li>✓ Teknik renang untuk fitness</li>
                        <li>✓ Konsultasi pribadi</li>
                        <li>✓ Jadwal: Senin-Sabtu</li>
                    </ul>
                    <a href="#registration" class="btn-program">Daftar Sekarang</a>
                </div>
                
                <!-- Kelas Profesional -->
                <div class="program-card premium-program">
                    <div class="program-badge premium-badge">Premium</div>
                    <div class="program-header">
                        <h3 class="program-name">Kelas Profesional</h3>
                        <p class="program-age">Semua Usia</p>
                    </div>
                    <div class="program-price">
                        <span class="price-amount">Rp 350.000</span>
                        <span class="price-period">/8x pertemuan</span>
                    </div>
                    <ul class="program-features">
                        <li>✓ Pembelajaran 1-on-1</li>
                        <li>✓ Program disesuaikan kebutuhan</li>
                        <li>✓ Progress tracking detail</li>
                        <li>✓ Prioritas jadwal</li>
                        <li>✓ Fleksibel & Intensif</li>
                    </ul>
                    <a href="#registration" class="btn-program">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Section -->
    <section class="registration" id="registration">
        <div class="container">
            <div class="registration-grid">
                <!-- Form Section -->
                <div class="form-section">
                    <div class="form-header">
                        <h2 class="form-title">Paket Berenang</h2>
                        <p class="form-subtitle">Isi formulir di bawah untuk mendaftar program renang</p>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert-error">
                            <strong>⚠️ Terjadi kesalahan:</strong>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="#registration" class="registration-form">
                        <div class="form-group">
                            <label for="full_name">Nama Lengkap <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="full_name" 
                                name="full_name" 
                                class="form-input" 
                                placeholder="Masukkan nama lengkap"
                                value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : (isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''); ?>"
                                required
                            >
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="age">Umur <span class="required">*</span></label>
                                <input 
                                    type="number" 
                                    id="age" 
                                    name="age" 
                                    class="form-input" 
                                    placeholder="Umur"
                                    min="4" 
                                    max="100"
                                    value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>"
                                    required
                                >
                            </div>
                            
                            <div class="form-group">
                                <label for="gender">Jenis Kelamin <span class="required">*</span></label>
                                <select id="gender" name="gender" class="form-input" required>
                                    <option value="">Pilih</option>
                                    <option value="Laki-laki" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="whatsapp">No. WhatsApp <span class="required">*</span></label>
                            <input 
                                type="tel" 
                                id="whatsapp" 
                                name="whatsapp" 
                                class="form-input" 
                                placeholder="08123456789"
                                value="<?php echo isset($_POST['whatsapp']) ? htmlspecialchars($_POST['whatsapp']) : ''; ?>"
                                required
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Alamat <span class="required">*</span></label>
                            <textarea 
                                id="address" 
                                name="address" 
                                class="form-input" 
                                rows="3"
                                placeholder="Masukkan alamat lengkap"
                                required
                            ><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="program">Program <span class="required">*</span></label>
                            <select id="program" name="program" class="form-input" required>
                                <option value="">Pilih Program</option>
                                <option value="Kelas Pemula" <?php echo (isset($_POST['program']) && $_POST['program'] === 'Kelas Pemula') ? 'selected' : ''; ?>>Kelas Pemula</option>
                                <option value="Kelas Recovery" <?php echo (isset($_POST['program']) && $_POST['program'] === 'Kelas Recovery') ? 'selected' : ''; ?>>Kelas Recovery</option>
                                <option value="Kelas Profesional" <?php echo (isset($_POST['program']) && $_POST['program'] === 'Kelas Profesional') ? 'selected' : ''; ?>>Kelas Profesional</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="schedule">Jadwal <span class="required">*</span></label>
                            <select id="schedule" name="schedule" class="form-input" required>
                                <option value="">Pilih Jadwal</option>
                                <option value="Jumat, siang (14:00 - 16:00)" <?php echo (isset($_POST['schedule']) && $_POST['schedule'] === 'Jumat, siang (14:00 - 16:00)') ? 'selected' : ''; ?>>Jumat, siang (14:00 - 16:00)</option>
                                <option value="Minggu, pagi (08:00 - 10:00)" <?php echo (isset($_POST['schedule']) && $_POST['schedule'] === 'Minggu, pagi (08:00 - 10:00)') ? 'selected' : ''; ?>>Minggu, pagi (08:00 - 10:00)</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            Daftar Sekarang →
                        </button>
                    </form>
                </div>
                
                <!-- Info Section -->
                <div class="info-section">
                    <div class="info-card">
                        <h3>💰 Harga Paket</h3>
                        <ul class="price-list">
                            <li>
                                <span>Kelas Pemula</span>
                                <strong>Rp 160.000/8x</strong>
                            </li>
                            <li>
                                <span>Kelas Recovery</span>
                                <strong>Rp 200.000/8x</strong>
                            </li>
                            <li>
                                <span>Kelas Profesional</span>
                                <strong>Rp 350.000/8x</strong>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="info-card">
                        <h3>📞 Hubungi Kami</h3>
                        <div class="contact-info">
                            <p><strong>WhatsApp:</strong> +62 853-2080-8003</p>
                            <p><strong>Email:</strong> info@swimpro.com</p>
                            <p><strong>Instagram:</strong> @swimpro.id</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3 class="footer-title">🏊 SwimPro</h3>
                    <p class="footer-desc">Kursus renang profesional dengan instruktur berpengalaman dan fasilitas terbaik.</p>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-heading">Training Center</h4>
                    <p class="footer-text">
                        Jl. Renang Indah No. 123<br>
                        Jakarta Selatan, 12345<br>
                        Indonesia
                    </p>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-heading">Contact</h4>
                    <p class="footer-text">
                        Email: info@swimpro.com<br>
                        Phone: +62 853-2080-8003<br>
                        WhatsApp: +62 853-2080-8003
                    </p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 SwimPro. All Rights Reserved.</p>
                <div class="footer-links">
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/landing-complete.js"></script>
</body>
</html>
