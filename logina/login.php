<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — School Information System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-body">

<div class="login-shell">

    <div class="login-panel">
        <div class="login-card">

            <div style="margin-bottom:28px;">
                <h2 class="login-card-title">Welcome back</h2>
                <p class="login-card-sub">Sign in to continue</p>
            </div>

            @if ($errors->any())
            <div class="login-error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:18px;">
                @csrf

                <div class="login-field">
                    <label class="login-label">ID Number</label>
                    <div class="login-input-wrap">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="login-input-icon">
                            <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" stroke="currentColor" stroke-width="2"/>
                            <line x1="12" y1="12" x2="12" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <line x1="10" y1="14" x2="14" y2="14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <input
                            type="text"
                            name="id_number"
                            class="login-input"
                            placeholder="Enter your ID number"
                            value="{{ old('id_number') }}"
                            required
                            autocomplete="username"
                        >
                    </div>
                </div>

                <div class="login-field">
                    <label class="login-label">Password</label>
                    <div class="login-input-wrap">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="login-input-icon">
                            <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <input
                            type="password"
                            name="password"
                            id="password-input"
                            class="login-input"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="login-eye-btn" onclick="togglePassword()">
                            <svg id="eye-icon" width="15" height="15" viewBox="0 0 24 24" fill="none">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="login-field">
                    <label class="login-label">Sign in as</label>
                    <div class="login-role-group">
                        <label class="login-role-btn {{ old('role') == 'student' ? 'selected' : '' }}">
                            <input type="radio" name="role" value="student" {{ old('role') == 'student' ? 'checked' : '' }} required>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                                <path d="M4 20v-2a8 8 0 0 1 16 0v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Student
                        </label>
                        <label class="login-role-btn {{ old('role') == 'teacher' ? 'selected' : '' }}">
                            <input type="radio" name="role" value="teacher" {{ old('role') == 'teacher' ? 'checked' : '' }}>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Teacher
                        </label>
                        <label class="login-role-btn {{ old('role') == 'admin' ? 'selected' : '' }}">
                            <input type="radio" name="role" value="admin" {{ old('role') == 'admin' ? 'checked' : '' }}>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Admin
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;font-size:14px;margin-top:4px;">
                    Sign In
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

            <p class="login-hint">Demo :D ID <strong>112903</strong> / Password <strong>112903</strong></p>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password-input');
    input.type = input.type === 'password' ? 'text' : 'password';
}
document.querySelectorAll('.login-role-btn input').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.login-role-btn').forEach(b => b.classList.remove('selected'));
        radio.closest('.login-role-btn').classList.add('selected');
    });
});
</script>
</body>
</html>