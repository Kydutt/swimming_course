// data_siswa.js

// ── Live search ──
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}

// ── Count-up animation for stat numbers ──
document.querySelectorAll('.stat-number').forEach(el => {
    const target = parseInt(el.textContent) || 0;
    if (!target) return;
    let cur = 0;
    const step = Math.ceil(target / 25);
    el.textContent = '0';
    const t = setInterval(() => {
        cur = Math.min(cur + step, target);
        el.textContent = cur;
        if (cur >= target) clearInterval(t);
    }, 40);
});
