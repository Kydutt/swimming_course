<section class="programs" id="programs">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Paket Program Renang</h2>
            <p class="section-desc">Pilih paket yang sesuai dengan kebutuhan dan usia Anda. Semua paket dilengkapi dengan instruktur profesional dan fasilitas terbaik.</p>
        </div>
        
        <div class="programs-grid">
            <?php 
            $card_classes = ['', 'featured-program', 'premium-program'];
            $badge_labels = ['Populer', '', 'Premium'];
            $i = 0;
            foreach ($programs_list as $prog): 
                $class = $card_classes[$i] ?? '';
                $badge = $badge_labels[$i] ?? '';
                $harga_formatted = 'Rp ' . number_format($prog['harga'], 0, ',', '.');
            ?>
            <div class="program-card <?php echo $class; ?>">
                <?php if (!empty($badge)): ?>
                <div class="program-badge <?php echo $i === 2 ? 'premium-badge' : ''; ?>"><?php echo $badge; ?></div>
                <?php endif; ?>
                <div class="program-header">
                    <h3 class="program-name"><?php echo htmlspecialchars($prog['nama_program']); ?></h3>
                    <p class="program-age">Semua Usia</p>
                </div>
                <div class="program-price">
                    <span class="price-amount"><?php echo $harga_formatted; ?></span>
                    <span class="price-period">/<?php echo $prog['jumlah_pertemuan']; ?>x pertemuan</span>
                </div>
                <ul class="program-features">
                    <?php 
                    $fitur = explode(', ', $prog['deskripsi']);
                    foreach ($fitur as $f): 
                    ?>
                    <li style="display: flex; align-items: start; gap: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981; flex-shrink: 0; margin-top: 3px;">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span><?php echo htmlspecialchars($f); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php
                $daftar_href = (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true)
                    ? '#registration'
                    : 'proses/login.php';
                ?>
                <a href="<?php echo $daftar_href; ?>" class="btn-program">Daftar Sekarang</a>
            </div>
            <?php $i++; endforeach; ?>
        </div>
    </div>
</section>
