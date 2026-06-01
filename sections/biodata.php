<?php
// $instruktur_list diisi dari index.php (semua instruktur)
$list = $instruktur_list ?? ($instruktur_data ? [$instruktur_data] : []);
?>
<section class="biodata" id="biodata">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Biodata Pelatih</h2>
            <p class="section-desc">Kenali instruktur profesional yang akan membimbing perjalanan renang Anda.</p>
        </div>

        <?php if (!empty($list)): ?>

        <!-- Slider Wrapper -->
        <div class="biodata-slider" id="biodataSlider">
            <div class="biodata-track" id="biodataTrack">

            <?php foreach ($list as $idx => $inst): ?>
                <div class="biodata-slide" data-index="<?= $idx ?>">
                    <div class="biodata-card">

                        <!-- ── Foto & Identitas ── -->
                        <div class="biodata-profile">
                            <div class="biodata-avatar">
                                <?php if (!empty($inst['foto']) && file_exists($inst['foto'])): ?>
                                    <img src="<?= htmlspecialchars($inst['foto']) ?>" alt="Foto <?= htmlspecialchars($inst['nama']) ?>">
                                <?php else: ?>
                                    <div class="biodata-avatar-initial">
                                        <?= strtoupper(substr($inst['nama'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="biodata-identity">
                                <h3 class="biodata-name"><?= htmlspecialchars($inst['nama']) ?></h3>

                                <?php if (!empty($inst['spesialisasi'])): ?>
                                <p class="biodata-role"><?= htmlspecialchars($inst['spesialisasi']) ?></p>
                                <?php endif; ?>

                                <div class="biodata-badges">
                                    <?php if (!empty($inst['sertifikasi'])): ?>
                                    <span class="biodata-badge biodata-badge--blue">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                                        <?= htmlspecialchars($inst['sertifikasi']) ?>
                                    </span>
                                    <?php endif; ?>

                                    <?php if (!empty($inst['umur'])): ?>
                                    <span class="biodata-badge biodata-badge--gray">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                        <?= $inst['umur'] ?> Tahun
                                    </span>
                                    <?php endif; ?>

                                    <?php if (!empty($inst['telepon'])): ?>
                                    <span class="biodata-badge biodata-badge--gray">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.25-3.95-6.847-6.847l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                                        <?= htmlspecialchars($inst['telepon']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($inst['bio'])): ?>
                                <p class="biodata-bio">"<?= htmlspecialchars($inst['bio']) ?>"</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- ── Pengalaman Grid ── -->
                        <div class="biodata-experience">

                            <?php if (!empty($inst['pengalaman_wasit'])): ?>
                            <div class="biodata-exp-card">
                                <div class="biodata-exp-icon">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0"/></svg>
                                </div>
                                <h4 class="biodata-exp-title">Pengalaman Wasit</h4>
                                <ul class="biodata-exp-list">
                                    <?php foreach (explode("\n", trim($inst['pengalaman_wasit'])) as $item):
                                        $item = trim($item);
                                        if ($item === '') continue;
                                    ?>
                                    <li><?= htmlspecialchars($item) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($inst['pengalaman_melatih'])): ?>
                            <div class="biodata-exp-card">
                                <div class="biodata-exp-icon">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM5 14c2 0 3-1.5 5-1.5S13 14 15 14s3-1.5 5-1.5M5 18c2 0 3-1.5 5-1.5s3 1.5 5 1.5 3-1.5 4-1.5M7 12l3-3 2 2 3-4"/></svg>
                                </div>
                                <h4 class="biodata-exp-title">Pengalaman Melatih</h4>
                                <ul class="biodata-exp-list">
                                    <?php foreach (explode("\n", trim($inst['pengalaman_melatih'])) as $item):
                                        $item = trim($item);
                                        if ($item === '') continue;
                                    ?>
                                    <li><?= htmlspecialchars($item) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            </div><!-- /.biodata-track -->

            <?php if (count($list) > 1): ?>
            <!-- Kontrol navigasi -->
            <button class="biodata-nav biodata-nav--prev" id="biodataPrev" aria-label="Instruktur sebelumnya">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
            <button class="biodata-nav biodata-nav--next" id="biodataNext" aria-label="Instruktur berikutnya">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>

            <!-- Dots -->
            <div class="biodata-dots" id="biodataDots">
                <?php foreach ($list as $i => $dummy): ?>
                <button class="biodata-dot <?= $i === 0 ? 'active' : '' ?>"
                        data-target="<?= $i ?>" aria-label="Instruktur <?= $i + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div><!-- /.biodata-slider -->

        <?php else: ?>
        <div class="biodata-empty">
            <p>Informasi pelatih belum tersedia.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($list) && count($list) > 1): ?>
<script>
(function () {
    const track = document.getElementById('biodataTrack');
    const prev  = document.getElementById('biodataPrev');
    const next  = document.getElementById('biodataNext');
    const dots  = document.querySelectorAll('.biodata-dot');
    const total = <?= count($list) ?>;

    // ── Teknik infinite loop: clone slide pertama & terakhir ──
    const slides      = Array.from(track.children);
    const firstClone  = slides[0].cloneNode(true);
    const lastClone   = slides[slides.length - 1].cloneNode(true);
    firstClone.removeAttribute('id');
    lastClone.removeAttribute('id');

    track.appendChild(firstClone);   // clone pertama di posisi total+1
    track.insertBefore(lastClone, slides[0]); // clone terakhir di posisi 0

    // posisi awal = slide 1 (setelah clone terakhir)
    let current = 1; // real index dalam track (1 = slide pertama asli)
    let isTransitioning = false;

    function setPosition(idx, withTransition) {
        track.style.transition = withTransition
            ? 'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1)'
            : 'none';
        track.style.transform = `translateX(-${idx * 100}%)`;
    }

    function updateDots(realIdx) {
        // realIdx 1-based dalam track → 0-based untuk dots
        const dotIdx = ((realIdx - 1) + total) % total;
        dots.forEach((d, i) => d.classList.toggle('active', i === dotIdx));
    }

    // Init tanpa animasi
    setPosition(current, false);
    updateDots(current);

    function slideNext() {
        if (isTransitioning) return;
        isTransitioning = true;
        current++;
        setPosition(current, true);
        updateDots(current);
    }

    function slidePrev() {
        if (isTransitioning) return;
        isTransitioning = true;
        current--;
        setPosition(current, true);
        updateDots(current);
    }

    // Setelah transisi selesai: kalau di clone, snap diam-diam ke real slide
    track.addEventListener('transitionend', () => {
        isTransitioning = false;
        if (current === total + 1) {
            // Sampai di clone pertama → snap ke real slide pertama
            current = 1;
            setPosition(current, false);
        } else if (current === 0) {
            // Sampai di clone terakhir → snap ke real slide terakhir
            current = total;
            setPosition(current, false);
        }
    });

    next.addEventListener('click', slideNext);
    prev.addEventListener('click', slidePrev);

    // Klik dots
    dots.forEach((d, i) => d.addEventListener('click', () => {
        if (isTransitioning) return;
        isTransitioning = true;
        current = i + 1; // dots 0-based → track 1-based
        setPosition(current, true);
        updateDots(current);
    }));

    // Swipe touch
    let startX = 0;
    track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', e => {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) diff > 0 ? slideNext() : slidePrev();
    });
})();
</script>
<?php endif; ?>