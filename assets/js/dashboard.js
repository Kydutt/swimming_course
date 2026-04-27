// dashboard.js — Shared scripts for all admin dashboard pages

// ── Hamburger / Sidebar toggle ──
const hamburger = document.getElementById('hamburgerBtn');
const sidebar   = document.getElementById('sidebar');
const overlay   = document.getElementById('sidebarOverlay');

function openSidebar()  { sidebar.classList.add('open');  overlay.classList.add('show'); }
function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

if (hamburger) hamburger.addEventListener('click', () =>
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar()
);
if (overlay) overlay.addEventListener('click', closeSidebar);

// ── Navigation exit animation ──
function navigateWithAnim(url) {
    document.body.classList.add('navigating');
    setTimeout(() => { window.location.href = url; }, 360);
}
document.querySelectorAll('a[href]').forEach(a => {
    if (a.getAttribute('onclick')) return;
    a.addEventListener('click', e => {
        e.preventDefault();
        navigateWithAnim(a.href);
    });
});
