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

<style>
/* ── Chat Panel ──────────────────────────────────────────────────────────────*/
.chat-panel {
    position: fixed;
    right: -380px;
    top: 0;
    height: 100vh;
    width: 360px;
    background: #fff;
    display: flex;
    flex-direction: column;
    z-index: 100;
    transition: right .3s cubic-bezier(.4,0,.2,1);
    box-shadow: -4px 0 40px rgba(0,0,0,0.12);
    border-left: 1px solid #f1f5f9;
}
.chat-panel.open { right: 0; }

.chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
    flex-shrink: 0;
}
.chat-header-left { display: flex; align-items: center; gap: 10px; }
.chat-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: rgba(255,255,255,0.18);
    display: flex; align-items: center; justify-content: center;
    color: white;
    backdrop-filter: blur(4px);
}
.chat-title   { font-size: 14px; font-weight: 700; color: white; letter-spacing: -.2px; }
.chat-subtitle { font-size: 11px; color: rgba(255,255,255,0.65); margin-top: 1px; }
.chat-header-actions { display: flex; align-items: center; gap: 4px; }
.chat-header-btn {
    width: 30px; height: 30px;
    border: none;
    background: rgba(255,255,255,0.15);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    color: white;
    transition: background .15s;
}
.chat-header-btn:hover { background: rgba(255,255,255,0.28); }

.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px 14px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: #ffffff;
    scroll-behavior: smooth;
}
.chat-body::-webkit-scrollbar { width: 4px; }
.chat-body::-webkit-scrollbar-track { background: transparent; }
.chat-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.chat-date-sep {
    display: flex; align-items: center; gap: 8px;
    margin: 2px 0;
}
.chat-date-sep span {
    font-size: 10.5px; color: #94a3b8;
    font-weight: 600; white-space: nowrap; letter-spacing: .3px;
}
.chat-date-sep::before,
.chat-date-sep::after {
    content: ''; flex: 1; height: 1px; background: #e2e8f0;
}

.chat-message {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    animation: msgIn .22s ease-out;
}
@keyframes msgIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.chat-message.user { flex-direction: row-reverse; }

