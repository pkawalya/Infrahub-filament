<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schedule a Call — InfraHub</title>
    <meta name="description"
        content="Book a personalized demo call with the InfraHub team. See how our construction project management platform can transform your operations.">

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
            --amber-300: #f5c563;
            --amber-400: #e8a229;
            --amber-500: #d4911e;
            --emerald-400: #34d399;
            --emerald-500: #10b981;
            --rose-500: #f43f5e;
        }

        [data-theme="dark"] {
            --bg-body: #020617;
            --bg-card: rgba(255, 255, 255, 0.02);
            --bg-card-hover: rgba(255, 255, 255, 0.05);
            --bg-elevated: #0f172a;
            --bg-glass: rgba(15, 23, 42, 0.6);
            --bg-input: rgba(15, 23, 42, 0.8);
            --border-subtle: #1e293b;
            --border-hover: #334155;
            --border-focus: var(--amber-400);
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --text-faint: #475569;
            --grid-line: rgba(232, 162, 41, 0.03);
            --glow-opacity: 0.15;
            --ghost-bg: rgba(255, 255, 255, 0.03);
            --ghost-border: #334155;
            --ghost-text: #cbd5e1;
            --nav-hover-bg: rgba(255, 255, 255, 0.05);
            --success-bg: rgba(16, 185, 129, 0.1);
            --success-border: rgba(16, 185, 129, 0.3);
            --success-text: #34d399;
            --error-bg: rgba(244, 63, 94, 0.1);
            --error-border: rgba(244, 63, 94, 0.3);
            --error-text: #fb7185;
        }

        [data-theme="light"] {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-card-hover: #f1f5f9;
            --bg-elevated: #ffffff;
            --bg-glass: rgba(255, 255, 255, 0.7);
            --bg-input: #ffffff;
            --border-subtle: #e2e8f0;
            --border-hover: #cbd5e1;
            --border-focus: var(--amber-500);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --text-faint: #94a3b8;
            --grid-line: rgba(30, 58, 95, 0.04);
            --glow-opacity: 0.06;
            --ghost-bg: #ffffff;
            --ghost-border: #cbd5e1;
            --ghost-text: #334155;
            --nav-hover-bg: rgba(0, 0, 0, 0.04);
            --success-bg: rgba(16, 185, 129, 0.08);
            --success-border: rgba(16, 185, 129, 0.25);
            --success-text: #059669;
            --error-bg: rgba(244, 63, 94, 0.06);
            --error-border: rgba(244, 63, 94, 0.2);
            --error-text: #e11d48;
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
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        /* Background */
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
            top: -150px;
            right: -80px;
            background: var(--amber-400);
        }

        .bg-glow-2 {
            bottom: -150px;
            left: -80px;
            background: var(--navy-600);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        /* Nav */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            font-family: inherit;
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

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
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
            width: 100%;
            justify-content: center;
        }

        /* Theme Toggle */
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

        /* Page Layout */
        .page-content {
            padding: 60px 0 80px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: start;
        }

        /* Left Column — Info */
        .info-column {
            padding-top: 20px;
        }

        .info-column .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(232, 162, 41, 0.1);
            border: 1px solid rgba(232, 162, 41, 0.2);
            padding: 8px 18px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            color: var(--amber-400);
            margin-bottom: 24px;
            animation: fadeInDown 0.6s ease-out;
        }

        .badge .dot {
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

        .info-column h1 {
            font-size: 44px;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -1.5px;
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease-out 0.1s both;
        }

        .info-column h1 .gradient {
            background: linear-gradient(135deg, var(--amber-300), var(--amber-400), var(--amber-500));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .info-column>p {
            font-size: 17px;
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 40px;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        .info-features {
            display: flex;
            flex-direction: column;
            gap: 20px;
            animation: fadeInUp 0.8s ease-out 0.5s both;
        }

        .info-feature {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .info-feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: rgba(232, 162, 41, 0.1);
            border: 1px solid rgba(232, 162, 41, 0.15);
            flex-shrink: 0;
        }

        .info-feature-text h3 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .info-feature-text p {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .contact-info {
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid var(--border-subtle);
            animation: fadeInUp 0.8s ease-out 0.7s both;
        }

        .contact-info h4 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 16px;
        }

        .contact-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .contact-link {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .contact-link:hover {
            color: var(--amber-400);
        }

        .contact-link span {
            font-size: 16px;
        }

        /* Right Column — Form */
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 24px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20%;
            right: 20%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--amber-400), transparent);
        }

        .form-card::after {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: var(--amber-400);
            opacity: 0.03;
            filter: blur(60px);
            pointer-events: none;
        }

        .form-card h2 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .form-card .subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .form-group label .required {
            color: var(--rose-500);
            margin-left: 2px;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid var(--border-subtle);
            background: var(--bg-input);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 14px;
            transition: all 0.2s;
            outline: none;
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: var(--text-faint);
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(232, 162, 41, 0.1);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        .form-textarea {
            resize: vertical;
            min-height: 90px;
        }

        .form-error {
            font-size: 12px;
            color: var(--error-text);
            margin-top: 2px;
        }

        .form-submit-row {
            grid-column: 1 / -1;
            margin-top: 8px;
        }

        /* Success Alert */
        .alert-success {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: fadeInDown 0.5s ease-out;
        }

        .alert-success .alert-icon {
            font-size: 22px;
            flex-shrink: 0;
        }

        .alert-success .alert-text h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--success-text);
            margin-bottom: 4px;
        }

        .alert-success .alert-text p {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Error List */
        .alert-errors {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .alert-errors p {
            font-size: 13px;
            font-weight: 600;
            color: var(--error-text);
            margin-bottom: 8px;
        }

        .alert-errors ul {
            list-style: none;
            padding: 0;
        }

        .alert-errors ul li {
            font-size: 13px;
            color: var(--error-text);
            padding: 2px 0;
        }

        .alert-errors ul li::before {
            content: '•';
            margin-right: 8px;
            opacity: 0.6;
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--border-subtle);
            padding: 32px 0;
            text-align: center;
        }

        footer small {
            color: var(--text-faint);
            font-size: 13px;
        }

        footer a {
            color: var(--amber-400);
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .page-content {
                grid-template-columns: 1fr;
                gap: 40px;
                padding: 40px 0 60px;
            }

            .info-column h1 {
                font-size: 34px;
            }

            .form-card {
                padding: 28px;
            }
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .nav-links .nav-link {
                display: none;
            }

            .info-column h1 {
                font-size: 28px;
            }
        }

        /* Spinner for submit button */
        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid var(--navy-700);
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-primary.loading .spinner {
            display: inline-block;
        }

        .btn-primary.loading .btn-text {
            opacity: 0.7;
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <!-- Nav -->
    <nav>
        <div class="inner">
            <a href="/" class="logo">
                <img src="{{ asset('logo/infrahub-logo-new.png') }}" alt="InfraHub"
                    style="height: 44px; border-radius: 12px;">
            </a>
            <div class="nav-links">
                <a href="/" class="nav-link">Home</a>
                <a href="/docs" class="nav-link">Docs</a>
                <a href="/get-started" class="nav-link">Get Started</a>
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle theme" aria-label="Toggle theme">
                    <span class="icon-moon">🌙</span>
                    <span class="icon-sun">☀️</span>
                </button>
                <a href="/app/login" class="btn btn-primary" style="padding:10px 20px; font-size:13px;">Sign In</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-content">
            <!-- Left: Info -->
            <div class="info-column">
                <div class="badge">
                    <span class="dot"></span>
                    Free Demo — No Obligation
                </div>

                <h1>Let's Talk About Your <span class="gradient">Projects</span></h1>

                <p>Book a personalized demo with our team. We'll walk you through InfraHub's modules, answer your
                    questions, and help you find the right plan for your organization.</p>

                <div class="info-features">
                    <div class="info-feature">
                        <div class="info-feature-icon">🎯</div>
                        <div class="info-feature-text">
                            <h3>Personalized Demo</h3>
                            <p>We tailor the walkthrough to your industry, team size, and specific project challenges.
                            </p>
                        </div>
                    </div>
                    <div class="info-feature">
                        <div class="info-feature-icon">⚡</div>
                        <div class="info-feature-text">
                            <h3>Quick Setup</h3>
                            <p>Get onboarded in under 30 minutes. We handle migration and configuration for you.</p>
                        </div>
                    </div>
                    <div class="info-feature">
                        <div class="info-feature-icon">🛡️</div>
                        <div class="info-feature-text">
                            <h3>14-Day Free Trial</h3>
                            <p>Start managing projects immediately with full access — no credit card required.</p>
                        </div>
                    </div>
                    <div class="info-feature">
                        <div class="info-feature-icon">🌍</div>
                        <div class="info-feature-text">
                            <h3>Africa-First</h3>
                            <p>Built for East African construction firms with local currency, offline support, and
                                regional compliance.</p>
                        </div>
                    </div>
                </div>

                <div class="contact-info">
                    <h4>Or reach us directly</h4>
                    <div class="contact-links">
                        <a href="mailto:info@infrahub.click" class="contact-link">
                            <span>📧</span> info@infrahub.click
                        </a>
                        <a href="https://infrahub.click" class="contact-link" target="_blank">
                            <span>🌐</span> infrahub.click
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="form-card">
                <h2>Schedule Your Call</h2>
                <p class="subtitle">Fill in the details below and we'll get back to you within 24 hours.</p>

                @if (session('success'))
                    <div class="alert-success">
                        <span class="alert-icon">✅</span>
                        <div class="alert-text">
                            <h3>Request Received!</h3>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-errors">
                        <p>Please fix the following:</p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('schedule-call.store') }}" method="POST" id="scheduleForm">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Full Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" class="form-input" placeholder="John Doe"
                                value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="form-input"
                                placeholder="john@company.com" value="{{ old('email') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="+256 700 000 000"
                                value="{{ old('phone') }}">
                        </div>

                        <div class="form-group">
                            <label for="company">Company Name</label>
                            <input type="text" id="company" name="company" class="form-input" placeholder="Your company"
                                value="{{ old('company') }}">
                        </div>

                        <div class="form-group">
                            <label for="team_size">Team Size</label>
                            <select id="team_size" name="team_size" class="form-select">
                                <option value="">Select team size</option>
                                <option value="1-5" {{ old('team_size') == '1-5' ? 'selected' : '' }}>1–5 people</option>
                                <option value="6-20" {{ old('team_size') == '6-20' ? 'selected' : '' }}>6–20 people
                                </option>
                                <option value="21-50" {{ old('team_size') == '21-50' ? 'selected' : '' }}>21–50 people
                                </option>
                                <option value="51-100" {{ old('team_size') == '51-100' ? 'selected' : '' }}>51–100 people
                                </option>
                                <option value="100+" {{ old('team_size') == '100+' ? 'selected' : '' }}>100+ people
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="preferred_date">Preferred Date <span class="required">*</span></label>
                            <input type="date" id="preferred_date" name="preferred_date" class="form-input"
                                value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="preferred_time">Preferred Time <span class="required">*</span></label>
                            <select id="preferred_time" name="preferred_time" class="form-select" required>
                                <option value="">Choose a time slot</option>
                                <option value="09:00 - 10:00 EAT" {{ old('preferred_time') == '09:00 - 10:00 EAT' ? 'selected' : '' }}>🌅 9:00 AM – 10:00 AM (EAT)</option>
                                <option value="10:00 - 11:00 EAT" {{ old('preferred_time') == '10:00 - 11:00 EAT' ? 'selected' : '' }}>☀️ 10:00 AM – 11:00 AM (EAT)</option>
                                <option value="11:00 - 12:00 EAT" {{ old('preferred_time') == '11:00 - 12:00 EAT' ? 'selected' : '' }}>☀️ 11:00 AM – 12:00 PM (EAT)</option>
                                <option value="14:00 - 15:00 EAT" {{ old('preferred_time') == '14:00 - 15:00 EAT' ? 'selected' : '' }}>🌤️ 2:00 PM – 3:00 PM (EAT)</option>
                                <option value="15:00 - 16:00 EAT" {{ old('preferred_time') == '15:00 - 16:00 EAT' ? 'selected' : '' }}>🌤️ 3:00 PM – 4:00 PM (EAT)</option>
                                <option value="16:00 - 17:00 EAT" {{ old('preferred_time') == '16:00 - 17:00 EAT' ? 'selected' : '' }}>🌇 4:00 PM – 5:00 PM (EAT)</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="message">How can we help? <small
                                    style="font-weight:400; color:var(--text-faint)">(optional)</small></label>
                            <textarea id="message" name="message" class="form-textarea"
                                placeholder="Tell us about your projects, challenges, or what you'd like to see in the demo...">{{ old('message') }}</textarea>
                        </div>

                        <div class="form-submit-row">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <span class="btn-text">📞 Schedule My Call</span>
                                <span class="spinner"></span>
                            </button>
                            <p style="font-size: 12px; color: var(--text-faint); margin-top: 12px; text-align: center;">
                                By submitting, you agree to receive a call from the InfraHub team at your chosen time.
                                No spam, ever.
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <small>© {{ date('Y') }} InfraHub. All rights reserved. · <a href="/">Home</a> · <a
                    href="/docs">Documentation</a> · <a href="/get-started">Get Started</a></small>
        </div>
    </footer>

    <script>
        // Theme toggle
        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('infrahub-theme', next);
        }

        // Restore saved theme
        (function () {
            const saved = localStorage.getItem('infrahub-theme');
            if (saved) document.documentElement.setAttribute('data-theme', saved);
        })();

        // Submit button loading state
        document.getElementById('scheduleForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });
    </script>
</body>

</html>