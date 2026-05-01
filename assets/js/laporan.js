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

// ── Charts (data disediakan oleh laporan.php via window.laporanData) ──
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined' || typeof window.laporanData === 'undefined') return;

    const d            = window.laporanData;
    const gridColor    = 'rgba(226,232,240,0.6)';
    const colorBlue    = '#2563eb';
    const colorTeal    = '#0891b2';
    const colorGreen   = '#10b981';

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color       = '#64748b';

    // ── 1. Line: Perkembangan Peserta per Bulan ──
    new Chart(document.getElementById('chartPesertaBulan'), {
        type: 'line',
        data: {
            labels: d.bulanLabels,
            datasets: [{
                label: 'Pendaftaran Masuk',
                data: d.bulanJumlah,
                borderColor: colorBlue,
                backgroundColor: 'rgba(37,99,235,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: colorBlue,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: {
                label: ctx => ` ${ctx.raw} pendaftar`
            }}},
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor } },
                x: { grid: { display: false } }
            }
        }
    });

    // ── 2. Doughnut: Status Peserta ──
    new Chart(document.getElementById('chartStatusPeserta'), {
        type: 'doughnut',
        data: {
            labels: ['Pending','Approved','Completed','Rejected'],
            datasets: [{
                data: d.statusData,
                backgroundColor: ['#f59e0b','#10b981','#2563eb','#ef4444'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} peserta` } }
            }
        }
    });

    // ── 3. Bar: Pendapatan Keuangan per Bulan ──
    new Chart(document.getElementById('chartKeuanganBulan'), {
        type: 'bar',
        data: {
            labels: d.bulanLabels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: d.bulanPendapatan,
                backgroundColor: 'rgba(8,145,178,0.75)',
                borderColor: colorTeal,
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
                maxBarThickness: 60
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: {
                label: ctx => ` Rp ${ctx.raw.toLocaleString('id-ID')}`
            }}},
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: {
                    callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v.toLocaleString('id-ID'))
                }},
                x: { grid: { display: false } }
            }
        }
    });

    // ── 4. Bar: Peserta per Program ──
    new Chart(document.getElementById('chartProgram'), {
        type: 'bar',
        data: {
            labels: d.progLabels,
            datasets: [
                {
                    label: 'Jumlah Peserta',
                    data: d.progJumlah,
                    backgroundColor: 'rgba(37,99,235,0.75)',
                    borderColor: colorBlue,
                    borderWidth: 1.5,
                    borderRadius: 6,
                    maxBarThickness: 60,
                    yAxisID: 'yLeft'
                },
                {
                    label: 'Pendapatan (Rp)',
                    data: d.progPendapatan,
                    backgroundColor: 'rgba(16,185,129,0.6)',
                    borderColor: colorGreen,
                    borderWidth: 1.5,
                    borderRadius: 6,
                    maxBarThickness: 60,
                    yAxisID: 'yRight'
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 14, font: { size: 11 } } },
                tooltip: { callbacks: {
                    label: ctx => ctx.datasetIndex === 0
                        ? ` ${ctx.raw} peserta`
                        : ` Rp ${ctx.raw.toLocaleString('id-ID')}`
                }}
            },
            scales: {
                yLeft:  { beginAtZero: true, position: 'left',  ticks: { stepSize: 1 }, grid: { color: gridColor } },
                yRight: { beginAtZero: true, position: 'right', grid: { display: false }, ticks: {
                    callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v.toLocaleString('id-ID'))
                }},
                x: { grid: { display: false } }
            }
        }
    });
});
