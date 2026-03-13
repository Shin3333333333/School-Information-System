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
                {{-- Chat Toggle Button --}}
                <button class="icon-btn chat-toggle-btn" onclick="toggleChatPanel()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9" cy="10" r="1" fill="currentColor"/>
                        <circle cx="12" cy="10" r="1" fill="currentColor"/>
                        <circle cx="15" cy="10" r="1" fill="currentColor"/>
                    </svg>
                </button>
                <div class="avatar-wrap dropdown dropdown-end"><div tabindex="0" role="button" class="avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' '), 1, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Body --}}
        <div class="page-body">
            @yield('content')
        </div>

    </main>

    {{-- ── Chat Panel ── --}}
    <aside class="chat-panel" id="chatPanel">
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="chat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <div class="chat-title">SIS Support</div>
                </div>
            </div>
            <button class="chat-close-btn" onclick="toggleChatPanel()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="chat-body" id="chatBody">
            <div class="chat-message assistant">
                <div class="chat-avatar">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="chat-bubble">
                    <div class="chat-text">Hi! I'm your AI assistant. How can I help you?</div>
                    <div class="chat-suggestions">
                        <button class="suggestion-chip" onclick="sendMessage('How many students enrolled this year?')">How many students enrolled this year?</button>
                        <button class="suggestion-chip" onclick="sendMessage('Show students with pending fees')">Show students with pending fees</button>
                        <button class="suggestion-chip" onclick="sendMessage('What is our collection rate?')">What is our collection rate?</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="chat-footer">
            <form class="chat-input-form" onsubmit="handleChatSubmit(event)">
                <input type="text" class="chat-input" id="chatInput" placeholder="Ask about students, fees, grades..." autocomplete="off">
                <button type="submit" class="chat-send-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
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
            if (link.target === '_blank') return;
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

// --- Chat Panel ---
function toggleChatPanel() {
    const panel = document.getElementById('chatPanel');
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
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if (message) {
        sendMessage(message);
        input.value = '';
    }
}

function sendMessage(message) {
    const chatBody = document.getElementById('chatBody');
    if (!chatBody) return;

    appendMessage('user', message);

    const typingIndicator = createTypingIndicator();
    chatBody.appendChild(typingIndicator);
    chatBody.scrollTop = chatBody.scrollHeight;

    setTimeout(() => {
        typingIndicator.remove();
        const response = generateAIResponse(message);
        appendMessage('assistant', response);
    }, 1500);
}

function appendMessage(role, text) {
    const chatBody = document.getElementById('chatBody');
    if (!chatBody) return;

    const messageDiv = document.createElement('div');
    messageDiv.className = 'chat-message ' + role;

    const avatar = document.createElement('div');
    avatar.className = 'chat-avatar';

    if (role === 'assistant') {
        avatar.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    } else {
        avatar.textContent = 'U';
    }

    const bubble = document.createElement('div');
    bubble.className = 'chat-bubble';

    const textDiv = document.createElement('div');
    textDiv.className = 'chat-text';
    textDiv.textContent = text;

    bubble.appendChild(textDiv);
    messageDiv.appendChild(avatar);
    messageDiv.appendChild(bubble);

    chatBody.appendChild(messageDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
}

function createTypingIndicator() {
    const typing = document.createElement('div');
    typing.className = 'chat-message assistant';
    typing.innerHTML = '<div class="chat-avatar"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/></svg></div><div class="chat-bubble"><div class="chat-text"><div class="chat-typing"><span></span><span></span><span></span></div></div></div>';
    return typing;
}

function generateAIResponse(query) {
    const lowerQuery = query.toLowerCase();

    if (lowerQuery.includes('how many') && lowerQuery.includes('student')) {
        return "Based on current enrollment data, we have 1,284 total students enrolled for academic year 2024-2025. This includes 1,198 active students across grades 7-12, with 24 new enrollments this month.";
    }
    if (lowerQuery.includes('pending') && lowerQuery.includes('fee')) {
        return "There are currently 86 students with pending fee payments. The total outstanding amount is ₱680,000. Most of these are due within the next 7 days.";
    }
    if (lowerQuery.includes('collection rate')) {
        return "Our current collection rate is 78% of total assessed fees. We've collected ₱2.41M out of ₱3.09M total.";
    }
    return "I understand you're asking about: \"" + query + "\". I can help you analyze student records, fee payments, enrollment stats, and more. Could you rephrase your question?";
}

// ESC key closes chat
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const panel = document.getElementById('chatPanel');
        if (panel && panel.classList.contains('open')) toggleChatPanel();
    }
});
</script>

</body>
</html>