.chat-avatar {
    width: 28px; height: 28px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700;
    flex-shrink: 0;
    margin-bottom: 2px;
}
.chat-message.assistant .chat-avatar {
    background: linear-gradient(135deg, #1e40af, #2563eb);
    color: white;
}
.chat-message.user .chat-avatar {
    display: none;
}

.msg-bubble {
    max-width: 82%;
    width: fit-content;
    border-radius: 16px;
    padding: 10px 14px;
    line-height: 1.6;
    position: relative;
    box-shadow: none !important;
    outline: none;
    border: none;
}
.chat-message.assistant .msg-bubble {
    background: #f1f5f9;
    color: #1e293b;
    border-bottom-left-radius: 4px;
}
.chat-message.user .msg-bubble {
    background: #2563eb;
    color: white;
    border-bottom-right-radius: 4px;
}

.chat-text { font-size: 13px; }
.chat-message.user .chat-text { color: white; }

.chat-link {
    color: #2563eb;
    font-weight: 600;
    text-decoration: none;
    background: #eff6ff;
    padding: 2px 8px;
    border-radius: 6px;
    border: 1px solid #bfdbfe;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: all .15s;
    cursor: pointer;
    white-space: nowrap;
}
.chat-link:hover { background: #dbeafe; border-color: #93c5fd; text-decoration: none; }
.chat-message.user .chat-link {
    color: white;
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.35);
}

.chat-suggestions { display: flex; flex-direction: column; gap: 6px; margin-top: 10px; }
.suggestion-chip {
    background: transparent;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 7px 12px;
    font-size: 12px;
    color: #2563eb;
    font-weight: 500;
    cursor: pointer;
    text-align: left;
    transition: all .15s;
    line-height: 1.4;
}
.suggestion-chip:hover {
    background: #eff6ff;
    border-color: #93c5fd;
    transform: translateX(3px);
}

.chat-typing { display: flex; align-items: center; gap: 4px; padding: 2px 0; }
.chat-typing span {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #94a3b8;
    animation: typingDot 1.2s infinite;
}
.chat-typing span:nth-child(2) { animation-delay: .2s; }
.chat-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes typingDot {
    0%, 60%, 100% { transform: translateY(0); opacity: .5; }
    30%           { transform: translateY(-5px); opacity: 1; }
}

.chat-footer {
    padding: 12px 14px 10px;
    background: white;
    border-top: 1px solid #f1f5f9;
    flex-shrink: 0;
}
.chat-input-form {
    display: flex; align-items: center; gap: 8px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 6px 6px 6px 14px;
    transition: border-color .15s, box-shadow .15s;
}

/* ── FIX: lighter focus ring so it doesn't clash with the blue send button */
.chat-input-form:focus-within {
    border-color: #93c5fd;
    box-shadow: 0 0 0 2px rgba(37,99,235,0.06);
}

.chat-input {
    flex: 1; border: none; background: transparent;
    font-size: 13px; color: #1e293b; outline: none; line-height: 1.4;
}
.chat-input::placeholder { color: #94a3b8; }

/* ── FIX: remove blue glow from send button so it doesn't double with focus ring */
.chat-send-btn {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1e40af, #2563eb);
    border: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: white; flex-shrink: 0;
    transition: transform .15s, opacity .15s;
    box-shadow: none;
}

.chat-send-btn:hover  { transform: scale(1.06); }
.chat-send-btn:active { transform: scale(.94); }
.chat-footer-note {
    text-align: center; font-size: 10.5px;
    color: #cbd5e1; margin-top: 6px; letter-spacing: .2px;
}

.chat-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.25); z-index: 99;
    backdrop-filter: blur(2px);
}
.chat-overlay.active { display: block; }
</style>

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
        </div>

        <nav class="sidebar-nav">
           @if(auth()->user()->role->name === 'Admin')
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
            <a href="{{ route('admin.sections') }}" class="nav-item {{ request()->routeIs('admin.sections*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Sections
            </a>
            <a href="{{ route('admin.schedule') }}" class="nav-item {{ request()->routeIs('admin.schedule*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Schedule
            </a>
            <a href="{{ route('admin.announcements') }}" class="nav-item {{ request()->routeIs('admin.announcements') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Announcements
            </a>

            <a href="{{ route('admin.calendar') }}" class="nav-item {{ request()->routeIs('admin.calendar*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12 6v2m0 8v2M8.5 9.5a3.5 1.5 0 0 1 7 0c0 .828-.597 1.57-1.5 2s-1.5 1.172-1.5 2a3.5 1.5 0 0 1-7 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Calendar
            </a>

            <a href="{{ route('admin.policies') }}" class="nav-item {{ request()->routeIs('admin.policies*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
                Policies
            </a>

            

            @elseif(auth()->user()->role->name === 'Teacher')
            <span class="nav-section-label">MAIN</span>
            <a href="{{ route('teacher.dashboard') }}" class="nav-item {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('teacher.class-list') }}" class="nav-item {{ request()->routeIs('teacher.class-list') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="9" cy="7" r="3" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M3 20c0-3.314 2.686-6 6-6s6 2.686 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M16 11c1.657 0 3-1.343 3-3s-1.343-3-3-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M21 20c0-3.314-2.239-6-5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Class List
            </a>
            <a href="{{ route('teacher.announcements') }}" class="nav-item {{ request()->routeIs('teacher.announcements') ? 'active' : '' }}">
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

            @else
            <span class="nav-section-label">MAIN</span>
            <a href="{{ route('student.dashboard') }}" class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.8"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('student.announcements') }}" class="nav-item {{ request()->routeIs('students.announcements') ? 'active' : '' }}">
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
                <button class="icon-btn chat-toggle-btn" onclick="toggleChatPanel()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9" cy="10" r="1" fill="currentColor"/>
                        <circle cx="12" cy="10" r="1" fill="currentColor"/>
                        <circle cx="15" cy="10" r="1" fill="currentColor"/>
                    </svg>
                </button>
                <div class="avatar-wrap dropdown dropdown-end">
                    <div tabindex="0" role="button" class="avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' '), 1, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        <div class="page-body">
            @yield('content')
        </div>

    </main>

    {{-- ── Chat Panel ── --}}
    <aside class="chat-panel" id="chatPanel">

        <div class="chat-header">
            <div class="chat-header-left">
                <div class="chat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9" cy="10" r="1" fill="currentColor"/>
                        <circle cx="12" cy="10" r="1" fill="currentColor"/>
                        <circle cx="15" cy="10" r="1" fill="currentColor"/>
                    </svg>
                </div>
                <div>
                    <div class="chat-title">SIS Assistant</div>
                    <div class="chat-subtitle">● Online — Ask me anything</div>
                </div>
            </div>
            <div class="chat-header-actions">
                <button onclick="clearChatHistory()" title="Clear chat" class="chat-header-btn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                        <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button onclick="toggleChatPanel()" title="Close" class="chat-header-btn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                        <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="chat-body" id="chatBody">
            <div class="chat-date-sep"><span>Today</span></div>
            <div class="chat-message assistant">
                <div class="chat-avatar">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="msg-bubble">
                    <div class="chat-text">
                        👋 Hi <strong>{{ explode(' ', auth()->user()->name)[0] }}</strong>! I'm your SIS assistant. How can I help you today?
                    </div>
                    <div class="chat-suggestions">
                        <button class="suggestion-chip" onclick="sendMessage('How many students are enrolled?')">📊 How many students are enrolled?</button>
                        <button class="suggestion-chip" onclick="sendMessage('What are the upcoming events?')">📅 What are the upcoming events?</button>
                        <button class="suggestion-chip" onclick="sendMessage('Show me active policies')">📋 Show me active policies</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="chat-footer">
            <form class="chat-input-form" onsubmit="handleChatSubmit(event)">
                <input type="text" class="chat-input" id="chatInput"
                    placeholder="Ask about students, events, policies..."
                    autocomplete="off">
                <button type="submit" class="chat-send-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>
            <div class="chat-footer-note">AI responses may not be 100% accurate</div>
        </div>

    </aside>

    {{-- Chat overlay --}}
    <div class="chat-overlay" id="chatOverlay" onclick="toggleChatPanel()"></div>

