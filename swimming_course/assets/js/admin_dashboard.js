// admin_dashboard.js

// ── Live Search ──
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
    if (target === 0) return;
    let current = 0;
    const step = Math.ceil(target / 25);
    el.textContent = '0';
    const timer = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = current;
        if (current >= target) clearInterval(timer);
    }, 40);
});

// ── Auto-dismiss success alert ──
const successAlert = document.getElementById('successAlert');
if (successAlert) {
    history.replaceState(null, '', window.location.pathname);
    successAlert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    setTimeout(() => {
        successAlert.style.opacity = '0';
        successAlert.style.transform = 'translateY(-8px)';
        setTimeout(() => successAlert.remove(), 600);
    }, 3000);
}
