<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#020617">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/images/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
    <title>@yield('title', 'InfraHub')</title>
    <link rel="preload" href="/css/mobile.css?v={{ filemtime(public_path('css/mobile.css')) }}" as="style">
    <link rel="stylesheet" href="/css/mobile.css?v={{ filemtime(public_path('css/mobile.css')) }}">
    @stack('head')
</head>

<body>
    {{-- Offline Banner --}}
    <div class="m-offline-bar" id="offline-bar">
        <span class="m-offline-dot"></span>
        You're offline — data is saved locally
    </div>

    {{-- Header --}}
    <header class="m-header">
        <a href="/mobile" class="m-header-brand">
            <span class="logo">IH</span>
            InfraHub
        </a>
        <div class="m-header-actions">
            <a href="/mobile/notifications" class="m-header-icon" id="notif-bell">
                <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <span class="m-badge" id="notif-count" style="display:none">0</span>
            </a>
            <a href="/mobile/profile" class="m-header-icon">
                <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </a>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="m-content">
        @yield('content')
    </main>

    {{-- Bottom Navigation --}}
    <nav class="m-nav">
        <a href="/mobile" class="m-nav-item {{ ($active ?? '') === 'home' ? 'active' : '' }}">
            <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Home
        </a>
        <a href="/mobile/projects" class="m-nav-item {{ ($active ?? '') === 'projects' ? 'active' : '' }}">
            <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0V21" />
            </svg>
            Projects
        </a>
        <a href="/mobile/tasks" class="m-nav-item {{ ($active ?? '') === 'tasks' ? 'active' : '' }}">
            <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Tasks
        </a>
        <a href="/mobile/forms" class="m-nav-item {{ ($active ?? '') === 'forms' ? 'active' : '' }}">
            <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Forms
        </a>
        <a href="/mobile/profile" class="m-nav-item {{ ($active ?? '') === 'profile' ? 'active' : '' }}">
            <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Profile
        </a>
    </nav>

    {{-- Toast Container --}}
    <div id="m-toast" class="m-toast"></div>

    {{-- Core JS --}}
    <script>
        // ── API Client ───────────────────────────────────────
        const API = {
            token: localStorage.getItem('m_token'),
            base: '/api/v1',

            headers() {
                const h = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
                if (this.token) h['Authorization'] = `Bearer ${this.token}`;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                if (csrf) h['X-CSRF-TOKEN'] = csrf;
                return h;
            },

            async get(path) {
                const resp = await fetch(this.base + path, { headers: this.headers(), credentials: 'same-origin' });
                if (resp.status === 401) { this.logout(); return null; }
                return resp.json();
            },

            async post(path, data = {}) {
                const resp = await fetch(this.base + path, {
                    method: 'POST', headers: this.headers(), credentials: 'same-origin',
                    body: JSON.stringify(data),
                });
                if (resp.status === 401) { this.logout(); return null; }
                return resp.json();
            },

            async put(path, data = {}) {
                const resp = await fetch(this.base + path, {
                    method: 'PUT', headers: this.headers(), credentials: 'same-origin',
                    body: JSON.stringify(data),
                });
                return resp.json();
            },

            async login(email, password) {
                const resp = await fetch(this.base + '/auth/login', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password, device_name: 'mobile-pwa' }),
                });
                return resp.json();
            },

            logout() {
                localStorage.removeItem('m_token');
                localStorage.removeItem('m_user');
                window.location.href = '/mobile/login';
            },

            isLoggedIn() {
                return !!this.token;
            },

            setAuth(token, user) {
                this.token = token;
                localStorage.setItem('m_token', token);
                localStorage.setItem('m_user', JSON.stringify(user));
            },

            getUser() {
                try { return JSON.parse(localStorage.getItem('m_user')); } catch { return null; }
            }
        };

        // ── Toast ────────────────────────────────────────────
        function toast(msg, type = 'success', duration = 3000) {
            const el = document.getElementById('m-toast');
            el.textContent = msg;
            el.className = `m-toast ${type} show`;
            setTimeout(() => { el.classList.remove('show'); }, duration);
        }

        // ── Offline Detection ────────────────────────────────
        function updateOnlineStatus() {
            const bar = document.getElementById('offline-bar');
            if (bar) bar.classList.toggle('visible', !navigator.onLine);
        }
        window.addEventListener('online', () => { updateOnlineStatus(); toast('Back online ✓', 'success'); });
        window.addEventListener('offline', () => { updateOnlineStatus(); toast('You are offline', 'error', 5000); });
        updateOnlineStatus();

        // ── Service Worker ───────────────────────────────────
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => { });
        }
    </script>
    @stack('scripts')
</body>

</html>