<section class="registration" id="registration">
    <div class="container">
        <div class="registration-grid">
            
            <div class="form-section">
                <div class="form-header">
                    <h2 class="form-title">Paket Berenang</h2>
                    <p class="form-subtitle">Isi formulir di bawah untuk mendaftar program renang</p>
                </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert-error">
                            <strong><?= icon('warning', 16) ?> Terjadi kesalahan:</strong>
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
                                <?php foreach ($programs_list as $prog): ?>
                                <option value="<?php echo htmlspecialchars($prog['nama_program']); ?>" <?php echo (isset($_POST['program']) && $_POST['program'] === $prog['nama_program']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($prog['nama_program']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="schedule">Jadwal <span class="required">*</span></label>
                            <select id="schedule" name="schedule" class="form-input" required>
                                <option value="">Pilih Jadwal</option>
                                <?php foreach ($jadwal_list as $jdw): ?>
                                <option value="<?php echo htmlspecialchars($jdw['keterangan']); ?>" <?php echo (isset($_POST['schedule']) && $_POST['schedule'] === $jdw['keterangan']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($jdw['keterangan']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            Daftar Sekarang →
                        </button>
                    </form>
            </div>

            <div class="info-section">
                <div class="info-card">
                    <h3 style="display: flex; align-items: center; gap: 8px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981;">
                            <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                            <circle cx="12" cy="12" r="2"></circle>
                            <path d="M6 12h.01M18 12h.01"></path>
                        </svg>
                        Harga Paket
                    </h3>
                    <ul class="price-list">
                        <?php foreach ($programs_list as $prog): ?>
                        <li>
                            <span><?php echo htmlspecialchars($prog['nama_program']); ?></span>
                            <strong>Rp <?php echo number_format($prog['harga'], 0, ',', '.'); ?>/<?php echo $prog['jumlah_pertemuan']; ?>x</strong>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3 style="display: flex; align-items: center; gap: 8px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #3b82f6;">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        Hubungi Kami
                    </h3>
                    <div class="contact-info">
                        <p><strong>WhatsApp:</strong> +62 896-5462-7672</p>
                        <p><strong>Email:</strong> info@swimpro.com</p>
                        <p><strong>Instagram:</strong> @swimming_course</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
