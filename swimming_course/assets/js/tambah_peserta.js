// tambah_peserta.js

// ── Status pill selector ──
function selectStatus(val) {
    document.getElementById('statusInput').value = val;
    document.querySelectorAll('.status-pill').forEach(pill => {
        pill.classList.toggle('selected', pill.textContent.trim().includes(val));
    });
}

// ── Highlight selected pill on page load ──
const curStatus = document.getElementById('statusInput');
if (curStatus) {
    document.querySelectorAll('.status-pill').forEach(pill => {
        if (pill.textContent.trim().includes(curStatus.value)) {
            pill.classList.add('selected');
        }
    });
}
