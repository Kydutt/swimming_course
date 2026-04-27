// manajemen_user.js

// ── Live search ──
const si = document.getElementById('searchInput');
if (si) si.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// ── Auto-dismiss flash message ──
const flash = document.getElementById('flashMsg');
if (flash) {
    flash.style.transition = 'opacity .5s, transform .5s';
    setTimeout(() => {
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-8px)';
        setTimeout(() => flash.remove(), 500);
    }, 3500);
    history.replaceState(null, '', 'manajemen_user.php');
}
