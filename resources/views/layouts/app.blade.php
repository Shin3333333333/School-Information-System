<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School Information System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:..." rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<body>

<div class="app-shell">

    {{-- ── Sidebar ── --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 3L1 9l11 6 11-6-11-6z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M1 9v6" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                    <path d="M23 9v6" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                    <path d="M1 15l11 6 11-6" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
                </svg>
            </div>
            <span class="brand-name">SIS</span>
        </div>

        <div class="sidebar-account-card">
            <div class="account-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="5" width="20" height="14" rx="2" stroke="#2563eb" stroke-width="2"/>
                    <path d="M2 10h20" stroke="#2563eb" stroke-width="2"/>
                </svg>
            </div>
            <div class="account-info">
                <span class="account-label">Academic Year</span>
                <span class="account-value">2024–2025</span>
            </div>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="chevron">
                <path d="M6 9l6 6 6-6" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <nav class="sidebar-nav">
            @if(session('user_role') == 'Admin')
            <span class="nav-section-label">MAIN</span>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('students.index') }}" class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M21 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                User Management
            </a>

            <a href="{{ route('enrollment.index') }}" class="nav-item {{ request()->routeIs('enrollment.*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Announcements
            </a>

            <a href="{{ route('fees.index') }}" class="nav-item {{ request()->routeIs('fees.*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12 6v2m0 8v2M8.5 9.5a3.5 1.5 0 0 1 7 0c0 .828-.597 1.57-1.5 2s-1.5 1.172-1.5 2a3.5 1.5 0 0 1-7 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Calendar
            </a>

            <a href="{{ route('grades.index') }}" class="nav-item {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
                Policies
            </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <button class="btn-switch" id="sidebar-logout-button">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Logout
            </button>
        </div>
    </aside>

    <main class="main-content">

        {{-- Top Bar --}}
        <header class="topbar">
            <div class="topbar-left">
                @yield('page-title')
            </div>
            <div class="topbar-right">
                <div class="search-wrap">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                        <circle cx="11" cy="11" r="8" stroke="#94a3b8" stroke-width="2"/>
                        <path d="m21 21-4.35-4.35" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input type="text" placeholder="Search student, ID, class…" class="search-input">
                </div>
                <button class="icon-btn notif-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <span class="badge">3</span>
                </button>
                <div class="avatar-wrap dropdown dropdown-end">
                    <div tabindex="0" role="button" class="avatar">AD</div>
                  
                </div>
            </div>
        </header>

        {{-- Page Body --}}
        <div class="page-body">
            @yield('content')
        </div>

    </main>
</div>
<div id="loading-modal" class="loading-modal-overlay">
    <div class="loading-modal-content">
        <span class="loading loading-ring loading-xs"></span>
        <span class="loading loading-ring loading-sm"></span>
        <span class="loading loading-ring loading-md"></span>
        <span class="loading loading-ring loading-lg"></span>
        <span class="loading loading-ring loading-xl"></span>
        <span class="loading loading-ring loading-2xl"></span>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmation-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); display: none; justify-content: center; align-items: center; z-index: 1000;">
    <div style="background: white; padding: 25px; border-radius: 8px; text-align: center; width: 90%; max-width: 400px;">
        <h3 id="modal-title" style="margin-top: 0; font-size: 1.25rem;">Confirmation</h3>
        <p id="modal-body" style="margin-bottom: 25px;">Are you sure?</p>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button id="modal-cancel-btn" class="btn btn-secondary">Cancel</button>
            <button id="modal-confirm-btn" class="btn btn-danger">Confirm</button>
        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

@stack('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingModal = document.getElementById('loading-modal');

    // --- Logout Modal Logic ---
    const logoutButton = document.getElementById('sidebar-logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', function(e) {
            e.preventDefault();
            showConfirmationModal(
                'Logout Confirmation',
                'Are you sure you want to log out?',
                function() {
                    if (loadingModal) loadingModal.style.display = 'flex';
                    document.getElementById('logout-form').submit();
                }
            );
        });
    }

    // --- Page Transition Loading Modal ---
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-item');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            // Don't show for links opening in new tabs
            if (link.target === '_blank') {
                return;
            }
            if (loadingModal) loadingModal.style.display = 'flex';
        });
    });

    // Hide loading modal if user navigates back
    window.addEventListener('pageshow', function(event) {
        if (loadingModal) loadingModal.style.display = 'none';
    });
});

function showConfirmationModal(title, body, onConfirm) {
    const modal = document.getElementById('confirmation-modal');
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-body').textContent = body;

    const confirmBtn = document.getElementById('modal-confirm-btn');
    const cancelBtn = document.getElementById('modal-cancel-btn');

    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

    newConfirmBtn.addEventListener('click', function() {
        onConfirm();
        modal.style.display = 'none';
    });

    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    modal.style.display = 'flex';
}
</script>

</body>
</html>