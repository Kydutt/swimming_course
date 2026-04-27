// pembayaran.js

// ── Live search ──
const si = document.getElementById('searchInput');
if (si) si.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
