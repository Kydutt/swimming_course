// laporan.js

// ── Count-up animation for stat numbers ──
document.querySelectorAll('.stat-number').forEach(el => {
    const t = parseInt(el.textContent) || 0;
    if (!t) return;
    let c = 0;
    const s = Math.ceil(t / 25);
    el.textContent = '0';
    const tm = setInterval(() => {
        c = Math.min(c + s, t);
        el.textContent = c;
        if (c >= t) clearInterval(tm);
    }, 40);
});

// ── Live search ──
const si = document.getElementById('searchInput');
if (si) si.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
