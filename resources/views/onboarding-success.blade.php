<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Submitted — InfraHub</title>
    <meta name="description"
        content="Your InfraHub application has been submitted successfully. We'll review and activate your account shortly.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo/infrahub-icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy-700: #152d4a;
            --navy-600: #1e3a5f;
            --amber-300: #f5c563;
            --amber-400: #e8a229;
            --amber-500: #d4911e;
        }

        /* ─── Dark Theme (default) ─── */
        [data-theme="dark"] {
            --bg-body: #020617;
            --bg-card: rgba(255, 255, 255, 0.02);
            --bg-card-inner: rgba(255, 255, 255, 0.03);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-inner: rgba(255, 255, 255, 0.06);
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-item: #cbd5e1;
            --grid-line: rgba(232, 162, 41, 0.03);
            --glow-opacity: 0.12;
            --toggle-bg: rgba(255, 255, 255, 0.04);
            --toggle-border: rgba(255, 255, 255, 0.1);
        }

        /* ─── Light Theme ─── */
        [data-theme="light"] {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-card-inner: #f8fafc;
            --border-subtle: #e2e8f0;
            --border-inner: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-item: #334155;
            --grid-line: rgba(30, 58, 95, 0.04);
            --glow-opacity: 0.05;
            --toggle-bg: #f1f5f9;
            --toggle-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            transition: background 0.3s, color 0.3s;
            overflow-x: hidden;
        }

        /* ─── Background Effects ─── */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(var(--grid-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .bg-glow {
            position: fixed;
            z-index: 0;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: var(--glow-opacity);
            pointer-events: none;
        }

        .bg-glow-1 {
            top: -200px;
            right: -100px;
            background: var(--amber-400);
        }

        .bg-glow-2 {
            bottom: -200px;
            left: -100px;
            background: var(--navy-600);
        }

        /* ─── Theme Toggle ─── */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 24px;
            z-index: 10;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--toggle-bg);
            border: 1px solid var(--toggle-border);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 18px;
            color: var(--text-secondary);
        }

        .theme-toggle:hover {
            border-color: var(--amber-400);
            color: var(--amber-400);
            background: rgba(232, 162, 41, 0.08);
        }

        .theme-toggle .icon-sun {
            display: none;
        }

        .theme-toggle .icon-moon {
            display: block;
        }

        [data-theme="light"] .theme-toggle .icon-sun {
            display: block;
        }

        [data-theme="light"] .theme-toggle .icon-moon {
            display: none;
        }

        /* ─── Card ─── */
        .card {
            text-align: center;
            max-width: 520px;
            padding: 60px 40px;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 24px;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 40px rgba(0, 0, 0, 0.06);
            transition: background 0.3s, border-color 0.3s, box-shadow 0.3s;
        }

        .card::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 20%;
            right: 20%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--amber-400), transparent);
        }

        /* ─── Check Icon ─── */
        .icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 28px;
            background: rgba(16, 185, 129, 0.1);
            border: 2px solid rgba(16, 185, 129, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            animation: pop-in 0.5s ease;
        }

        @keyframes pop-in {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            60% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            color: var(--text-primary);
        }

        .card>p {
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 16px;
            font-size: 15px;
        }

        .highlight {
            color: var(--amber-400);
            font-weight: 600;
        }

        /* ─── Steps List ─── */
        .steps-list {
            text-align: left;
            list-style: none;
            margin: 28px 0;
            padding: 20px 24px;
            background: var(--bg-card-inner);
            border-radius: 14px;
            border: 1px solid var(--border-inner);
            transition: background 0.3s, border-color 0.3s;
        }

        .steps-list li {
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: var(--text-item);
        }

        .steps-list li::before {
            content: '✓';
            color: #10b981;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* ─── Button ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            background: linear-gradient(135deg, var(--amber-400), var(--amber-500));
            color: var(--navy-700);
            transition: all 0.3s;
            box-shadow: 0 4px 20px rgba(232, 162, 41, 0.25);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(232, 162, 41, 0.35);
        }

        /* ─── Logo Link ─── */
        .home-logo {
            position: fixed;
            top: 20px;
            left: 24px;
            z-index: 10;
        }

        .home-logo img {
            height: 36px;
            border-radius: 10px;
        }

        @media (max-width: 600px) {
            .card {
                margin: 20px;
                padding: 40px 24px;
            }
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <a href="/" class="home-logo">
        <img src="{{ asset('logo/infrahub-logo-new.png') }}" alt="InfraHub">
    </a>

    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle light/dark mode">
        <span class="icon-moon">🌙</span>
        <span class="icon-sun">☀️</span>
    </button>

    <div class="card">
        <div class="icon">✅</div>
        <h1>Application Submitted!</h1>
        <p>
            Thank you for choosing <span class="highlight">InfraHub</span>. Your company registration
            has been received and is now <span class="highlight">pending approval</span>.
        </p>
        <ul class="steps-list">
            <li>Your application is being reviewed by our team</li>
            <li>You'll receive an email once your account is approved</li>
            <li>After approval, log in with the credentials you created</li>
            <li>A 14-day free trial will begin on activation</li>
        </ul>
        <a href="/" class="btn">← Back to Home</a>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('infrahub-theme', next);
        }

        // Restore saved preference (or respect system preference)
        (function () {
            const saved = localStorage.getItem('infrahub-theme');
            if (saved) {
                document.documentElement.setAttribute('data-theme', saved);
            } else if (window.matchMedia('(prefers-color-scheme: light)').matches) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
</body>

</html>