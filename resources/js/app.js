document.addEventListener('DOMContentLoaded', () => {

    // ── Active nav highlighting (client-side fallback) ────────────
    const path = window.location.pathname;
    document.querySelectorAll('.nav-item').forEach(link => {
        const href = link.getAttribute('href');
        if (href && path.startsWith(href) && href !== '/') {
            link.classList.add('active');
        } else if (href === '/' && path === '/') {
            link.classList.add('active');
        }
    });

    // ── Tab switching ─────────────────────────────────────────────
    document.querySelectorAll('.tab-item').forEach(tab => {
        tab.addEventListener('click', function (e) {
            const tabBar = this.closest('.tab-bar');
            if (!tabBar) return;
            tabBar.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // ── View toggle ──────────────
    document.querySelectorAll('.view-toggle').forEach(group => {
        group.querySelectorAll('.vtog-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                group.querySelectorAll('.vtog-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterTable(this.textContent.trim().toLowerCase());
            });
        });
    });

    // ── Table filter by type ──────────────────────────────────────
    function filterTable(type) {
        const rows = document.querySelectorAll('.data-table tbody tr');
        rows.forEach(row => {
            if (type === 'all' || type === 'both') {
                row.style.display = '';
                return;
            }
            const dot = row.querySelector('.detail-dot');
            if (!dot) { row.style.display = ''; return; }
            const isPositive = dot.classList.contains('dot-green');
            if ((type === 'paid' || type === 'male') && isPositive) {
                row.style.display = '';
            } else if ((type === 'unpaid' || type === 'female') && !isPositive) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // ── Search filter ─────────────────────────────────────────────
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.data-table tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    // ── Pagination buttons ──────────────────────────────────
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (this.textContent === '‹' || this.textContent === '›') return;
            const container = this.closest('.page-buttons');
            container.querySelectorAll('.page-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // ── Auto-dismiss flash messages ───────────────────────────────
    const flash = document.querySelector('.flash-msg');
    if (flash) {
        setTimeout(() => flash.remove(), 4000);
    }

    // ── Form date validation ───────────────────────────────────────
    const fromDate = document.querySelector('input[name="from"], input[type="date"]:first-of-type');
    const toDate   = document.querySelector('input[name="to"], input[type="date"]:last-of-type');
    if (fromDate && toDate) {
        toDate.addEventListener('change', () => {
            if (fromDate.value && toDate.value < fromDate.value) {
                toDate.setCustomValidity('End date must be after start date');
                toDate.reportValidity();
            } else {
                toDate.setCustomValidity('');
            }
        });
    }

    // ── Highlighted row click ────────────────────────────
    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function () {
            document.querySelectorAll('.data-table tbody tr').forEach(r => r.classList.remove('highlighted'));
            this.classList.add('highlighted');
        });
    });

});
