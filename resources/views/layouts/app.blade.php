<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School Information System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<body>

<style>
/* ── Clickable account card ─────────────────────────────────────────────────*/
.sidebar-account-card.a_is-link {
    text-decoration: none;
    transition: all .2s ease;
}
.sidebar-account-card.a_is-link:hover {
    background: rgba(255,255,255,0.09) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* ── Pulse Indicator ────────────────────────────────────────────────────────*/
.pulse-dot {
    width: 6px;
    height: 6px;
    background-color: #10b981;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulse 2s infinite cubic-bezier(0.66, 0, 0, 1);
}
@keyframes pulse {
    to { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
}

/* ── Management accordion nav group ─────────────────────────────────────────*/
.nav-group { display: flex; flex-direction: column; }

.nav-group-trigger {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: var(--radius-md);
    color: #64748b;
    font-weight: 500;
    font-size: 13px;
    cursor: pointer;
    user-select: none;
    transition: background .15s, color .15s;
    white-space: nowrap;
    position: relative;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    font-family: var(--font-body);
}
.nav-group-trigger:hover { background: rgba(255,255,255,0.06); color: #94a3b8; }
.nav-group-trigger.active-group { color: #94a3b8; background: rgba(255,255,255,0.04); }
.nav-group-trigger svg.trigger-icon { flex-shrink: 0; opacity: .7; }
.nav-group-trigger span.trigger-label { flex: 1; }

/* Chevron rotates when open */
.nav-group-chevron {
    width: 14px; height: 14px;
    flex-shrink: 0;
    opacity: .45;
    transition: transform .22s ease, opacity .15s;
}
.nav-group-trigger[aria-expanded="true"] .nav-group-chevron {
    transform: rotate(180deg);
    opacity: .7;
}

/* Collapsed sidebar: hide labels + chevron */
@media (max-width: 860px) {
    .nav-group-trigger span.trigger-label,
    .nav-group-chevron { display: none; }
    .nav-group-trigger { justify-content: center; padding: 10px; gap: 0; }
}

/* Sliding children panel */
.nav-group-children {
    display: flex;
    flex-direction: column;
    gap: 2px;
    overflow: hidden;
    max-height: 0;
    transition: max-height .28s cubic-bezier(.4,0,.2,1), opacity .22s ease;
    opacity: 0;
    padding-left: 28px;   /* indent under trigger icon */
}
.nav-group-children.open {
    max-height: 400px;    /* large enough for all children */
    opacity: 1;
}

/* Child nav items are slightly smaller */
.nav-group-children .nav-item {
    font-size: 12.5px;
    padding: 8px 12px;
}
.nav-group-children .nav-item::before {
    /* keep the left accent bar */
}

/* Active indicator on the trigger when any child is active */
.nav-group-trigger.active-group {
    color: #fff;
    font-weight: 600;
}
.nav-group-trigger.active-group .trigger-icon { opacity: 1; }

/* Collapsed sidebar: show children as tooltip/popover – for now just hide */
@media (max-width: 860px) {
    .nav-group-children { padding-left: 0; }
    .nav-group-children .nav-item { justify-content: center; padding: 10px; gap: 0; }
    .nav-group-children .nav-item span { display: none; }
}

/* ── Chat Panel ──────────────────────────────────────────────────────────────*/
.chat-panel {
    position: fixed;
    right: -380px;
    top: 0;
    height: 100vh;
    width: 360px;
    background: var(--dk-surface);
    display: flex;
    flex-direction: column;
    z-index: 100;
    transition: right .3s cubic-bezier(.4,0,.2,1);
    box-shadow: -4px 0 40px rgba(0,0,0,0.5);
    border-left: 1px solid var(--dk-b1);
}
.chat-panel.open { right: 0; }

/* ── Chat Header ── */
.chat-header {
    display: flex; 
    align-items: center; 
    justify-content: space-between;
    padding: 14px 16px;
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
    flex-shrink: 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.chat-header-left { 
    display: flex; 
    align-items: center; 
    gap: 10px; 
}
.chat-icon {
    width: 36px; 
    height: 36px; 
    border-radius: 10px;
    background: rgba(255,255,255,0.18);
    display: flex; 
    align-items: center; 
    justify-content: center;
    color: white; 
    backdrop-filter: blur(4px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.chat-title { 
    font-size: 14px; 
    font-weight: 700; 
    color: white; 
    letter-spacing: -.2px; 
}
.chat-subtitle { 
    font-size: 11px; 
    color: rgba(255,255,255,0.75); 
    margin-top: 1px; 
}
.chat-header-actions { 
    display: flex; 
    align-items: center; 
    gap: 4px; 
}
.chat-header-btn {
    width: 30px; 
    height: 30px; 
    border: none;
    background: rgba(255,255,255,0.15); 
    border-radius: 8px;
    display: flex; 
    align-items: center; 
    justify-content: center;
    cursor: pointer; 
    color: white; 
    transition: all .15s;
}
.chat-header-btn:hover { 
    background: rgba(255,255,255,0.28); 
    transform: scale(1.05);
}

/* ── Chat Body ── */
.chat-body {
    flex: 1; 
    overflow-y: auto; 
    padding: 16px 14px;
    display: flex; 
    flex-direction: column; 
    gap: 12px;
    background: var(--dk-bg);
    scroll-behavior: smooth;
}
.chat-body::-webkit-scrollbar { width: 4px; }
.chat-body::-webkit-scrollbar-track { background: transparent; }
.chat-body::-webkit-scrollbar-thumb { 
    background: rgba(255,255,255,0.1); 
    border-radius: 4px; 
}
.chat-body::-webkit-scrollbar-thumb:hover { 
    background: rgba(255,255,255,0.2); 
}

/* ── Date Separator ── */
.chat-date-sep { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    margin: 2px 0; 
}
.chat-date-sep span { 
    font-size: 10.5px; 
    color: var(--dk-t4); 
    font-weight: 600; 
    white-space: nowrap; 
    letter-spacing: .3px; 
}
.chat-date-sep::before, 
.chat-date-sep::after { 
    content: ''; 
    flex: 1; 
    height: 1px; 
    background: var(--dk-b2); 
}

/* ── Message Bubbles ── */
.chat-message { 
    display: flex; 
    align-items: flex-end; 
    gap: 8px; 
    animation: msgIn .22s ease-out; 
}
@keyframes msgIn { 
    from { opacity: 0; transform: translateY(8px); } 
    to { opacity: 1; transform: translateY(0); } 
}
.chat-message.user { 
    flex-direction: row-reverse; 
}

/* ── Avatar Styles ── */
.chat-avatar {
    width: 28px; 
    height: 28px; 
    border-radius: 50%;
    display: flex; 
    align-items: center; 
    justify-content: center;
    font-size: 11px; 
    font-weight: 700; 
    flex-shrink: 0; 
    margin-bottom: 2px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.chat-message.assistant .chat-avatar { 
    background: linear-gradient(135deg, #1e40af, #2563eb); 
    color: white; 
}
.chat-message.user .chat-avatar { 
    background: linear-gradient(135deg, #334155, #1e293b); 
    color: #94a3b8; 
}

/* ── Message Bubble ── */
.msg-bubble {
    max-width: 82%; 
    width: fit-content; 
    border-radius: 16px;
    padding: 10px 14px; 
    line-height: 1.6; 
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    word-wrap: break-word;
}
.chat-message.assistant .msg-bubble { 
    background: var(--dk-surface); 
    color: var(--dk-t1); 
    border-bottom-left-radius: 4px; 
    border: 1px solid var(--dk-b1); 
}
.chat-message.user .msg-bubble { 
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%); 
    color: white; 
    border-bottom-right-radius: 4px; 
}
.chat-text { 
    font-size: 13px; 
    line-height: 1.6;
}
.chat-message.user .chat-text { 
    color: white; 
}
.chat-message.assistant .chat-text {
    color: var(--dk-t1);
}

/* ── Links inside messages ── */
.chat-link {
    color: #60a5fa; 
    font-weight: 600; 
    text-decoration: none;
    background: rgba(37,99,235,0.15); 
    padding: 2px 8px; 
    border-radius: 6px;
    border: 1px solid rgba(96,165,250,0.3); 
    font-size: 12px;
    display: inline-flex; 
    align-items: center; 
    gap: 3px;
    transition: all .15s; 
    cursor: pointer; 
    white-space: nowrap;
}
.chat-link:hover { 
    background: rgba(37,99,235,0.25); 
    border-color: rgba(96,165,250,0.5); 
    text-decoration: none; 
}
.chat-message.user .chat-link { 
    color: white; 
    background: rgba(255,255,255,0.2); 
    border-color: rgba(255,255,255,0.35); 
}

/* ── Suggestion Chips ── */
.chat-suggestions { 
    display: flex; 
    flex-direction: column; 
    gap: 6px; 
    margin-top: 10px; 
}
.suggestion-chip {
    background: var(--dk-surface2);
    border: 1.5px solid var(--dk-b2);
    border-radius: 10px; 
    padding: 7px 12px; 
    font-size: 12px;
    color: #60a5fa; 
    font-weight: 500; 
    cursor: pointer; 
    text-align: left;
    transition: all .15s; 
    line-height: 1.4;
    font-family: var(--font-body);
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}
.suggestion-chip:hover { 
    background: rgba(37,99,235,0.15); 
    border-color: rgba(96,165,250,0.4); 
    transform: translateX(3px); 
    color: #93c5fd;
}

/* ── Typing Indicator ── */
.chat-typing { 
    display: flex; 
    align-items: center; 
    gap: 4px; 
    padding: 2px 0; 
}
.chat-typing span { 
    width: 7px; 
    height: 7px; 
    border-radius: 50%; 
    background: #60a5fa; 
    animation: typingDot 1.2s infinite; 
    opacity: 0.7;
}
.chat-typing span:nth-child(2) { animation-delay: .2s; }
.chat-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes typingDot { 
    0%, 60%, 100% { transform: translateY(0); opacity: 0.5; } 
    30% { transform: translateY(-5px); opacity: 1; } 
}

/* ── Chat Footer ── */
.chat-footer { 
    padding: 12px 14px 10px; 
    background: var(--dk-surface); 
    border-top: 1px solid var(--dk-b2); 
    flex-shrink: 0; 
}

/* ── Chat Input Form ── */
.chat-input-form {
    display: flex; 
    align-items: center; 
    gap: 8px;
    background: var(--dk-surface2); 
    border: 1.5px solid var(--dk-b2);
    border-radius: 14px; 
    padding: 6px 6px 6px 14px;
    transition: border-color .15s, box-shadow .15s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.chat-input-form:focus-within { 
    border-color: rgba(96,165,250,0.5); 
    box-shadow: 0 0 0 3px rgba(96,165,250,0.1); 
    background: var(--dk-surface2);
}
.chat-input { 
    flex: 1; 
    border: none; 
    background: transparent; 
    font-size: 13px; 
    color: var(--dk-t1); 
    outline: none; 
    line-height: 1.4; 
    font-family: var(--font-body);
}
.chat-input::placeholder { 
    color: var(--dk-t4); 
}

/* ── Send Button ── */
.chat-send-btn {
    width: 34px; 
    height: 34px; 
    border-radius: 10px;
    background: linear-gradient(135deg, #1e40af, #2563eb);
    border: none; 
    display: flex; 
    align-items: center; 
    justify-content: center;
    cursor: pointer; 
    color: white; 
    flex-shrink: 0;
    transition: all .15s; 
    box-shadow: 0 2px 8px rgba(37,99,235,0.3);
}
.chat-send-btn:hover { 
    transform: scale(1.06); 
    background: linear-gradient(135deg, #2563eb, #1e40af);
    box-shadow: 0 4px 12px rgba(37,99,235,0.4);
}
.chat-send-btn:active { 
    transform: scale(.94);  
}
.chat-send-btn:disabled { 
    background: var(--dk-surface2); 
    color: var(--dk-t4); 
    cursor: not-allowed; 
    box-shadow: none; 
    border: 1px solid var(--dk-b2);
}

/* ── Footer Note ── */
.chat-footer-note { 
    text-align: center; 
    font-size: 10.5px; 
    color: var(--dk-t4); 
    margin-top: 6px; 
    letter-spacing: .2px; 
}

/* ── Chat Overlay ── */
.chat-overlay { 
    display: none; 
    position: fixed; 
    inset: 0; 
    background: rgba(0,0,0,0.6); 
    backdrop-filter: blur(2px); 
    z-index: 99; 
}
.chat-overlay.active { 
    display: block; 
}

/* ── Responsive ── */
@media (max-width: 900px) { 
    .chat-panel { 
        width: 100%; 
        right: -100%; 
    } 
}
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

        @php
            $activeYearRecord = \Illuminate\Support\Facades\DB::table('academic_years')->where('is_active', 1)->first();
            $activeAcademicYear = $activeYearRecord ? $activeYearRecord->year_label : 'No Active Year';
            $isActive = $activeYearRecord ? true : false;
        @endphp

        @if(auth()->user()->role->name === 'Admin')
            <a href="{{ route('academic-years.index') }}" class="sidebar-account-card a_is-link" title="Manage Academic Years" style="border: 1px solid {{ $isActive ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }}; background: linear-gradient(145deg, rgba(255,255,255,0.02), {{ $isActive ? 'rgba(16,185,129,0.04)' : 'rgba(239,68,68,0.04)' }});">
                <div class="account-icon" style="background: {{ $isActive ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }};">
                    @if($isActive)
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#10b981" stroke-width="2"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke="#10b981" stroke-width="2"/>
                            <line x1="8" y1="2" x2="8" y2="6" stroke="#10b981" stroke-width="2"/>
                            <line x1="3" y1="10" x2="21" y2="10" stroke="#10b981" stroke-width="2"/>
                        </svg>
                    @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#ef4444" stroke-width="2"/>
                            <line x1="12" y1="8" x2="12" y2="12" stroke="#ef4444" stroke-width="2"/>
                            <line x1="12" y1="16" x2="12.01" y2="16" stroke="#ef4444" stroke-width="2"/>
                        </svg>
                    @endif
                </div>
                <div class="account-info">
                    <span class="account-label" style="display:flex; align-items:center; gap:6px; {{ !$isActive ? 'color:#ef4444;' : '' }}">
                        {{ $isActive ? 'CURRENT TERM' : 'ATTENTION' }}
                        @if($isActive)<span class="pulse-dot"></span>@endif
                    </span>
                    <span class="account-value" style="color: {{ $isActive ? '#10b981' : '#fca5a5' }}; font-weight: {{ $isActive ? '700' : '600' }}; font-size: 12.5px;">
                        {{ $isActive ? $activeAcademicYear : 'No Active Year' }}
                    </span>
                </div>
            </a>
        @else
            <div class="sidebar-account-card" style="border: 1px solid {{ $isActive ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }}; background: linear-gradient(145deg, rgba(255,255,255,0.02), {{ $isActive ? 'rgba(16,185,129,0.04)' : 'rgba(239,68,68,0.04)' }});">
                <div class="account-icon" style="background: {{ $isActive ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }};">
                    @if($isActive)
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#10b981" stroke-width="2"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke="#10b981" stroke-width="2"/>
                            <line x1="8" y1="2" x2="8" y2="6" stroke="#10b981" stroke-width="2"/>
                            <line x1="3" y1="10" x2="21" y2="10" stroke="#10b981" stroke-width="2"/>
                        </svg>
                    @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#ef4444" stroke-width="2"/>
                            <line x1="12" y1="8" x2="12" y2="12" stroke="#ef4444" stroke-width="2"/>
                            <line x1="12" y1="16" x2="12.01" y2="16" stroke="#ef4444" stroke-width="2"/>
                        </svg>
                    @endif
                </div>
                <div class="account-info">
                    <span class="account-label" style="display:flex; align-items:center; gap:6px; {{ !$isActive ? 'color:#ef4444;' : '' }}">
                        {{ $isActive ? 'CURRENT TERM' : 'SYSTEM ALERT' }}
                        @if($isActive)<span class="pulse-dot"></span>@endif
                    </span>
                    <span class="account-value" style="color: {{ $isActive ? '#10b981' : '#fca5a5' }}; font-weight: {{ $isActive ? '700' : '600' }}; font-size: 12.5px;">
                        {{ $isActive ? $activeAcademicYear : 'No Active Academic Year' }}
                    </span>
                </div>
            </div>
        @endif

        <nav class="sidebar-nav">

            @if(auth()->user()->role->name === 'Admin')
            {{-- ══ ADMIN NAV ════════════════════════════════════════════════ --}}
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

            <a href="{{ route('admin.announcements') }}" class="nav-item {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Announcements
            </a>

            <a href="{{ route('admin.calendar') }}" class="nav-item {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M8 z`14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Calendar
            </a>

            <a href="{{ route('admin.policies') }}" class="nav-item {{ request()->routeIs('admin.policies*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
                Policies
            </a>

            {{-- ── Management Group ─────────────────────────────────────── --}}
            @php
                $mgmtRoutes = ['students.*', 'admin.sections*', 'admin.schedule*', 'admin.grades', 'admin.subjects*'];
                $mgmtActive = collect($mgmtRoutes)->contains(fn($r) => request()->routeIs($r));
            @endphp

            <span class="nav-section-label">MANAGEMENT</span>

            <div class="nav-group" id="navGroupMgmt">

                {{-- Trigger button --}}
                <button class="nav-group-trigger {{ $mgmtActive ? 'active-group' : '' }}"
                        aria-expanded="{{ $mgmtActive ? 'true' : 'false' }}"
                        aria-controls="navGroupMgmtChildren"
                        onclick="toggleNavGroup('navGroupMgmt')">
                    <svg class="trigger-icon" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <rect x="2" y="3" width="20" height="5" rx="1" stroke="currentColor" stroke-width="1.8"/>
                        <rect x="2" y="10" width="20" height="5" rx="1" stroke="currentColor" stroke-width="1.8"/>
                        <rect x="2" y="17" width="20" height="5" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                    <span class="trigger-label">Manage Records</span>
                    <svg class="nav-group-chevron" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                {{-- Children --}}
                <div class="nav-group-children {{ $mgmtActive ? 'open' : '' }}" id="navGroupMgmtChildren">

                    <a href="{{ route('students.index') }}"
                       class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M21 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        User Management
                    </a>

                    <a href="{{ route('admin.sections') }}"
                       class="nav-item {{ request()->routeIs('admin.sections*') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Sections
                    </a>

                    <a href="{{ route('admin.schedule') }}"
                       class="nav-item {{ request()->routeIs('admin.schedule*') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Schedule
                    </a>

                    <a href="{{ route('admin.grades') }}"
                       class="nav-item {{ request()->routeIs('admin.grades') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M9 11l3 3L22 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Grades
                    </a>

                    <a href="{{ route('admin.subjects') }}"
                       class="nav-item {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M9 7h6M9 11h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Subjects
                    </a>

                </div>
            </div>
            {{-- ── End Management Group ─────────────────────────────────── --}}

           @elseif(auth()->user()->role->name === 'Teacher')
                {{-- ══ TEACHER NAV ══════════════════════════════════════════════ --}}
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

                <a href="{{ route('teacher.schedule') }}" class="nav-item {{ request()->routeIs('teacher.schedule') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    My Schedule
                </a>

                <a href="{{ route('announcements.index') }}" class="nav-item {{ request()->routeIs('announcements.index') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Announcements
                </a>

                <a href="{{ route('teacher.class-list') }}" class="nav-item {{ request()->routeIs('teacher.class-list*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <circle cx="9" cy="7" r="3" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M3 20c0-3.314 2.686-6 6-6s6 2.686 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M16 11c1.657 0 3-1.343 3-3s-1.343-3-3-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M21 20c0-3.314-2.239-6-5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Class List
                </a>

                <a href="{{ route('grades.index') }}" class="nav-item {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 11l3 3L22 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Grades
                </a>

                <a href="{{ route('teacher.calendar') }}" class="nav-item {{ request()->routeIs('teacher.calendar') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Calendar
                </a>

                <a href="{{ route('teacher.policies') }}" class="nav-item {{ request()->routeIs('teacher.policies') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                    Policies
                </a>

            @elseif(auth()->user()->role->name === 'Student')
            {{-- ══ STUDENT NAV ══════════════════════════════════════════════ --}}
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

            <a href="{{ route('announcements.index') }}" class="nav-item {{ request()->routeIs('announcements.index') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Announcements
            </a>

            <a href="{{ route('student.schedule') }}" class="nav-item {{ request()->routeIs('student.schedule') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                My Schedule
            </a>

            <a href="{{ route('grades.index') }}" class="nav-item {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M9 11l3 3L22 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                My Grades
            </a>

            <a href="{{ route('student.calendar') }}" class="nav-item {{ request()->routeIs('student.calendar') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Calendar
            </a>

            <a href="{{ route('student.policies') }}" class="nav-item {{ request()->routeIs('student.policies') ? 'active' : '' }}">
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
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
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
            <a href="{{ route('profile.index') }}" class="avatar-wrap" title="My Profile" style="text-decoration:none;">
                <div class="avatar" style="cursor:pointer; transition:box-shadow .2s;"
                    onmouseover="this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.4)'"
                    onmouseout="this.style.boxShadow=''">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' '), 1, 1)) }}
                </div>
            </a>
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

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

@stack('scripts')

<script>
// ── Nav group accordion ───────────────────────────────────────────────────────
function toggleNavGroup(groupId) {
    const group    = document.getElementById(groupId);
    if (!group) return;
    const trigger  = group.querySelector('.nav-group-trigger');
    const children = group.querySelector('.nav-group-children');
    const isOpen   = children.classList.contains('open');

    if (isOpen) {
        children.classList.remove('open');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.classList.remove('active-group');
    } else {
        children.classList.add('open');
        trigger.setAttribute('aria-expanded', 'true');
    }
}

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

// ── Chat persistence ──────────────────────────────────────────────────────────
function saveChatHistory() {
    const chatBody = document.getElementById('chatBody');
    if (chatBody) sessionStorage.setItem('sis_chat_html', chatBody.innerHTML);
}
function restoreChatHistory() {
    const chatBody = document.getElementById('chatBody');
    if (!chatBody) return;
    const saved = sessionStorage.getItem('sis_chat_html');
    if (saved) { chatBody.innerHTML = saved; chatBody.scrollTop = chatBody.scrollHeight; }
}
function clearChatHistory() {
    sessionStorage.removeItem('sis_chat_html');
    const chatBody = document.getElementById('chatBody');
    if (chatBody) { chatBody.innerHTML = defaultChatHTML; chatBody.scrollTop = 0; }
}

// ── DOM Ready ─────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const loadingModal = document.getElementById('loading-modal');

    // Define loading modal functions globally
    if (loadingModal) {
        window.showLoading = () => { loadingModal.style.display = 'flex'; };
        window.hideLoading = () => { loadingModal.style.display = 'none'; };
    }

    const navType = performance.getEntriesByType('navigation')[0]?.type;
    if (navType === 'reload') {
        sessionStorage.removeItem('sis_chat_html');
    } else {
        restoreChatHistory();
    }

    const logoutButton = document.getElementById('sidebar-logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', function (e) {
            e.preventDefault();
            showConfirmationModal('Logout Confirmation', 'Are you sure you want to log out?', function () {
                clearChatHistory();
                if (window.showLoading) window.showLoading();
                document.getElementById('logout-form').submit();
            });
        });
    }

    document.querySelectorAll('.sidebar-nav .nav-item').forEach(function (link) {
        link.addEventListener('click', function () {
            if (link.target !== '_blank' && window.showLoading) window.showLoading();
        });
    });

    window.addEventListener('pageshow', function () {
        if (window.hideLoading) window.hideLoading();
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
    newConfirmBtn.addEventListener('click', function () { onConfirm(); modal.style.display = 'none'; });
    cancelBtn.addEventListener('click', function () { modal.style.display = 'none'; });
    modal.style.display = 'flex';
}

// ── Chat Panel ────────────────────────────────────────────────────────────────
function toggleChatPanel() {
    const panel   = document.getElementById('chatPanel');
    const overlay = document.getElementById('chatOverlay');
    if (!panel || !overlay) return;
    const isOpen = panel.classList.contains('open');
    panel.classList.toggle('open', !isOpen);
    overlay.classList.toggle('active', !isOpen);
    if (!isOpen) setTimeout(() => { const i = document.getElementById('chatInput'); if (i) i.focus(); }, 300);
}
function handleChatSubmit(event) {
    event.preventDefault();
    const input = document.getElementById('chatInput');
    const msg   = input.value.trim();
    if (msg) { sendMessage(msg); input.value = ''; }
}
function sendMessage(message) {
    const chatBody = document.getElementById('chatBody');
    if (!chatBody) return;
    document.querySelectorAll('.chat-suggestions').forEach(el => el.style.display = 'none');
    appendMessage('user', message);
    const typing = createTypingIndicator();
    chatBody.appendChild(typing);
    chatBody.scrollTop = chatBody.scrollHeight;
    $.ajax({
        url: '{{ route("chat.send") }}', method: 'POST',
        data: { message, _token: '{{ csrf_token() }}' },
        success: function (r) { typing.remove(); appendMessage('assistant', r.reply); },
        error:   function ()  { typing.remove(); appendMessage('assistant', 'Sorry, something went wrong. Please try again.'); }
    });
}
function appendMessage(role, text) {
    const chatBody = document.getElementById('chatBody');
    if (!chatBody) return;
    const msg    = document.createElement('div');
    msg.className = 'chat-message ' + role;
    const avatar = document.createElement('div');
    avatar.className = 'chat-avatar';
    if (role === 'assistant') {
        avatar.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>`;
    } else {
        avatar.textContent = '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}';
    }
    const bubble   = document.createElement('div');  bubble.className = 'msg-bubble';
    const textDiv  = document.createElement('div');  textDiv.className = 'chat-text';
    if (role === 'assistant') { textDiv.innerHTML = text.replace(/\n/g, '<br>'); } else { textDiv.textContent = text; }
    bubble.appendChild(textDiv);
    msg.appendChild(avatar);
    msg.appendChild(bubble);
    chatBody.appendChild(msg);
    chatBody.scrollTop = chatBody.scrollHeight;
    saveChatHistory();
}
function createTypingIndicator() {
    const t = document.createElement('div');
    t.className = 'chat-message assistant';
    t.innerHTML = `<div class="chat-avatar"><svg width="13" height="13" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/></svg></div><div class="msg-bubble"><div class="chat-text"><div class="chat-typing"><span></span><span></span><span></span></div></div></div>`;
    return t;
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const p = document.getElementById('chatPanel');
        if (p && p.classList.contains('open')) toggleChatPanel();
    }
});
</script>

</body>
</html>