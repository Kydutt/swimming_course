/* data_instruktur.js — Preview foto & auto-dismiss flash */

document.addEventListener('DOMContentLoaded', function () {

    // ── Preview foto sebelum upload ──
    const fotoInput    = document.getElementById('fotoInput');
    const previewImg   = document.getElementById('previewImg');
    const placeholder  = document.getElementById('fotoPlaceholder');

    if (fotoInput) {
        fotoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('File harus berupa gambar (JPG, PNG, atau WebP).');
                this.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran foto maksimal 2MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    }

    // ── Auto-dismiss flash message ──
    const flash = document.getElementById('flashMsg');
    if (flash) {
        setTimeout(function () {
            flash.style.transition = 'opacity 0.5s';
            flash.style.opacity = '0';
            setTimeout(function () { flash.remove(); }, 500);
        }, 3500);
    }
});
