<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>InfraHub — All-in-One Construction Management Platform</title>
    <meta name="description"
        content="InfraHub connects people, processes, and project data across the entire project lifecycle—from planning to handover and beyond.">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo/infrahub-icon.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,800&family=Inter:wght@300;400;500;600;700;800;900&display=swap"
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

        /* ─── Light Theme (Default for new landing design) ─── */
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
            --trusted-filter: grayscale(0.2) opacity(0.9);
            --logo-card-bg: #ffffff;
            --logo-card-border: #e2e8f0;
        }

        /* ─── Dark Theme Support ─── */
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
            --trusted-filter: brightness(0.95) contrast(1.05);
            --logo-card-bg: #0a1128;
            --logo-card-border: #1e293b;
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

        /* ─── Container ─── */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        /* ─── Navbar ─── */
        header {
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

        nav {
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
            gap: 26px;
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

        /* Dropdowns */
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
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
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
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .lang-select:hover {
            background: var(--slate-100);
            border-color: var(--slate-200);
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

        /* ─── Hero Section ─── */
        .hero-section {
            padding: 70px 0 90px;
            position: relative;
            background: var(--bg-hero);
            overflow: hidden;
        }

        .hero-ambient-glow {
            position: absolute;
            top: -100px;
            left: 30%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.12) 0%, rgba(249, 115, 22, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        .hero-section .container {
            max-width: 1440px;
            padding: 0 32px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1.35fr;
            gap: 56px;
            align-items: center;
        }

        .hero-content {
            max-width: 580px;
            position: relative;
            z-index: 2;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            border: 1px solid var(--slate-200);
            padding: 7px 16px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: border-color 0.2s;
        }

        .hero-pill:hover {
            border-color: var(--orange-500);
        }

        [data-theme="dark"] .hero-pill {
            background: var(--navy-900);
            border-color: var(--slate-700);
            color: var(--slate-300);
        }

        .hero-pill-icon {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            background: rgba(249, 115, 22, 0.15);
            color: var(--orange-500);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        .hero-title {
            font-size: 56px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: -1.8px;
            color: var(--text-main);
            margin-bottom: 22px;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, var(--orange-500), #f97316, #ea580c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
        }

        .hero-description {
            font-size: 18px;
            line-height: 1.65;
            color: var(--text-muted);
            margin-bottom: 38px;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn-watch {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main);
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 12px;
            transition: all 0.2s;
            background: rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border-subtle);
        }

        [data-theme="dark"] .btn-watch {
            background: rgba(255, 255, 255, 0.03);
        }

        .btn-watch:hover {
            color: var(--orange-500);
            border-color: var(--orange-500);
        }

        .play-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(249, 115, 22, 0.12);
            color: var(--orange-500);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: transform 0.2s, background 0.2s, color 0.2s;
        }

        .btn-watch:hover .play-icon {
            transform: scale(1.1);
            background: var(--orange-500);
            color: #ffffff;
        }

        .badge-duration {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-faint);
            background: var(--slate-100);
            padding: 3px 9px;
            border-radius: 12px;
            margin-left: 2px;
        }

        [data-theme="dark"] .badge-duration {
            background: var(--slate-800);
        }

        /* ─── Hero Showcase Mockups ─── */
        .showcase-wrapper {
            position: relative;
            width: 100%;
        }

        .scaffolding-bg {
            position: absolute;
            inset: -50px -30px -50px -50px;
            background: radial-gradient(circle at 70% 30%, rgba(249, 115, 22, 0.12), transparent 60%),
                        url('https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?q=80&w=1200&auto=format&fit=crop') center/cover no-repeat;
            opacity: 0.22;
            filter: grayscale(0.4) opacity(0.85);
            border-radius: 32px;
            z-index: 0;
        }

        /* Laptop Mockup */
        .laptop-frame {
            position: relative;
            z-index: 2;
            background: #0b1324;
            border-radius: 20px;
            padding: 10px 10px 18px;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.12);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .laptop-frame:hover {
            transform: translateY(-4px);
        }

        .laptop-header-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px 10px;
        }

        .dot-control {
            width: 9px;
            height: 9px;
            border-radius: 50%;
        }

        .dot-red { background: #ef4444; }
        .dot-yellow { background: #f59e0b; }
        .dot-green { background: #10b981; }

        .laptop-screen {
            background: #090f1d;
            border-radius: 0 0 14px 14px;
            overflow: hidden;
            height: 440px;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
        }

        .laptop-screen-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top left;
            display: block;
            border-radius: 0 0 14px 14px;
        }

        /* Laptop Sidebar */
        .app-sidebar {
            background: #070e20;
            color: #94a3b8;
            padding: 16px 14px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .app-sidebar-logo {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #ffffff;
            font-weight: 800;
            font-size: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .app-sidebar-logo span {
            color: var(--orange-500);
        }

        .app-menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
            list-style: none;
        }

        .app-menu-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 500;
            color: #94a3b8;
            transition: all 0.2s;
        }

        .app-menu-item.active {
            background: linear-gradient(135deg, var(--orange-500), var(--orange-600));
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        /* Laptop App Content */
        .app-content {
            background: #f8fafc;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            overflow: hidden;
        }

        .app-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .app-title {
            font-weight: 800;
            font-size: 14px;
            color: #0f172a;
        }

        .app-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .app-stat-card {
            background: #ffffff;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .app-stat-lbl {
            font-size: 9.5px;
            color: #64748b;
            font-weight: 600;
        }

        .app-stat-val {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 3px;
        }

        .app-charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .app-card {
            background: #ffffff;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .app-card-title {
            font-size: 10.5px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }

        /* Donut Chart Mock */
        .donut-mock {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .donut-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: conic-gradient(var(--orange-500) 0% 60%, #3b82f6 60% 85%, #ef4444 85% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .donut-inner {
            width: 30px;
            height: 30px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 800;
        }

        .donut-legend {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 9.5px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #475569;
        }

        .legend-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        /* ── Theme-Responsive Laptop App Dashboard Mockup ── */
        .app-content,
        .app-stat-card,
        .app-card,
        .app-topbar,
        .app-title,
        .app-stat-val,
        .app-stat-lbl,
        .donut-inner,
        .legend-item {
            transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        [data-theme="dark"] .app-content {
            background: #070c1b;
        }

        [data-theme="dark"] .app-topbar {
            border-bottom-color: #1e293b;
        }

        [data-theme="dark"] .app-title {
            color: #f8fafc;
        }

        [data-theme="dark"] .app-stat-card {
            background: #0f172a;
            border-color: #1e293b;
        }

        [data-theme="dark"] .app-stat-lbl {
            color: #94a3b8;
        }

        [data-theme="dark"] .app-stat-val {
            color: #f8fafc;
        }

        [data-theme="dark"] .app-card {
            background: #0f172a;
            border-color: #1e293b;
        }

        [data-theme="dark"] .app-card-title {
            color: #cbd5e1;
        }

        [data-theme="dark"] .donut-inner {
            background: #0f172a;
            color: #f8fafc;
        }

        [data-theme="dark"] .legend-item {
            color: #94a3b8;
        }

        /* Mobile Mockup (Overlapping Actual Mobile PWA Screenshot) */
        .mobile-frame {
            position: absolute;
            left: -50px;
            bottom: -30px;
            width: 175px;
            z-index: 10;
            background: #090e1a;
            border-radius: 30px;
            padding: 7px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6), 0 0 0 2px rgba(255, 255, 255, 0.15), 0 0 35px rgba(249, 115, 22, 0.2);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .mobile-frame:hover {
            transform: translateY(-6px) scale(1.03);
        }

        .mobile-notch {
            width: 38%;
            height: 12px;
            background: #090e1a;
            margin: 0 auto;
            border-radius: 0 0 8px 8px;
            position: absolute;
            top: 7px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 15;
        }

        .mobile-screen {
            background: #0b1329;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
            position: relative;
            height: 350px;
        }

        .mobile-screen-img {
            width: 100%;
            height: 100%;
            display: block;
            border-radius: 22px;
            object-fit: cover;
            object-position: top center;
        }

        /* ─── Trusted By Section (Infinite Marquee Scroller) ─── */
        .trusted-section {
            padding: 56px 0;
            background: var(--bg-body);
            border-top: 1px solid var(--border-subtle);
            border-bottom: 1px solid var(--border-subtle);
            position: relative;
            overflow: hidden;
        }

        .trusted-heading {
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--slate-500);
            margin-bottom: 36px;
        }

        .marquee-wrapper {
            position: relative;
            width: 100%;
            overflow: hidden;
            mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        }

        .marquee-track {
            display: flex;
            align-items: center;
            gap: 28px;
            width: max-content;
            animation: marqueeScroll 32s linear infinite;
        }

        .marquee-wrapper:hover .marquee-track {
            animation-play-state: paused;
        }

        @keyframes marqueeScroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        .trusted-logo-card {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            padding: 14px 32px;
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            flex-shrink: 0;
            height: 72px;
        }

        [data-theme="dark"] .trusted-logo-card {
            background: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
        }

        .trusted-logo-card:hover {
            transform: translateY(-4px) scale(1.04);
            border-color: var(--orange-500);
            box-shadow: 0 12px 28px rgba(249, 115, 22, 0.25);
        }

        .trusted-logo-img {
            height: 48px;
            width: auto;
            max-width: 210px;
            object-fit: contain;
            display: block;
        }


        /* ─── Deep Navy Feature Banner ─── */
        .feature-banner-section {
            background: #040814;
            color: #ffffff;
            padding: 72px 0;
            position: relative;
            overflow: hidden;
        }

        .banner-ambient {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 10% 50%, rgba(249, 115, 22, 0.1), transparent 50%),
                        radial-gradient(circle at 90% 50%, rgba(59, 130, 246, 0.1), transparent 50%);
            pointer-events: none;
        }

        .banner-grid {
            display: grid;
            grid-template-columns: 290px 1fr;
            gap: 48px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .banner-intro h2 {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.8px;
            margin-bottom: 14px;
            color: #ffffff;
        }

        .banner-intro p {
            font-size: 14px;
            line-height: 1.65;
            color: #94a3b8;
        }

        .features-cards-flex {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
        }

        .feature-item-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 20px 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .feature-item-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(249, 115, 22, 0.5);
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .f-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .f-icon-orange { background: rgba(249, 115, 22, 0.18); color: #f97316; }
        .f-icon-blue { background: rgba(59, 130, 246, 0.18); color: #3b82f6; }
        .f-icon-green { background: rgba(16, 185, 129, 0.18); color: #10b981; }

        .f-card-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.35;
        }

        .f-card-desc {
            font-size: 11.5px;
            line-height: 1.55;
            color: #94a3b8;
        }

        /* ─── Enterprise Modules Grid Section ─── */
        .modules-section {
            padding: 96px 0;
            background: var(--bg-subtle);
        }

        .section-header {
            text-align: center;
            max-width: 640px;
            margin: 0 auto 60px;
        }

        .section-tag {
            display: inline-block;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: var(--orange-500);
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 38px;
            font-weight: 800;
            letter-spacing: -1.2px;
            color: var(--text-main);
            margin-bottom: 16px;
        }

        .section-subtitle {
            font-size: 16.5px;
            color: var(--text-muted);
            line-height: 1.65;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 26px;
        }

        .mod-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 18px;
            padding: 30px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .mod-card:hover {
            border-color: var(--orange-500);
            transform: translateY(-5px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.08);
        }

        .mod-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--orange-50);
            color: var(--orange-500);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        .mod-card:hover .mod-icon {
            transform: scale(1.1);
        }

        [data-theme="dark"] .mod-icon {
            background: rgba(249, 115, 22, 0.18);
        }

        .mod-card h3 {
            font-size: 19px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 10px;
        }

        .mod-card p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.65;
        }

        /* ─── Platform Metrics Counter Section ─── */
        .metrics-section {
            padding: 72px 0;
            background: var(--bg-body);
            border-bottom: 1px solid var(--border-subtle);
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            text-align: center;
        }

        .metric-card {
            padding: 28px 20px;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 18px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .metric-card:hover {
            transform: translateY(-4px);
            border-color: var(--orange-500);
        }

        .metric-value {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: -1.5px;
            color: var(--orange-500);
            margin-bottom: 8px;
        }

        .metric-label {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .metric-desc {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        /* ─── Production CTA Banner Section ─── */
        .cta-section {
            padding: 96px 0;
            background: linear-gradient(135deg, #0a1128, #040814);
            color: #ffffff;
            position: relative;
            overflow: hidden;
            border-top: 1px solid #1e293b;
        }

        .cta-ambient {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 50%, rgba(249, 115, 22, 0.15), transparent 70%);
            pointer-events: none;
        }

        .cta-box {
            text-align: center;
            max-width: 780px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .cta-box h2 {
            font-size: 42px;
            font-weight: 800;
            letter-spacing: -1.5px;
            line-height: 1.15;
            margin-bottom: 18px;
            color: #ffffff;
        }

        .cta-box p {
            font-size: 17px;
            line-height: 1.65;
            color: #94a3b8;
            margin-bottom: 36px;
        }

        .cta-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        /* ─── Footer ─── */
        footer {
            background: #040814;
            color: #94a3b8;
            padding: 64px 0 36px;
            border-top: 1px solid #1e293b;
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

        /* ─── Responsive Media Queries ─── */
        @media (max-width: 1100px) {
            .features-cards-flex {
                grid-template-columns: repeat(3, 1fr);
            }
            .banner-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 960px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .modules-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .nav-center {
                display: none;
            }
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .modules-grid {
                grid-template-columns: 1fr;
            }
            .features-cards-flex {
                grid-template-columns: 1fr;
            }
            .hero-title {
                font-size: 40px;
            }
            .mobile-frame {
                display: none;
            }
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- Header Navigation -->
    <x-landing-nav activePage="home" />

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-ambient-glow"></div>
        <div class="container">
            <div class="hero-grid">
                
                <!-- Left Hero Content -->
                <div class="hero-content">
                    <div class="hero-pill">
                        <div class="hero-pill-icon">🏢</div>
                        <span>All-in-One Construction Management Platform</span>
                    </div>

                    <h1 class="hero-title">
                        Build Better.<br>
                        Manage Smarter.<br>
                        <span class="highlight">Deliver with Confidence.</span>
                    </h1>

                    <p class="hero-description">
                        Infrahub connects people, processes and project data across the entire project lifecycle—from planning to handover and beyond.
                    </p>

                    <div class="hero-actions">
                        <a href="/schedule-call" class="btn-demo">
                            Book a Demo <span>→</span>
                        </a>
                        <a href="#overview" class="btn-watch">
                            <div class="play-icon">▶</div>
                            <span>Watch Overview</span>
                            <span class="badge-duration">2 min</span>
                        </a>
                    </div>
                </div>

                <!-- Right Device Mockup Showcase -->
                <div class="showcase-wrapper">
                    <div class="scaffolding-bg"></div>

                    <!-- Laptop Frame Mockup -->
                    <div class="laptop-frame">
                        <div class="laptop-header-bar">
                            <div class="dot-control dot-red"></div>
                            <div class="dot-control dot-yellow"></div>
                            <div class="dot-control dot-green"></div>
                        </div>
                        <div class="laptop-screen">
                            <img src="{{ asset('images/infrahub-dashboard-preview.png') }}" alt="InfraHub Operations Overview Dashboard" class="laptop-screen-img">
                        </div>
                    </div>

                    <!-- Smartphone Frame Mockup (Overlapping Actual Mobile App Screenshot) -->
                    <div class="mobile-frame" title="InfraHub Field Operations Mobile PWA">
                        <div class="mobile-notch"></div>
                        <div class="mobile-screen">
                            <img src="{{ asset('images/infrahub-mobile-preview.png') }}" alt="InfraHub Mobile Field Operations UI" class="mobile-screen-img">
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- Trusted By Organizations Across Africa (Infinite Marquee Scroller) -->
    <section class="trusted-section">
        <div class="trusted-heading">TRUSTED BY LEADING INFRASTRUCTURE AUTHORITIES & ORGANIZATIONS</div>
        
        <div class="marquee-wrapper">
            <div class="marquee-track">
                <!-- Original Logo Set -->
                <div class="trusted-logo-card" title="African Development Bank (AfDB)">
                    <img src="{{ asset('images/logos/afdb.png') }}" alt="African Development Bank" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="World Bank Group">
                    <img src="{{ asset('images/logos/worldbank.png') }}" alt="World Bank Group" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="Uganda National Roads Authority (UNRA)">
                    <img src="{{ asset('images/logos/unra.png') }}" alt="Uganda National Roads Authority" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="Kampala Capital City Authority (KCCA)">
                    <img src="{{ asset('images/logos/kcca.png') }}" alt="Kampala Capital City Authority" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="Rwanda Utilities Regulatory Authority (RURA)">
                    <img src="{{ asset('images/logos/rura.png') }}" alt="Rwanda Utilities Regulatory Authority" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="Ministry of Works & Transport (MoWT)">
                    <img src="{{ asset('images/logos/mowt.png') }}" alt="Ministry of Works & Transport" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="Tanzania National Roads Agency (TANROADS)">
                    <img src="{{ asset('images/logos/tanroads.png') }}" alt="Tanzania National Roads Agency" class="trusted-logo-img">
                </div>

                <!-- Duplicate Logo Set for Infinite Seamless Looping -->
                <div class="trusted-logo-card" title="African Development Bank (AfDB)">
                    <img src="{{ asset('images/logos/afdb.png') }}" alt="African Development Bank" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="World Bank Group">
                    <img src="{{ asset('images/logos/worldbank.png') }}" alt="World Bank Group" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="Uganda National Roads Authority (UNRA)">
                    <img src="{{ asset('images/logos/unra.png') }}" alt="Uganda National Roads Authority" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="Kampala Capital City Authority (KCCA)">
                    <img src="{{ asset('images/logos/kcca.png') }}" alt="Kampala Capital City Authority" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="Rwanda Utilities Regulatory Authority (RURA)">
                    <img src="{{ asset('images/logos/rura.png') }}" alt="Rwanda Utilities Regulatory Authority" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="Ministry of Works & Transport (MoWT)">
                    <img src="{{ asset('images/logos/mowt.png') }}" alt="Ministry of Works & Transport" class="trusted-logo-img">
                </div>
                <div class="trusted-logo-card" title="Tanzania National Roads Agency (TANROADS)">
                    <img src="{{ asset('images/logos/tanroads.png') }}" alt="Tanzania National Roads Agency" class="trusted-logo-img">
                </div>
            </div>
        </div>
    </section>
    <!-- Built for Infrastructure Excellence (Deep Navy Feature Banner) -->
    <section class="feature-banner-section">
        <div class="banner-ambient"></div>
        <div class="container">
            <div class="banner-grid">
                <div class="banner-intro">
                    <span class="section-tag" style="color: #f97316;">Core Capabilities</span>
                    <h2>Built for Infrastructure Excellence.</h2>
                    <p>Engineered to handle complex civil works, road networks, housing developments, and public utilities across East Africa.</p>
                </div>

                <div class="features-cards-flex">
                    <div class="feature-item-card">
                        <div class="f-icon-box f-icon-orange">🏗️</div>
                        <div class="f-card-title">ISO 19650 CDE</div>
                        <div class="f-card-desc">BIM container routing, strict document metadata & audit logs.</div>
                    </div>

                    <div class="feature-item-card">
                        <div class="f-icon-box f-icon-blue">📊</div>
                        <div class="f-card-title">BOQ & Variations</div>
                        <div class="f-card-desc">Instant Excel paste import, variations tracker & IPC billing.</div>
                    </div>

                    <div class="feature-item-card">
                        <div class="f-icon-box f-icon-green">📅</div>
                        <div class="f-card-title">WBS & MS Project</div>
                        <div class="f-card-desc">Dynamic Gantt charts, XML import/export & EVM tracking.</div>
                    </div>

                    <div class="feature-item-card">
                        <div class="f-icon-box f-icon-orange">👷</div>
                        <div class="f-card-title">AI SHEQ Safety</div>
                        <div class="f-card-desc">Instant risk pulse, safety incident logs & daily site diaries.</div>
                    </div>

                    <div class="feature-item-card">
                        <div class="f-icon-box f-icon-blue">🚚</div>
                        <div class="f-card-title">Fleet Telematics</div>
                        <div class="f-card-desc">Equipment hour logs, fuel expenditure & operator rosters.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enterprise Platform Modules Grid -->
    <section class="modules-section" id="modules">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Enterprise Modules</span>
                <h2 class="section-title">Everything You Need to Control Projects</h2>
                <p class="section-subtitle">Connect site crews, quantity surveyors, project managers, and executive leaders in real time.</p>
            </div>

            <div class="modules-grid">
                <div class="mod-card">
                    <div class="mod-icon">📊</div>
                    <h3>Operations & Dashboard</h3>
                    <p>Real-time executive oversight, active project counts, work order tracking, and revenue MTD analytics in a single unified view.</p>
                </div>

                <div class="mod-card">
                    <div class="mod-icon">📅</div>
                    <h3>WBS Schedule & Gantt</h3>
                    <p>Manage project timelines with interactive Gantt charts, dependency links, milestone tracking, and seamless MS Project XML file import.</p>
                </div>

                <div class="mod-card">
                    <div class="mod-icon">📑</div>
                    <h3>ISO 19650 CDE Gateway</h3>
                    <p>Compliant Common Data Environment for drawing revisions, transmittals, digital approvals, and structured document security.</p>
                </div>

                <div class="mod-card">
                    <div class="mod-icon">📩</div>
                    <h3>RFIs & Site Communications</h3>
                    <p>Standardize Requests for Information, field queries, submittals, and site diary logs with automated email notifications and audit histories.</p>
                </div>

                <div class="mod-card">
                    <div class="mod-icon">🛡️</div>
                    <h3>AI SHEQ Safety & Risk Pulse</h3>
                    <p>Track safety incidents, environmental compliance, hazard reports, and AI-assisted safety analysis to minimize site risk.</p>
                </div>

                <div class="mod-card">
                    <div class="mod-icon">💰</div>
                    <h3>BOQ & Valuation Invoicing</h3>
                    <p>Copy-paste Bill of Quantities straight from Excel, track variation orders, and generate interim payment certificates (IPC) automatically.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Platform Impact Metrics -->
    <section class="metrics-section">
        <div class="container">
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">$1.2B+</div>
                    <div class="metric-label">Infrastructure Portfolio</div>
                    <div class="metric-desc">Tracked across regional road & civil projects</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">250+</div>
                    <div class="metric-label">Active Project Sites</div>
                    <div class="metric-desc">Connected field teams & contractor crews</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">99.9%</div>
                    <div class="metric-label">Platform Uptime SLA</div>
                    <div class="metric-desc">High-availability cloud infrastructure</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">ISO 19650</div>
                    <div class="metric-label">Certified CDE Compliance</div>
                    <div class="metric-desc">International BIM & document standards</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Production Call to Action Banner -->
    <section class="cta-section">
        <div class="cta-ambient"></div>
        <div class="container">
            <div class="cta-box">
                <h2>Ready to Standardize Your Infrastructure Projects?</h2>
                <p>Join leading contractors, ministries, and infrastructure authorities delivering projects on time and within budget with InfraHub.</p>
                <div class="cta-actions">
                    <a href="/schedule-call" class="btn-demo" style="padding: 14px 32px; font-size: 16px;">
                        Book a Demo Call <span>→</span>
                    </a>
                    <a href="/get-started" class="btn-watch" style="padding: 14px 28px; font-size: 16px;">
                        Start Free Trial
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="/" class="brand-logo">
                        <img src="{{ asset('logo/infrahub-logo-dark.png') }}" alt="InfraHub" style="height: 44px; object-fit: contain;">
                    </a>
                    <p>The unified construction project management platform engineered for Africa's leading infrastructure projects.</p>
                </div>

                <div class="footer-col">
                    <h4>Platform</h4>
                    <a href="#modules">BOQ Management</a>
                    <a href="#modules">CDE Drawings</a>
                    <a href="#modules">Daily Diaries</a>
                    <a href="#modules">Safety & HSE</a>
                </div>

                <div class="footer-col">
                    <h4>Resources</h4>
                    <a href="/docs">Documentation</a>
                    <a href="/schedule-call">Schedule a Call</a>
                    <a href="/get-started">Get Started</a>
                    <a href="/mobile">Mobile App</a>
                </div>

                <div class="footer-col">
                    <h4>Company</h4>
                    <a href="#about">About Us</a>
                    <a href="#pricing">Pricing</a>
                    <a href="/health">System Status</a>
                    <a href="/login">Login</a>
                </div>
            </div>

            <div class="footer-bottom">
                <div>&copy; {{ date('Y') }} InfraHub Platform. All rights reserved.</div>
                <div style="display: flex; gap: 16px;">
                    <a href="/docs" style="color: #64748b; text-decoration: none;">Privacy Policy</a>
                    <a href="/docs" style="color: #64748b; text-decoration: none;">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function applyLogoForTheme(theme) {
            const logoImg = document.getElementById('header-brand-logo');
            if (logoImg) {
                logoImg.src = theme === 'dark' ? "{{ asset('logo/infrahub-logo-dark.png') }}" : "{{ asset('logo/infrahub-logo-new.png') }}";
            }
        }

        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            const icon = document.getElementById('theme-icon');
            if (icon) icon.textContent = next === 'dark' ? '☀️' : '🌙';
            localStorage.setItem('theme', next);
            applyLogoForTheme(next);
        }

        // Initialize saved theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
            const icon = document.getElementById('theme-icon');
            if (icon) icon.textContent = savedTheme === 'dark' ? '☀️' : '🌙';
            applyLogoForTheme(savedTheme);
        }
    </script>
</body>

</html>