// jadwal_latihan.js

// ── Auto-dismiss flash message ──
const flash = document.getElementById('flashMsg');
if (flash) {
    flash.style.transition = 'opacity .5s, transform .5s';
    setTimeout(() => {
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-8px)';
        setTimeout(() => flash.remove(), 500);
    }, 3500);
    history.replaceState(null, '', 'jadwal_latihan.php');
}

// ── Auto-fill keterangan from hari + waktu ──
const hariSel = document.querySelector('[name="hari"]');
const wMulai = document.querySelector('[name="waktu_mulai"]');
const wSelesai = document.querySelector('[name="waktu_selesai"]');
const ketInput = document.querySelector('[name="keterangan"]');

function autoKet() {
    if (!ketInput || ketInput.value.trim()) return;
    const h = hariSel ? hariSel.value : '';
    const wm = wMulai ? wMulai.value : '';
    const ws = wSelesai ? wSelesai.value : '';
    if (h && wm && ws) ketInput.placeholder = `${h}, ${wm} – ${ws}`;
}
[hariSel, wMulai, wSelesai].forEach(el => el && el.addEventListener('change', autoKet));
