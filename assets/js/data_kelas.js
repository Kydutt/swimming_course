// data_kelas.js

// ── Auto-dismiss flash message ──
const flash = document.getElementById('flashMsg');
if (flash) {
    flash.style.transition = 'opacity .5s, transform .5s';
    setTimeout(() => {
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-8px)';
        setTimeout(() => flash.remove(), 500);
    }, 3500);
    history.replaceState(null, '', 'data_kelas.php');
}

// ── Strip non-numeric chars from price input ──
const hargaInput = document.querySelector('[name="harga"]');
if (hargaInput) {
    hargaInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
}
