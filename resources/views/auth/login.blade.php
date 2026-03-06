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
<body class="login-body">

<div class="login-shell">

    <div class="login-panel">
        <div class="login-card">

            <div class="login-card-header">
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

            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf

                <div class="login-field">
                    <label class="login-label">Email</label>
                    <div class="login-input-wrap">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="login-input-icon">
                            <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" stroke="currentColor" stroke-width="2"/>
                            <line x1="12" y1="12" x2="12" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <line x1="10" y1="14" x2="14" y2="14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <input
                            type="email"
                            name="email"
                            class="login-input"
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                        >
                    </div>
                </div>

                <div class="login-field">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <label class="login-label">Password</label>
                        <a href="#" class="login-forgot-link">Forgot?</a>
                    </div>
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

                <input type="hidden" name="role" value="admin">

                <button type="submit" class="btn btn-primary">
                    Sign In
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

            <p class="login-hint">Email: <strong>admin@example.com</strong> / Password: <strong>password</strong></p>
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
 $(document).ready(function() {
        $('.login-form').on('submit', function(e) {
            loadingModal.show();
            e.preventDefault(); // Prevent the default form submission

            $.ajax({
                url: '{{ route("ajax.login") }}', // The route that handles the login logic
                method: 'POST',
                data: $(this).serialize(), // Sends the form data (ID number and password)
                success: function(response) {
                    if (response.success) {
                        // If login is successful, redirect to the dashboard
                        window.location.href = '{{ route("dashboard") }}';
                    } else {
                        // If login fails, show an alert with the error message
                        alert(response.message);
                    }
                    loadingModal.hide();
                },
                error: function(xhr, status, error) {
                    loadingModal.hide();
                    // Handle AJAX errors, such as network issues or server errors
                    console.error("AJAX Error:", status, error);
                    alert('An error occurred during login. Please try again.');
                }
            });
        });
    });
</script>
</body>
</html>