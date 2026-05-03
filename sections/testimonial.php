<!-- Testimonial Section -->
<section class="testimonial-section" id="testimonial">
    <div class="container">
        <div class="testimonial-header">
            <h2 class="testimonial-title">Kata Mereka <?= icon('chat', 24) ?></h2>
            <p class="testimonial-sub">Pengalaman nyata dari peserta kursus renang kami</p>
        </div>

        <!-- Flash (hanya untuk fallback non-AJAX) -->
        <?php if (!empty($feedback_flash)): ?>
            <div class="fb-flash fb-flash-<?php echo $feedback_flash_type; ?>" id="fbFlash">
                <?php echo $feedback_flash; ?>
            </div>
        <?php endif; ?>

        <!-- Testimonial Cards -->
        <?php if (!empty($feedback_list)): ?>
        <div class="testimonial-grid" id="testimonialGrid">
            <?php foreach ($feedback_list as $fb): ?>
            <div class="testimonial-card">
                <div class="tc-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="<?php echo $i <= $fb['rating'] ? 'star-on' : 'star-off'; ?>"><?= icon('star', 18) ?></span>
                    <?php endfor; ?>
                </div>
                <p class="tc-comment">"<?php echo htmlspecialchars($fb['komentar']); ?>"</p>
                <div class="tc-author">
                    <div class="tc-avatar"><?php echo strtoupper(mb_substr($fb['name'], 0, 1)); ?></div>
                    <div>
                        <div class="tc-name"><?php echo htmlspecialchars($fb['name']); ?></div>
                        <div class="tc-date"><?php echo date('d M Y', strtotime($fb['created_at'])); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="testimonial-grid" id="testimonialGrid"></div>
        <p class="testimonial-empty" id="testimonialEmpty">Belum ada testimoni. Jadilah yang pertama! <?= icon('wave', 20) ?></p>
        <?php endif; ?>

        <!-- Feedback Form (only for logged-in users) -->
        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true && $_SESSION['role'] === 'user'): ?>
        <div class="fb-form-wrap" id="fbFormWrap">
            <h3 class="fb-form-title"><?= icon('chat', 20) ?> Bagikan Pengalamanmu</h3>

            <!-- Flash AJAX -->
            <div id="fbAjaxFlash" style="display:none; margin-bottom:1rem;"></div>

            <form id="fbForm" class="fb-form">
                <!-- Star rating -->
                <div class="fb-stars" id="starRating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i === 5 ? 'checked' : ''; ?>>
                        <label for="star<?php echo $i; ?>"><?= icon('star', 24) ?></label>
                    <?php endfor; ?>
                </div>

                <textarea name="komentar" id="fbKomentar" class="fb-textarea" rows="4"
                          placeholder="Ceritakan pengalamanmu belajar renang di sini..." required maxlength="500"></textarea>
                <div class="fb-char"><span id="charCount">0</span>/500</div>

                <button type="submit" name="submit_feedback" class="fb-btn" id="fbSubmitBtn">Kirim Feedback →</button>
            </form>
        </div>

        <script>
        (function () {
            const form      = document.getElementById('fbForm');
            const grid      = document.getElementById('testimonialGrid');
            const emptyNote = document.getElementById('testimonialEmpty');
            const flash     = document.getElementById('fbAjaxFlash');
            const submitBtn = document.getElementById('fbSubmitBtn');
            const textarea  = document.getElementById('fbKomentar');
            const charEl    = document.getElementById('charCount');

            // Character counter
            if (textarea && charEl) {
                textarea.addEventListener('input', () => {
                    charEl.textContent = textarea.value.length;
                });
            }

            function showFlash(msg, type) {
                flash.innerHTML = msg;
                flash.className   = 'fb-flash fb-flash-' + type;
                flash.style.display = 'block';
                setTimeout(() => {
                    flash.style.transition = 'opacity .5s, transform .5s';
                    flash.style.opacity    = '0';
                    flash.style.transform  = 'translateY(-8px)';
                    setTimeout(() => {
                        flash.style.display   = '';
                        flash.style.opacity   = '';
                        flash.style.transform = '';
                    }, 500);
                }, 4000);
            }

            function buildCard(data) {
                let stars = '';
                const svgStar = `<?= icon('star', 18) ?>`;
                for (let i = 1; i <= 5; i++) {
                    stars += `<span class="${i <= data.rating ? 'star-on' : 'star-off'}">${svgStar}</span>`;
                }
                const initial = data.name.charAt(0).toUpperCase();
                const card = document.createElement('div');
                card.className = 'testimonial-card';
                card.style.cssText = 'opacity:0; transform:translateY(20px); transition:opacity .4s, transform .4s;';
                card.innerHTML = `
                    <div class="tc-stars">${stars}</div>
                    <p class="tc-comment">"${data.komentar}"</p>
                    <div class="tc-author">
                        <div class="tc-avatar">${initial}</div>
                        <div>
                            <div class="tc-name">${data.name}</div>
                            <div class="tc-date">${data.created_at}</div>
                        </div>
                    </div>`;
                return card;
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                // Ambil nilai rating yang dipilih
                const ratingInput = form.querySelector('input[name="rating"]:checked');
                const rating      = ratingInput ? ratingInput.value : 5;
                const komentar    = textarea.value.trim();

                if (!komentar) return;

                // Loading state
                submitBtn.disabled   = true;
                submitBtn.innerHTML = `<?= icon('clock', 16, 'inline-icon') ?> Mengirim...`;

                const body = new URLSearchParams();
                body.append('submit_feedback', '1');
                body.append('rating', rating);
                body.append('komentar', komentar);

                try {
                    const res  = await fetch('proses/feedback.php', {
                        method : 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body   : body,
                    });
                    const json = await res.json();

                    if (json.success) {
                        // Tampilkan flash sukses
                        showFlash('<?= icon('check', 16) ?> Terima kasih! Feedback kamu berhasil disimpan.', 'success');

                        // Tambahkan kartu baru ke grid
                        if (emptyNote) emptyNote.style.display = 'none';
                        const card = buildCard(json.data);
                        grid.prepend(card);
                        // Animasi masuk
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                card.style.opacity   = '1';
                                card.style.transform = 'translateY(0)';
                            });
                        });

                        // Reset form
                        textarea.value   = '';
                        charEl.textContent = '0';
                        form.querySelector('#star5').checked = true;
                    } else {
                        showFlash('<?= icon('warning', 16) ?> ' + (json.message || 'Gagal menyimpan feedback.'), 'error');
                    }
                } catch (err) {
                    showFlash('<?= icon('warning', 16) ?> Terjadi kesalahan jaringan. Coba lagi.', 'error');
                } finally {
                    submitBtn.disabled    = false;
                    submitBtn.textContent = 'Kirim Feedback →';
                }
            });
        })();
        </script>

        <?php elseif (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']): ?>
        <div class="fb-login-prompt">
            <p><?= icon('lock', 16) ?> <a href="proses/login.php">Login</a> untuk memberikan feedback</p>
        </div>
        <?php endif; ?>
    </div>
</section>
