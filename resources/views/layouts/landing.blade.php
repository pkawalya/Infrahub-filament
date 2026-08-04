<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'InfraHub — All-in-One Construction Management Platform' }}</title>
    <meta name="description" content="{{ $description ?? 'InfraHub connects people, processes, and project data across the entire project lifecycle—from planning to handover and beyond.' }}">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo/infrahub-icon.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,800&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --orange-500: #f97316;
            --orange-600: #ea580c;
            --orange-700: #c2410c;
            --orange-50: #fff7ed;
            --orange-100: #ffedd5;

            --navy-950: #040814;
            --navy-900: #0a1128;
            --navy-800: #101c3d;
            --navy-700: #152d4a;
            --navy-600: #1e3a5f;

            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-300: #cbd5e1;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
        }

        [data-theme="light"] {
            --bg-body: #ffffff;
            --bg-nav: rgba(255, 255, 255, 0.92);
            --bg-hero: #ffffff;
            --bg-card: #ffffff;
            --bg-card-hover: #f8fafc;
            --bg-subtle: #f8fafc;
            --border-subtle: #e2e8f0;
            --border-hover: #cbd5e1;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-faint: #94a3b8;
            --nav-link: #334155;
            --nav-link-hover: #0f172a;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 6px 16px -2px rgba(0, 0, 0, 0.08);
            --shadow-xl: 0 24px 48px -12px rgba(0, 0, 0, 0.12);
            --dropdown-bg: #ffffff;
            --dropdown-border: #e2e8f0;
        }

        [data-theme="dark"] {
            --bg-body: #040814;
            --bg-nav: rgba(10, 17, 40, 0.92);
            --bg-hero: #040814;
            --bg-card: #0a1128;
            --bg-card-hover: #101c3d;
            --bg-subtle: #0a1128;
            --border-subtle: #1e293b;
            --border-hover: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-faint: #64748b;
            --nav-link: #cbd5e1;
            --nav-link-hover: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.6);
            --shadow-md: 0 6px 16px -2px rgba(0, 0, 0, 0.6);
            --shadow-xl: 0 24px 48px -12px rgba(0, 0, 0, 0.8);
            --dropdown-bg: #0a1128;
            --dropdown-border: #1e293b;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        /* ─── Header & Nav ─── */
        header.landing-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: var(--bg-nav);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            transition: all 0.3s;
            width: 100%;
        }

        .nav-container {
            width: 100%;
            padding: 0 40px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 768px) {
            .nav-container {
                padding: 0 20px;
            }
        }

        nav.landing-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
            width: 100%;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: transform 0.2s;
        }

        .brand-logo:hover {
            transform: scale(1.02);
        }

        .brand-logo img {
            height: 40px;
            object-fit: contain;
        }

        .nav-center {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .nav-item {
            position: relative;
            color: var(--nav-link);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            padding: 8px 0;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .nav-item:hover,
        .nav-item.active {
            color: var(--orange-500);
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2.5px;
            background: linear-gradient(90deg, var(--orange-500), var(--orange-600));
            border-radius: 2px;
        }

        /* ── Dropdowns ── */
        .nav-dropdown-wrapper {
            position: relative;
        }

        .nav-dropdown-wrapper:hover .nav-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .nav-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 280px;
            background: var(--dropdown-bg);
            border: 1px solid var(--dropdown-border);
            border-radius: 16px;
            padding: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1050;
        }

        .nav-dropdown-menu.mega-menu {
            min-width: 380px;
        }

        .dropdown-header {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--orange-500);
            padding: 8px 12px 6px;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-main);
            transition: background 0.2s, transform 0.2s;
        }

        .dropdown-item:hover {
            background: var(--bg-card-hover);
            transform: translateX(3px);
        }

        .dropdown-item .item-icon {
            font-size: 18px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(249, 115, 22, 0.1);
            border-radius: 8px;
            flex-shrink: 0;
        }

        .dropdown-item .item-text strong {
            display: block;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-main);
        }

        .dropdown-item .item-text small {
            display: block;
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
            line-height: 1.3;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .lang-select {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-login {
            color: var(--text-main);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-login:hover {
            color: var(--orange-500);
            background: rgba(249, 115, 22, 0.06);
        }

        .btn-demo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(249, 115, 22, 0.3);
            border: none;
            cursor: pointer;
        }

        .btn-demo:hover {
            background: linear-gradient(135deg, var(--orange-600), var(--orange-700));
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(249, 115, 22, 0.45);
        }

        .theme-toggle-btn {
            background: var(--slate-100);
            border: 1px solid var(--slate-200);
            cursor: pointer;
            font-size: 16px;
            padding: 8px 10px;
            border-radius: 10px;
            color: var(--text-muted);
            transition: all 0.2s;
        }

        [data-theme="dark"] .theme-toggle-btn {
            background: var(--navy-800);
            border-color: var(--slate-700);
            color: var(--slate-300);
        }

        .theme-toggle-btn:hover {
            color: var(--orange-500);
            transform: rotate(15deg);
        }

        /* ── Main Hero Page Section ── */
        .page-hero {
            padding: 70px 0 50px;
            text-align: center;
            background: var(--bg-hero);
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 700;
            color: var(--orange-500);
            margin-bottom: 18px;
            box-shadow: var(--shadow-sm);
        }

        .page-hero h1 {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: -1.5px;
            line-height: 1.15;
            margin-bottom: 16px;
        }

        .page-hero p {
            font-size: 18px;
            color: var(--text-muted);
            max-width: 720px;
            margin: 0 auto 30px;
            line-height: 1.6;
        }

        /* ── Grid Cards ── */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 28px;
            margin-top: 40px;
        }

        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            padding: 32px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .feature-card:hover {
            border-color: var(--orange-500);
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .card-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(249, 115, 22, 0.12);
            color: var(--orange-500);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-main);
        }

        .feature-card p {
            font-size: 14.5px;
            color: var(--text-muted);
            line-height: 1.65;
            margin-bottom: 16px;
        }

        .feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--border-subtle);
        }

        .feature-list li {
            font-size: 13.5px;
            color: var(--text-main);
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .feature-list li::before {
            content: '✓';
            color: var(--orange-500);
            font-weight: 800;
        }

        /* ── Footer ── */
        footer {
            background: #040814;
            color: #94a3b8;
            padding: 64px 0 36px;
            border-top: 1px solid #1e293b;
            margin-top: 80px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-brand p {
            font-size: 14px;
            color: #64748b;
            margin-top: 16px;
            line-height: 1.65;
            max-width: 320px;
        }

        .footer-col h4 {
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .footer-col a {
            display: block;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            padding: 7px 0;
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: var(--orange-500);
        }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 30px;
            border-top: 1px solid #1e293b;
            font-size: 13px;
            color: #64748b;
        }

        @media (max-width: 960px) {
            .nav-center {
                display: none;
            }
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
            .page-hero h1 {
                font-size: 36px;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <x-landing-nav :activePage="$activePage ?? 'home'" />

    <main>
        {{ $slot }}
    </main>

    <x-landing-footer />

    <script>
        function applyLogoForTheme(theme) {
            const logoImg = document.getElementById('header-brand-logo');
            if (logoImg) {
                logoImg.src = theme === 'dark' ? "{{ asset('logo/infrahub-logo-dark.png') }}" : "{{ asset('logo/infrahub-logo-new.png') }}";
            }
        }

        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('infrahub-theme', next);
            
            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.textContent = next === 'dark' ? '☀️' : '🌙';
            }
            applyLogoForTheme(next);
        }

        (function () {
            const saved = localStorage.getItem('infrahub-theme');
            if (saved) {
                document.documentElement.setAttribute('data-theme', saved);
                const icon = document.getElementById('theme-icon');
                if (icon) icon.textContent = saved === 'dark' ? '☀️' : '🌙';
                applyLogoForTheme(saved);
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
