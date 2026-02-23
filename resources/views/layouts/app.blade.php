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

        </nav>

        <div class="sidebar-footer">
            <button class="btn-switch">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                    <path d="M8 7l-5 5 5 5M16 7l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Switch Role
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
                <div class="avatar-wrap">
                    <div class="avatar">AD</div>
                </div>
            </div>
        </header>

        {{-- Page Body --}}
        <div class="page-body">
            @yield('content')
        </div>

    </main>
</div>
</body>
</html>
