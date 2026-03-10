// ── Global Loading Modal Control ─────────────────────────────────────────────
window.loadingModal = {
    show: function() {
        var el = document.getElementById('loading-modal');
        if (el) el.classList.add('show');
    },
    hide: function() {
        var el = document.getElementById('loading-modal');
        if (el) el.classList.remove('show');
    }
};

// ── Global Popup ──────────────────────────────────────────────────────────────
window.showPopup = function(title, message, type) {
    type = type || 'success';

    if (!document.getElementById('popup-modal')) {
        $('#sis-popup').remove();

        var colors = {
            success: { bg: '#dcfce7', border: '#16a34a', text: '#16a34a' },
            warning: { bg: '#fef3c7', border: '#d97706', text: '#d97706' },
            error:   { bg: '#fee2e2', border: '#dc2626', text: '#dc2626' },
            info:    { bg: '#dbeafe', border: '#3b82f6', text: '#3b82f6' },
        };
        var icons = {
            success: '<path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
            warning: '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
            error:   '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
            info:    '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        };
        var c = colors[type] || colors.success;

        var popup = $(
            '<div id="sis-popup" style="' +
                'position:fixed;top:24px;right:24px;z-index:9999;' +
                'background:' + c.bg + ';' +
                'border:1.5px solid ' + c.border + ';' +
                'border-radius:10px;' +
                'padding:14px 18px;' +
                'min-width:280px;max-width:380px;' +
                'box-shadow:0 4px 16px rgba(0,0,0,.10);' +
                'display:flex;align-items:flex-start;gap:12px;' +
                'animation:slideInRight .25s ease-out;' +
            '">' +
                '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;margin-top:1px;color:' + c.text + '">' +
                    icons[type] +
                '</svg>' +
                '<div style="flex:1;">' +
                    '<div style="font-weight:700;font-size:13.5px;color:' + c.text + ';margin-bottom:3px;">' + title + '</div>' +
                    '<div style="font-size:12.5px;color:#334155;white-space:pre-line;">' + message + '</div>' +
                '</div>' +
                '<button onclick="$(\'#sis-popup\').remove()" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;line-height:1;font-size:16px;">✕</button>' +
            '</div>'
        );

        $('body').append(popup);
        setTimeout(() => $('#sis-popup').fadeOut(300, function(){ $(this).remove(); }), 4000);
        return;
    }

    var config = {
        success: { icon: '✅', color: '#10b981' },
        error:   { icon: '❌', color: '#ef4444' },
        warning: { icon: '⚠️',  color: '#f59e0b' },
        info:    { icon: 'ℹ️',  color: '#3b82f6' },
    };
    var cfg = config[type] || config.success;
    document.getElementById('popup-icon').textContent         = cfg.icon;
    document.getElementById('popup-title').textContent        = title;
    document.getElementById('popup-message').textContent      = message;
    document.getElementById('popup-box').style.borderTopColor = cfg.color;
    document.getElementById('popup-modal').style.display      = 'flex';
};

// ── Slide-in animation ────────────────────────────────────────────────────────
const style = document.createElement('style');
style.textContent = '@keyframes slideInRight { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }';
document.head.appendChild(style);

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

    // ── View toggle ───────────────────────────────────────────────
    document.querySelectorAll('.view-toggle').forEach(group => {
        group.querySelectorAll('.vtog-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                group.querySelectorAll('.vtog-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                let role = this.textContent.trim().toLowerCase();
                setGender(this, role);
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

    // ── Pagination buttons ────────────────────────────────────────
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

    // ── Form date validation ──────────────────────────────────────
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

    // ── Highlighted row click ─────────────────────────────────────
    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function () {
            document.querySelectorAll('.data-table tbody tr').forEach(r => r.classList.remove('highlighted'));
            this.classList.add('highlighted');
        });
    });

    // ── Page Transition Loading ───────────────────────────────────
    document.querySelectorAll('a').forEach(link => {
        if (link.hostname === window.location.hostname && link.target !== '_blank' && !link.href.startsWith('javascript:')) {
            link.addEventListener('click', function(e) {
                if (this.pathname === window.location.pathname && this.hash) return;
                loadingModal.show();
            });
        }
    });

    // ── Popup modal backdrop click to close ───────────────────────
    var popupModal = document.getElementById('popup-modal');
    if (popupModal) {
        popupModal.addEventListener('click', function (e) {
            if (e.target === this) closePopup();
        });
    }

});