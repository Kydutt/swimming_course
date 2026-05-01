/* ============================================
   tambah_peserta.js
   ============================================ */

function selectStatus(val) {
    document.getElementById('statusInput').value = val;
    document.querySelectorAll('.status-pill').forEach(pill => {
        pill.classList.toggle('selected', pill.textContent.trim().includes(val));
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const curStatus = document.getElementById('statusInput').value;
    document.querySelectorAll('.status-pill').forEach(pill => {
        if (pill.textContent.trim().includes(curStatus)) pill.classList.add('selected');
    });
});
