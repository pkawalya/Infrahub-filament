<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>InfraHub — Construction Project Management Platform</title>
    <meta name="description"
        content="InfraHub is an all-in-one construction project management platform. Manage projects, BOQs, contracts, safety, field ops, and teams — from one powerful dashboard.">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo/infrahub-icon.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

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
            --navy-500: #2a4d7a;
            --navy-400: #3d6899;
            --amber-300: #f5c563;
            --amber-400: #e8a229;
            --amber-500: #d4911e;
            --amber-600: #b87a15;
            --gray-brand: #7a7d80;
            --emerald-400: #34d399;
            --emerald-500: #10b981;
            --rose-500: #f43f5e;
            --blue-500: #3b82f6;
            --violet-500: #8b5cf6;
        }

        /* ─── Dark Theme (default) ─── */
        [data-theme="dark"] {
            --bg-body: #020617;
            --bg-card: rgba(255, 255, 255, 0.02);
            --bg-card-hover: rgba(255, 255, 255, 0.05);
            --bg-elevated: #0f172a;
            --bg-stat: #0f172a;
            --bg-glass: rgba(15, 23, 42, 0.6);
            --border-subtle: #1e293b;
            --border-hover: #334155;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --text-faint: #475569;
            --grid-line: rgba(232, 162, 41, 0.03);
            --glow-opacity: 0.15;
            --ghost-bg: rgba(255, 255, 255, 0.03);
            --ghost-border: #334155;
            --ghost-text: #cbd5e1;
            --badge-bg: rgba(232, 162, 41, 0.1);
            --badge-border: rgba(232, 162, 41, 0.2);
            --cta-bg: linear-gradient(135deg, rgba(30, 58, 95, 0.15), rgba(232, 162, 41, 0.05));
            --nav-hover-bg: rgba(255, 255, 255, 0.05);
            --testimonial-bg: rgba(15, 23, 42, 0.5);
            --step-line: #1e293b;
            --trusted-filter: brightness(0.7) grayscale(0.5);
            --trusted-hover-filter: brightness(1) grayscale(0);
        }

        /* ─── Light Theme ─── */
        [data-theme="light"] {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-card-hover: #f1f5f9;
            --bg-elevated: #ffffff;
            --bg-stat: #ffffff;
            --bg-glass: rgba(255, 255, 255, 0.7);
            --border-subtle: #e2e8f0;
            --border-hover: #cbd5e1;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --text-faint: #94a3b8;
            --grid-line: rgba(30, 58, 95, 0.04);
            --glow-opacity: 0.06;
            --ghost-bg: #ffffff;
            --ghost-border: #cbd5e1;
            --ghost-text: #334155;
            --badge-bg: rgba(232, 162, 41, 0.08);
            --badge-border: rgba(232, 162, 41, 0.2);
            --cta-bg: linear-gradient(135deg, rgba(30, 58, 95, 0.06), rgba(232, 162, 41, 0.04));
            --nav-hover-bg: rgba(0, 0, 0, 0.04);
            --testimonial-bg: rgba(255, 255, 255, 0.8);
            --step-line: #e2e8f0;
            --trusted-filter: grayscale(0.6) opacity(0.6);
            --trusted-hover-filter: grayscale(0) opacity(1);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
        }

        /* --- Background --- */
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
            width: 600px;
            height: 600px;
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

        .bg-glow-3 {
            top: 40%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--amber-400);
            opacity: 0.06;
        }

        /* Floating particles */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: var(--amber-400);
            opacity: 0;
            animation: float-up linear infinite;
        }

        @keyframes float-up {
            0% {
                opacity: 0;
                transform: translateY(100vh) scale(0);
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                opacity: 0;
                transform: translateY(-10vh) scale(1);
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        /* --- Nav --- */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: var(--bg-glass);
            margin: 0 -24px;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-subtle);
        }

        nav .inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: var(--text-primary);
            background: var(--nav-hover-bg);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--amber-400), var(--amber-500));
            color: var(--navy-700);
            box-shadow: 0 4px 20px rgba(232, 162, 41, 0.3);
            font-weight: 700;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(232, 162, 41, 0.4);
        }

        .btn-ghost {
            color: var(--ghost-text);
            border: 1px solid var(--ghost-border);
            background: var(--ghost-bg);
        }

        .btn-ghost:hover {
            border-color: var(--amber-400);
            color: var(--text-primary);
            background: rgba(232, 162, 41, 0.05);
        }

        .btn-lg {
            padding: 16px 32px;
            font-size: 16px;
            border-radius: 14px;
        }

        /* --- Hero --- */
        .hero {
            padding: 100px 0 60px;
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--badge-bg);
            border: 1px solid var(--badge-border);
            padding: 8px 18px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            color: var(--amber-400);
            margin-bottom: 28px;
            animation: fadeInDown 0.6s ease-out;
        }

        .hero-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--amber-300);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-size: clamp(40px, 6vw, 72px);
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: -2px;
            margin-bottom: 24px;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .hero h1 .gradient {
            background: linear-gradient(135deg, var(--amber-300) 0%, var(--amber-400) 40%, var(--amber-500) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 18px;
            color: var(--text-secondary);
            max-width: 640px;
            margin: 0 auto 40px;
            line-height: 1.7;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .hero-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        /* --- Trusted By --- */
        .trusted-section {
            padding: 40px 0;
            text-align: center;
            opacity: 0.8;
        }

        .trusted-section p {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-faint);
            font-weight: 600;
            margin-bottom: 24px;
        }

        .trusted-logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 48px;
            flex-wrap: wrap;
        }

        .trusted-logo {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-faint);
            letter-spacing: -0.5px;
            filter: var(--trusted-filter);
            transition: filter 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .trusted-logo:hover {
            filter: var(--trusted-hover-filter);
        }

        .trusted-logo span {
            font-size: 24px;
        }

        /* --- Stats Bar --- */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--border-subtle);
            border-radius: 16px;
            overflow: hidden;
            margin: 40px 0 80px;
            border: 1px solid var(--border-subtle);
        }

        .stat-item {
            background: var(--bg-stat);
            padding: 32px;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-item:hover {
            background: var(--bg-card-hover);
        }

        .stat-number {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, var(--amber-300), var(--amber-500));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- How It Works --- */
        .how-section {
            padding: 80px 0;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            position: relative;
        }

        .steps-grid::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 15%;
            right: 15%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--amber-400), var(--amber-400), transparent);
            opacity: 0.2;
        }

        .step-card {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--amber-400), var(--amber-500));
            color: var(--navy-700);
            font-size: 22px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 30px rgba(232, 162, 41, 0.25);
            position: relative;
            z-index: 1;
        }

        .step-card h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .step-card p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            max-width: 280px;
            margin: 0 auto;
        }

        /* --- Modules --- */
        .section-title {
            text-align: center;
            margin-bottom: 56px;
        }

        .section-title h2 {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 12px;
        }

        .section-title p {
            font-size: 16px;
            color: var(--text-secondary);
            max-width: 560px;
            margin: 0 auto;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 20px;
            margin-bottom: 100px;
        }

        .module-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 32px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--amber-400), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .module-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            background: radial-gradient(circle at top right, rgba(232, 162, 41, 0.04), transparent 60%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .module-card:hover {
            border-color: var(--border-hover);
            background: var(--bg-card-hover);
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
        }

        .module-card:hover::before {
            opacity: 1;
        }

        .module-card:hover::after {
            opacity: 1;
        }

        .module-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 22px;
            position: relative;
            z-index: 1;
        }

        .module-card h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .module-card p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        /* --- Testimonials --- */
        .testimonials-section {
            padding: 80px 0;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .testimonial-card {
            background: var(--testimonial-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            padding: 32px;
            transition: all 0.3s;
        }

        .testimonial-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-hover);
        }

        .testimonial-stars {
            color: var(--amber-400);
            font-size: 14px;
            letter-spacing: 2px;
            margin-bottom: 16px;
        }

        .testimonial-text {
            font-size: 15px;
            line-height: 1.7;
            color: var(--text-secondary);
            margin-bottom: 24px;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .testimonial-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            color: white;
        }

        .testimonial-info strong {
            display: block;
            font-size: 14px;
            font-weight: 600;
        }

        .testimonial-info span {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* --- CTA --- */
        .cta {
            text-align: center;
            padding: 80px 40px;
            margin-bottom: 80px;
            background: var(--cta-bg);
            border: 1px solid var(--border-subtle);
            border-radius: 24px;
            position: relative;
            overflow: hidden;
        }

        .cta::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 20%;
            right: 20%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--amber-400), transparent);
        }

        .cta::after {
            content: '';
            position: absolute;
            bottom: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: var(--amber-400);
            opacity: 0.05;
            filter: blur(60px);
        }

        .cta h2 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .cta p {
            color: var(--text-secondary);
            margin-bottom: 32px;
            font-size: 16px;
        }

        .cta-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* --- Footer --- */
        footer {
            border-top: 1px solid var(--border-subtle);
            padding: 60px 0 32px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-brand p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-top: 12px;
            max-width: 280px;
        }

        .footer-col h4 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .footer-col a {
            display: block;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            padding: 6px 0;
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: var(--amber-400);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 24px;
            border-top: 1px solid var(--border-subtle);
        }

        .footer-bottom small {
            color: var(--text-faint);
            font-size: 13px;
        }

        .footer-socials {
            display: flex;
            gap: 12px;
        }

        .footer-socials a {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 16px;
            transition: all 0.2s;
        }

        .footer-socials a:hover {
            border-color: var(--amber-400);
            color: var(--amber-400);
            background: rgba(232, 162, 41, 0.08);
        }

        /* ─── Theme Toggle ─── */
        .theme-toggle {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--nav-hover-bg);
            border: 1px solid var(--border-subtle);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 18px;
            color: var(--text-secondary);
            flex-shrink: 0;
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

        /* Scroll reveal */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Smooth transitions for themed elements */
        nav,
        .stats-bar,
        .stat-item,
        .module-card,
        .cta,
        footer,
        .hero-badge,
        .btn-ghost,
        .theme-toggle,
        .testimonial-card,
        .step-card {
            transition: background 0.3s, border-color 0.3s, color 0.3s, box-shadow 0.3s;
        }

        /* --- Responsive --- */
        @media (max-width: 900px) {
            .steps-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .steps-grid::before {
                display: none;
            }

            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 32px;
            }
        }

        @media (max-width: 768px) {
            .stats-bar {
                grid-template-columns: repeat(2, 1fr);
            }

            .modules-grid {
                grid-template-columns: 1fr;
            }

            .hero {
                padding: 60px 0 40px;
            }

            .nav-links .nav-link {
                display: none;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .trusted-logos {
                gap: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    <div class="bg-glow bg-glow-3"></div>

    <!-- Floating particles -->
    <div class="particles" id="particles"></div>

    <!-- Nav (sticky + glass) -->
    <nav>
        <div class="inner">
            <a href="/" class="logo">
                <img src="{{ asset('logo/infrahub-logo-new.png') }}" alt="InfraHub"
                    style="height: 44px; border-radius: 12px;">
            </a>

            <div class="nav-links">
                <a href="#modules" class="nav-link">Modules</a>
                <a href="/schedule-call" class="nav-link">Schedule a Call</a>
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle light/dark mode"
                    aria-label="Toggle theme">
                    <span class="icon-moon">🌙</span>
                    <span class="icon-sun">☀️</span>
                </button>
                @auth
                    <a href="{{ url('/admin') }}" class="nav-link">Super Admin</a>
                    <a href="{{ url('/app') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ url('/app/login') }}" class="nav-link">Log in</a>
                    <a href="{{ url('/get-started') }}" class="btn btn-primary">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero -->
        <section class="hero">
            <div class="hero-badge">
                <span class="dot"></span>
                Built for Construction & Infrastructure Teams
            </div>
            <h1>
                Build Smarter with<br>
                <span class="gradient">InfraHub</span>
            </h1>
            <p>
                The all-in-one construction project management platform.
                Manage BOQs, contracts, safety, field operations, documents, and teams — from a single powerful
                dashboard.
            </p>
            <div class="hero-actions">
                <a href="/get-started" class="btn btn-primary btn-lg">
                    Get Started
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
                <a href="#modules" class="btn btn-ghost btn-lg">
                    Explore Modules
                </a>
            </div>
        </section>

        <!-- Trusted By -->
        <div class="trusted-section reveal">
            <p>Trusted by teams across Africa & beyond</p>
            <div class="trusted-logos">
                <div class="trusted-logo"><span>🏗️</span> Roko Construction</div>
                <div class="trusted-logo"><span>🛣️</span> UNRA</div>
                <div class="trusted-logo"><span>⚡</span> Karuma HPP</div>
                <div class="trusted-logo"><span>✈️</span> UCAA</div>
                <div class="trusted-logo"><span>🏢</span> SBI Intl</div>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-bar reveal">
            <div class="stat-item">
                <div class="stat-number" data-target="10">0</div>
                <div class="stat-label">Project Modules</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">∞</div>
                <div class="stat-label">Multi-Company</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Real-Time Data</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Cloud Based</div>
            </div>
        </div>

        <!-- How It Works -->
        <section class="how-section reveal">
            <div class="section-title">
                <h2>Up & Running in 3 Steps</h2>
                <p>From signup to managing your first project in under 10 minutes</p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>Create Your Account</h3>
                    <p>Sign up your company, invite your team members, and choose a plan that fits.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>Set Up Projects</h3>
                    <p>Create projects, assign managers, enable the modules you need, and configure workflows.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>Manage & Track</h3>
                    <p>Track progress, costs, safety, and documents in real time. Generate reports with one click.</p>
                </div>
            </div>
        </section>

        <!-- Modules -->
        <section id="modules" class="reveal">
            <div class="section-title">
                <h2>Everything Your Projects Need</h2>
                <p>10 integrated modules covering every phase of construction project delivery.</p>
            </div>

            <div class="modules-grid">
                <div class="module-card">
                    <div class="module-icon" style="background: rgba(56, 189, 248, 0.1);">📋</div>
                    <h3>Task & Workflow</h3>
                    <p>Create, assign, and track tasks with priorities, due dates, status tracking, and team
                        assignments. Monitor progress in real time.</p>
                </div>
                <div class="module-card">
                    <div class="module-icon" style="background: rgba(99, 102, 241, 0.1);">📁</div>
                    <h3>Document Management (CDE)</h3>
                    <p>Common Data Environment with folder structures, version control, RFIs, submittals, and full
                        document audit trails.</p>
                </div>
                <div class="module-card">
                    <div class="module-icon" style="background: rgba(16, 185, 129, 0.1);">📊</div>
                    <h3>BOQ Management</h3>
                    <p>Bills of Quantities with line items, cost estimation, revisions, and approval workflows. Track
                        project costing at every stage.</p>
                </div>
                <div class="module-card">
                    <div class="module-icon" style="background: rgba(245, 158, 11, 0.1);">📝</div>
                    <h3>Cost & Contracts</h3>
                    <p>Manage contracts, variations, payment applications, and track original vs revised values with
                        active status monitoring.</p>
                </div>
                <div class="module-card">
                    <div class="module-icon" style="background: rgba(244, 63, 94, 0.1);">🛡️</div>
                    <h3>SHEQ</h3>
                    <p>Safety, Health, Environment & Quality. Log incidents, track severity, perform inspections, and
                        maintain compliance records.</p>
                </div>
                <div class="module-card">
                    <div class="module-icon" style="background: rgba(139, 92, 246, 0.1);">🏗️</div>
                    <h3>Field Management</h3>
                    <p>Daily site logs with weather, workforce count, materials received, delays, and work summaries.
                        Complete site diary system.</p>
                </div>
                <div class="module-card">
                    <div class="module-icon" style="background: rgba(34, 211, 238, 0.1);">📦</div>
                    <h3>Inventory & Procurement</h3>
                    <p>Purchase orders, stock tracking, supplier management, and delivery monitoring. Keep materials
                        flowing to your projects.</p>
                </div>
                <div class="module-card">
                    <div class="module-icon" style="background: rgba(52, 211, 153, 0.1);">🎯</div>
                    <h3>Planning & Progress</h3>
                    <p>Milestones, schedule tracking, progress percentages, and schedule health indicators. Know if
                        you're on track at a glance.</p>
                </div>
                <div class="module-card">
                    <div class="module-icon" style="background: rgba(251, 191, 36, 0.1);">🔧</div>
                    <h3>Core FSM</h3>
                    <p>Work orders, service requests, asset management, and invoicing. Manage field service operations
                        end-to-end.</p>
                </div>
                <div class="module-card">
                    <div class="module-icon" style="background: rgba(59, 130, 246, 0.1);">📈</div>
                    <h3>Reporting & Dashboards</h3>
                    <p>Aggregated project analytics with progress tracking, financial summaries, and exportable reports
                        for stakeholders.</p>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="testimonials-section reveal">
            <div class="section-title">
                <h2>Trusted by Industry Leaders</h2>
                <p>See what construction professionals say about InfraHub</p>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-stars">★★★★★</div>
                    <p class="testimonial-text">"InfraHub transformed how we manage our highway projects. The BOQ module
                        alone saved us 20+ hours per week on cost tracking and reconciliation."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"
                            style="background: linear-gradient(135deg, var(--amber-400), var(--amber-500));">JK</div>
                        <div class="testimonial-info">
                            <strong>James Kato</strong>
                            <span>Project Director, Roko Construction</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars">★★★★★</div>
                    <p class="testimonial-text">"The safety and compliance module is exceptional. We went from
                        paper-based checklists to a fully digital SHEQ system in just one week."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"
                            style="background: linear-gradient(135deg, var(--emerald-400), var(--emerald-500));">AN
                        </div>
                        <div class="testimonial-info">
                            <strong>Amina Nassur</strong>
                            <span>SHEQ Manager, Stirling Civil</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars">★★★★★</div>
                    <p class="testimonial-text">"Having all project documents, contracts, and field reports in one
                        platform is a game changer. No more digging through shared drives."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"
                            style="background: linear-gradient(135deg, var(--blue-500), var(--violet-500));">DM</div>
                        <div class="testimonial-info">
                            <strong>David Mukisa</strong>
                            <span>CEO, Multiplex Construction</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <div class="cta reveal">
            <h2>Ready to Build Better?</h2>
            <p>Start your 14-day free trial. No credit card required. Onboard your team in minutes.</p>
            <div class="cta-buttons">
                <a href="/get-started" class="btn btn-primary btn-lg">
                    Get Started
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
                <a href="/schedule-call" class="btn btn-ghost btn-lg">
                    Schedule a Call
                </a>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="/" class="logo" style="text-decoration:none;">
                        <img src="{{ asset('logo/infrahub-logo-new.png') }}" alt="InfraHub"
                            style="height: 36px; border-radius: 8px;">
                    </a>
                    <p>The all-in-one construction project management platform built for African infrastructure teams
                        and beyond.</p>
                </div>
                <div class="footer-col">
                    <h4>Product</h4>
                    <a href="#modules">Modules</a>
                    <a href="/get-started">Pricing</a>
                    <a href="/schedule-call">Book a Demo</a>
                </div>
                <div class="footer-col">
                    <h4>Resources</h4>
                    <a href="#">Documentation</a>
                    <a href="#">API Reference</a>
                    <a href="#">Release Notes</a>
                </div>
                <div class="footer-col">
                    <h4>Company</h4>
                    <a href="#">About Us</a>
                    <a href="#">Careers</a>
                    <a href="#">Contact</a>
                </div>
            </div>

            <div class="footer-bottom">
                <small>© {{ date('Y') }} InfraHub. All rights reserved.</small>
                <div class="footer-socials">
                    <a href="#" title="Twitter" aria-label="Twitter">𝕏</a>
                    <a href="#" title="LinkedIn" aria-label="LinkedIn">in</a>
                    <a href="#" title="GitHub" aria-label="GitHub">⌨</a>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Theme toggle
        function toggleTheme() {
            const html = document.documentElement;
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('infrahub-theme', next);
        }

        // Restore saved preference
        (function () {
            const saved = localStorage.getItem('infrahub-theme');
            if (saved) {
                document.documentElement.setAttribute('data-theme', saved);
            } else if (window.matchMedia('(prefers-color-scheme: light)').matches) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();

        // Floating particles
        (function () {
            const container = document.getElementById('particles');
            for (let i = 0; i < 20; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                p.style.left = Math.random() * 100 + '%';
                p.style.animationDuration = (8 + Math.random() * 12) + 's';
                p.style.animationDelay = (Math.random() * 10) + 's';
                p.style.width = (2 + Math.random() * 3) + 'px';
                p.style.height = p.style.width;
                container.appendChild(p);
            }
        })();

        // Scroll reveal
        (function () {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        })();

        // Animated stat counter
        (function () {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const target = parseInt(el.dataset.target);
                        if (!target) return;
                        let current = 0;
                        const step = Math.ceil(target / 30);
                        const timer = setInterval(() => {
                            current += step;
                            if (current >= target) {
                                current = target;
                                clearInterval(timer);
                            }
                            el.textContent = current + '+';
                        }, 40);
                        observer.unobserve(el);
                    }
                });
            }, { threshold: 0.5 });

            document.querySelectorAll('[data-target]').forEach(el => observer.observe(el));
        })();
    </script>
</body>

</html>