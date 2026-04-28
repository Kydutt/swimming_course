// data_siswa.js — Search, Filter, Pagination

(function () {
    const ROWS_PER_PAGE = 10;
    const table = document.getElementById('dataTable');
    if (!table) return;

    const tbody = table.querySelector('tbody');
    const allRows = Array.from(tbody.querySelectorAll('tr'));
    const globalSearch = document.getElementById('globalSearch');
    const filterLevel = document.getElementById('filterLevel');
    const filterStatus = document.getElementById('filterStatus');
    const showingInfo = document.getElementById('showingInfo');
    const totalEntries = document.getElementById('totalEntries');
    const paginationEl = document.getElementById('pagination');

    let currentPage = 1;

    function getVisibleRows() {
        const q = (globalSearch ? globalSearch.value : '').toLowerCase();
        const level = filterLevel ? filterLevel.value : '';
        const status = filterStatus ? filterStatus.value : '';

        return allRows.filter(tr => {
            const text = tr.textContent.toLowerCase();
            const rowLevel = tr.dataset.level || '';
            const rowStatus = tr.dataset.status || '';

            if (q && !text.includes(q)) return false;
            if (level && rowLevel !== level) return false;
            if (status && rowStatus !== status) return false;
            return true;
        });
    }

    function render() {
        const visible = getVisibleRows();
        const totalPages = Math.max(1, Math.ceil(visible.length / ROWS_PER_PAGE));
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageRows = visible.slice(start, end);

        allRows.forEach(r => r.style.display = 'none');
        pageRows.forEach(r => r.style.display = '');

        if (showingInfo) {
            if (visible.length === 0) {
                showingInfo.textContent = '0';
            } else {
                showingInfo.textContent = `${start + 1} to ${Math.min(end, visible.length)}`;
            }
        }
        if (totalEntries) totalEntries.textContent = visible.length;

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (!paginationEl) return;
        paginationEl.innerHTML = '';

        const prevBtn = document.createElement('button');
        prevBtn.textContent = 'Prev';
        prevBtn.disabled = currentPage <= 1;
        prevBtn.addEventListener('click', () => { currentPage--; render(); });
        paginationEl.appendChild(prevBtn);

        const maxVisible = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);
        if (endPage - startPage < maxVisible - 1) startPage = Math.max(1, endPage - maxVisible + 1);

        if (startPage > 1) {
            paginationEl.appendChild(createPageBtn(1));
            if (startPage > 2) {
                const dots = document.createElement('span');
                dots.className = 'page-ellipsis';
                dots.textContent = '...';
                paginationEl.appendChild(dots);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationEl.appendChild(createPageBtn(i));
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dots = document.createElement('span');
                dots.className = 'page-ellipsis';
                dots.textContent = '...';
                paginationEl.appendChild(dots);
            }
            paginationEl.appendChild(createPageBtn(totalPages));
        }

        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Next';
        nextBtn.disabled = currentPage >= totalPages;
        nextBtn.addEventListener('click', () => { currentPage++; render(); });
        paginationEl.appendChild(nextBtn);
    }

    function createPageBtn(page) {
        const btn = document.createElement('button');
        btn.textContent = page;
        if (page === currentPage) btn.classList.add('active');
        btn.addEventListener('click', () => { currentPage = page; render(); });
        return btn;
    }

    // Event listeners
    if (globalSearch) globalSearch.addEventListener('input', () => { currentPage = 1; render(); });
    if (filterLevel) filterLevel.addEventListener('change', () => { currentPage = 1; render(); });
    if (filterStatus) filterStatus.addEventListener('change', () => { currentPage = 1; render(); });

    // Attendance bar animation
    document.querySelectorAll('.attendance-fill').forEach(bar => {
        const w = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => { bar.style.width = w; }, 300);
    });

    // Auto-dismiss alert
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
        history.replaceState(null, '', window.location.pathname);
        successAlert.style.transition = 'opacity .5s ease, transform .5s ease';
        setTimeout(() => {
            successAlert.style.opacity = '0';
            successAlert.style.transform = 'translateY(-8px)';
            setTimeout(() => successAlert.remove(), 500);
        }, 3000);
    }

    // Initial render
    render();
})();