</div>

{{-- Loading Modal --}}
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

{{-- Confirmation Modal --}}
<div id="confirmation-modal" style="position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); display:none; justify-content:center; align-items:center; z-index:1000;">
    <div style="background:white; padding:25px; border-radius:8px; text-align:center; width:90%; max-width:400px;">
        <h3 id="modal-title" style="margin-top:0; font-size:1.25rem;">Confirmation</h3>
        <p id="modal-body" style="margin-bottom:25px;">Are you sure?</p>
        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button id="modal-cancel-btn" class="btn btn-secondary">Cancel</button>
            <button id="modal-confirm-btn" class="btn btn-danger">Confirm</button>
        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>

@stack('scripts')

<script>
// ── Default welcome HTML ──────────────────────────────────────────────────────
const defaultChatHTML = `
    <div class="chat-date-sep"><span>Today</span></div>
    <div class="chat-message assistant">
        <div class="chat-avatar">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                <path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="msg-bubble">
            <div class="chat-text">
                👋 Hi <strong>{{ explode(' ', auth()->user()->name)[0] }}</strong>! I'm your SIS assistant. How can I help you today?
            </div>
            <div class="chat-suggestions">
                <button class="suggestion-chip" onclick="sendMessage('How many students are enrolled?')">📊 How many students are enrolled?</button>
                <button class="suggestion-chip" onclick="sendMessage('What are the upcoming events?')">📅 What are the upcoming events?</button>
                <button class="suggestion-chip" onclick="sendMessage('Show me active policies')">📋 Show me active policies</button>
            </div>
        </div>
    </div>`;

// ── Chat Persistence ──────────────────────────────────────────────────────────
function saveChatHistory() {
    const chatBody = document.getElementById('chatBody');
    if (!chatBody) return;
    sessionStorage.setItem('sis_chat_html', chatBody.innerHTML);
}

function restoreChatHistory() {
    const chatBody = document.getElementById('chatBody');
    if (!chatBody) return;
    const saved = sessionStorage.getItem('sis_chat_html');
    if (saved) {
        chatBody.innerHTML = saved;
        chatBody.scrollTop = chatBody.scrollHeight;
    }
}

function clearChatHistory() {
    sessionStorage.removeItem('sis_chat_html');
    const chatBody = document.getElementById('chatBody');
    if (chatBody) {
        chatBody.innerHTML = defaultChatHTML;
        chatBody.scrollTop = 0;
    }
}

