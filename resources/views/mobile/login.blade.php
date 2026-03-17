<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#020617">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <title>Login — InfraHub</title>
    <link rel="stylesheet" href="/css/mobile.css?v={{ filemtime(public_path('css/mobile.css')) }}">
</head>

<body>
    <div class="m-login-container">
        <div class="m-login-card">
            <div class="m-login-brand">
                <div class="logo">IH</div>
                <h1>InfraHub</h1>
                <p>Sign in to your mobile workspace</p>
            </div>

            <div id="login-error" class="m-login-error" style="display:none"></div>

            <form id="login-form" onsubmit="return doLogin(event)">
                <div class="m-form-group">
                    <label class="m-label" for="email">Email</label>
                    <input type="email" id="email" class="m-input" placeholder="you@company.com" required autofocus
                        autocomplete="email">
                </div>
                <div class="m-form-group">
                    <label class="m-label" for="password">Password</label>
                    <input type="password" id="password" class="m-input" placeholder="••••••••" required
                        autocomplete="current-password">
                </div>
                <button type="submit" class="m-btn m-btn-primary" id="login-btn" style="margin-top:0.5rem">
                    <span id="login-text">Sign In</span>
                </button>
            </form>

            <div style="text-align:center; margin-top:1.5rem;">
                <a href="/app/login" style="color:var(--text-dim); font-size:0.78rem; text-decoration:none;">
                    Use desktop version →
                </a>
            </div>
        </div>

        <div style="text-align:center; margin-top:2rem; font-size:0.7rem; color:var(--text-dim);">
            <p>Install InfraHub on your device for the best experience</p>
            <p style="margin-top:0.25rem;">📱 Add to Home Screen from your browser menu</p>
        </div>
    </div>

    <script>
        // If already logged in, redirect
        if (localStorage.getItem('m_token')) {
            window.location.href = '/mobile';
        }

        async function doLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('login-btn');
            const text = document.getElementById('login-text');
            const errDiv = document.getElementById('login-error');
            errDiv.style.display = 'none';

            btn.disabled = true;
            text.textContent = 'Signing in...';

            try {
                const resp = await fetch('/api/v1/auth/login', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                        device_name: 'mobile-pwa',
                    }),
                });

                const data = await resp.json();

                if (resp.ok && data.success && data.data?.token) {
                    localStorage.setItem('m_token', data.data.token);
                    localStorage.setItem('m_user', JSON.stringify(data.data.user));
                    window.location.href = '/mobile';
                } else {
                    errDiv.textContent = data.message || 'Invalid email or password';
                    errDiv.style.display = 'block';
                }
            } catch (err) {
                errDiv.textContent = 'Connection failed. Check your internet.';
                errDiv.style.display = 'block';
            }

            btn.disabled = false;
            text.textContent = 'Sign In';
            return false;
        }
    </script>
</body>

</html>