// ── DOM Ready ─────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const loadingModal = document.getElementById('loading-modal');

    // ── Restore or clear based on navigation type ─────────────────────────────
    // 'reload'   = hard refresh / F5 / Ctrl+Shift+R → clear chat
    // 'navigate' = clicking sidebar links            → restore chat
    const navType = performance.getEntriesByType('navigation')[0]?.type;
    if (navType === 'reload') {
        sessionStorage.removeItem('sis_chat_html');
    } else {
        restoreChatHistory();
    }

    // ── Logout ────────────────────────────────────────────────────────────────
    const logoutButton = document.getElementById('sidebar-logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', function(e) {
            e.preventDefault();
            showConfirmationModal(
                'Logout Confirmation',
                'Are you sure you want to log out?',
                function() {
                    clearChatHistory();
                    if (loadingModal) loadingModal.style.display = 'flex';
                    document.getElementById('logout-form').submit();
                }
            );
        });
    }

    // ── Page Transition Loading Modal ─────────────────────────────────────────
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-item');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (link.target === '_blank') return;
            if (loadingModal) loadingModal.style.display = 'flex';
        });
    });

    window.addEventListener('pageshow', function(event) {
        if (loadingModal) loadingModal.style.display = 'none';
    });
});

// ── Confirmation Modal ────────────────────────────────────────────────────────
function showConfirmationModal(title, body, onConfirm) {
    const modal      = document.getElementById('confirmation-modal');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    const cancelBtn  = document.getElementById('modal-cancel-btn');

    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-body').textContent  = body;

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

// ── Chat Panel ────────────────────────────────────────────────────────────────
function toggleChatPanel() {
    const panel   = document.getElementById('chatPanel');
    const overlay = document.getElementById('chatOverlay');
    if (!panel || !overlay) return;

    const isOpen = panel.classList.contains('open');
    if (isOpen) {
        panel.classList.remove('open');
        overlay.classList.remove('active');
    } else {
        panel.classList.add('open');
        overlay.classList.add('active');
        setTimeout(() => {
            const input = document.getElementById('chatInput');
            if (input) input.focus();
        }, 300);
    }
}

function handleChatSubmit(event) {
    event.preventDefault();
    const input   = document.getElementById('chatInput');
    const message = input.value.trim();
    if (message) {
        sendMessage(message);
        input.value = '';
    }
}

function sendMessage(message) {
    const chatBody = document.getElementById('chatBody');
    if (!chatBody) return;

    document.querySelectorAll('.chat-suggestions').forEach(el => el.style.display = 'none');

    appendMessage('user', message);

    const typingIndicator = createTypingIndicator();
    chatBody.appendChild(typingIndicator);
    chatBody.scrollTop = chatBody.scrollHeight;

    $.ajax({
        url:    '{{ route("chat.send") }}',
        method: 'POST',
        data:   { message: message, _token: '{{ csrf_token() }}' },
        success: function (response) {
            typingIndicator.remove();
            appendMessage('assistant', response.reply);
        },
        error: function () {
            typingIndicator.remove();
            appendMessage('assistant', 'Sorry, something went wrong. Please try again.');
        }
    });
}

function appendMessage(role, text) {
    const chatBody = document.getElementById('chatBody');
    if (!chatBody) return;

    const messageDiv = document.createElement('div');
    messageDiv.className = 'chat-message ' + role;

    const avatar = document.createElement('div');
    avatar.className = 'chat-avatar';

    if (role === 'assistant') {
        avatar.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
            <path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>`;
    } else {
        avatar.textContent = '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}';
    }

    const bubble  = document.createElement('div');
    bubble.className = 'msg-bubble';

    const textDiv = document.createElement('div');
    textDiv.className = 'chat-text';

    if (role === 'assistant') {
        textDiv.innerHTML = text.replace(/\n/g, '<br>');
    } else {
        textDiv.textContent = text;
    }

    bubble.appendChild(textDiv);
    messageDiv.appendChild(avatar);
    messageDiv.appendChild(bubble);
    chatBody.appendChild(messageDiv);
    chatBody.scrollTop = chatBody.scrollHeight;

    saveChatHistory();
}

function createTypingIndicator() {
    const typing = document.createElement('div');
    typing.className = 'chat-message assistant';
    typing.innerHTML = `
        <div class="chat-avatar">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
            </svg>
        </div>
        <div class="msg-bubble">
            <div class="chat-text">
                <div class="chat-typing"><span></span><span></span><span></span></div>
            </div>
        </div>`;
    return typing;
}

// ── ESC closes chat ───────────────────────────────────────────────────────────
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const panel = document.getElementById('chatPanel');
        if (panel && panel.classList.contains('open')) toggleChatPanel();
    }
});
</script>

</body>
</html>