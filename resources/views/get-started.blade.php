<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schedule a Call — InfraHub</title>
    <meta name="description"
        content="Book a personalized demo call with the InfraHub team. See how our construction project management platform can transform your operations.">

    <link rel="icon" type="image/svg+xml" href="{{ asset('logo/infrahub-icon.svg') }}">
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
            --bg-card: rgba(255, 255, 255, 0.03);
            --bg-input: rgba(255, 255, 255, 0.04);
            --bg-input-focus: rgba(255, 255, 255, 0.06);
            --border-subtle: #1e293b;
            --border-hover: #334155;
            --border-focus: var(--amber-400);
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --text-faint: #475569;
            --grid-line: rgba(232, 162, 41, 0.03);
            --glow-opacity: 0.15;
            --nav-hover-bg: rgba(255, 255, 255, 0.05);
            --success-bg: rgba(16, 185, 129, 0.1);
            --success-border: rgba(16, 185, 129, 0.3);
            --success-text: var(--emerald-400);
            --error-text: #fb7185;
            --check-bg: rgba(232, 162, 41, 0.08);
            --check-border: rgba(232, 162, 41, 0.15);
        }

        [data-theme="light"] {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-input: #ffffff;
            --bg-input-focus: #ffffff;
            --border-subtle: #e2e8f0;
            --border-hover: #cbd5e1;
            --border-focus: var(--amber-500);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --text-faint: #94a3b8;
            --grid-line: rgba(30, 58, 95, 0.04);
            --glow-opacity: 0.06;
            --nav-hover-bg: rgba(0, 0, 0, 0.04);
            --success-bg: rgba(16, 185, 129, 0.08);
            --success-border: rgba(16, 185, 129, 0.25);
            --success-text: #059669;
            --error-text: #e11d48;
            --check-bg: rgba(232, 162, 41, 0.06);
            --check-border: rgba(232, 162, 41, 0.12);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
            min-height: 100vh;
        }

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

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

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

        .theme-toggle .icon-sun { display: none; }
        .theme-toggle .icon-moon { display: block; }
        [data-theme="light"] .theme-toggle .icon-sun { display: block; }
        [data-theme="light"] .theme-toggle .icon-moon { display: none; }

        /* --- Page Layout --- */
        .page-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            padding: 60px 0 80px;
            align-items: start;
        }

        /* --- Left Column --- */
        .info-col h1 {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: -1.5px;
            line-height: 1.1;
            margin-bottom: 16px;
        }

        .info-col h1 .gradient {
            background: linear-gradient(135deg, var(--amber-300), var(--amber-400), var(--amber-500));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .info-col > p {
            font-size: 17px;
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 40px;
        }

        .checklist {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 40px;
        }

        .checklist li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .checklist li .check-icon {
            width: 28px;
            height: 28px;
            min-width: 28px;
            border-radius: 8px;
            background: var(--check-bg);
            border: 1px solid var(--check-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-top: 1px;
        }

        .trust-note {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 20px;
            border-radius: 12px;
            background: var(--check-bg);
            border: 1px solid var(--check-border);
            font-size: 13px;
            color: var(--text-muted);
        }

        .trust-note .shield {
            font-size: 18px;
        }

        /* --- Form Card --- */
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--amber-400), transparent);
        }

        .form-card h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .form-card > p {
            color: var(--text-muted);
            font-size: 14px;
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
            grid-column: span 2;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            letter-spacing: 0.3px;
        }

        .form-group label .required {
            color: var(--rose-500);
            margin-left: 2px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 16px;
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            background: var(--bg-input);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 14px;
            transition: all 0.2s;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--border-focus);
            background: var(--bg-input-focus);
            box-shadow: 0 0 0 3px rgba(232, 162, 41, 0.1);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--text-faint);
        }

        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        .form-group select option {
            background: var(--bg-body);
            color: var(--text-primary);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .date-time-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            grid-column: span 2;
        }

        .form-submit {
            margin-top: 24px;
            grid-column: span 2;
        }

        .form-submit .btn {
            width: 100%;
            justify-content: center;
            padding: 14px 24px;
            font-size: 15px;
        }

        /* --- Success Message --- */
        .success-message {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            margin-bottom: 20px;
        }

        .success-message .success-icon {
            font-size: 40px;
            margin-bottom: 12px;
        }

        .success-message h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--success-text);
            margin-bottom: 6px;
        }

        .success-message p {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
        }

        /* --- Errors --- */
        .error-text {
            font-size: 12px;
            color: var(--error-text);
            margin-top: 2px;
        }

        /* --- Footer --- */
        footer {
            border-top: 1px solid var(--border-subtle);
            padding: 32px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        footer small {
            color: var(--text-faint);
            font-size: 13px;
        }

        /* --- Transitions --- */
        nav, .form-card, .trust-note, footer, .theme-toggle {
            transition: background 0.3s, border-color 0.3s, color 0.3s;
        }

        /* --- Responsive --- */
        @media (max-width: 900px) {
            .page-content {
                grid-template-columns: 1fr;
                gap: 40px;
                padding: 40px 0 60px;
            }
            .info-col h1 {
                font-size: 32px;
            }
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width,
            .date-time-row,
            .form-submit {
                grid-column: span 1;
            }
            .date-time-row {
                grid-template-columns: 1fr;
            }
            .form-card {
                padding: 24px;
            }
            .nav-links .nav-link {
                display: none;
            }
            footer {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <div class="container">
        <!-- Nav -->
        <nav>
            <a href="/" class="logo">
                <img src="{{ asset('logo/infrahub-logo-new.png') }}" alt="InfraHub"
                    style="height: 44px; border-radius: 12px;">
            </a>
            <div class="nav-links">
                <a href="/" class="nav-link">← Back to Home</a>
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle light/dark mode" aria-label="Toggle theme">
                    <span class="icon-moon">🌙</span>
                    <span class="icon-sun">☀️</span>
                </button>
                @auth
                    <a href="{{ url('/app') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ url('/app/login') }}" class="nav-link">Log in</a>
                @endauth
            </div>
        </nav>

        <!-- Page Content -->
        <div class="page-content">
            <!-- Left: Info -->
            <div class="info-col">
                <h1>
                    Let's Talk About<br>
                    <span class="gradient">Your Projects</span>
                </h1>
                <p>
                    Book a free 30-minute call with our team. We'll walk you through InfraHub,
                    answer your questions, and help you find the right plan for your organization.
                </p>

                <ul class="checklist">
                    <li>
                        <span class="check-icon">✓</span>
                        <span><strong>Personalized demo</strong> — See the modules that matter to your projects</span>
                    </li>
                    <li>
                        <span class="check-icon">✓</span>
                        <span><strong>Onboarding guidance</strong> — We'll help you set up teams, roles & projects</span>
                    </li>
                    <li>
                        <span class="check-icon">✓</span>
                        <span><strong>Pricing tailored</strong> — Get a plan that fits your company size & needs</span>
                    </li>
                    <li>
                        <span class="check-icon">✓</span>
                        <span><strong>No commitment</strong> — Zero pressure, cancel anytime during your trial</span>
                    </li>
                </ul>

                <div class="trust-note">
                    <span class="shield">🔒</span>
                    <span>Your information is secure and will only be used to schedule your call.</span>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="form-card">
                @if (session('success'))
                    <div class="success-message">
                        <div class="success-icon">🎉</div>
                        <h3>Call Scheduled!</h3>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <h2>Schedule Your Call</h2>
                <p>Fill in your details and pick a time that works for you.</p>

                <form action="{{ route('schedule-call.store') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <!-- Name -->
                        <div class="form-group">
                            <label for="name">Full Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" placeholder="John Doe"
                                value="{{ old('name') }}" required>
                            @error('name') <span class="error-text">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Work Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="john@company.com"
                                value="{{ old('email') }}" required>
                            @error('email') <span class="error-text">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="+256 700 000 000"
                                value="{{ old('phone') }}">
                            @error('phone') <span class="error-text">{{ $message }}</span> @enderror
                        </div>

                        <!-- Company -->
                        <div class="form-group">
                            <label for="company">Company Name</label>
                            <input type="text" id="company" name="company" placeholder="Acme Construction Ltd"
                                value="{{ old('company') }}">
                            @error('company') <span class="error-text">{{ $message }}</span> @enderror
                        </div>

                        <!-- Job Title -->
                        <div class="form-group">
                            <label for="job_title">Job Title</label>
                            <input type="text" id="job_title" name="job_title" placeholder="Project Manager"
                                value="{{ old('job_title') }}">
                            @error('job_title') <span class="error-text">{{ $message }}</span> @enderror
                        </div>

                        <!-- Company Size -->
                        <div class="form-group">
                            <label for="company_size">Company Size</label>
                            <select id="company_size" name="company_size">
                                <option value="">Select size</option>
                                <option value="1-10" {{ old('company_size') == '1-10' ? 'selected' : '' }}>1–10 employees</option>
                                <option value="11-50" {{ old('company_size') == '11-50' ? 'selected' : '' }}>11–50 employees</option>
                                <option value="51-200" {{ old('company_size') == '51-200' ? 'selected' : '' }}>51–200 employees</option>
                                <option value="201-500" {{ old('company_size') == '201-500' ? 'selected' : '' }}>201–500 employees</option>
                                <option value="500+" {{ old('company_size') == '500+' ? 'selected' : '' }}>500+ employees</option>
                            </select>
                            @error('company_size') <span class="error-text">{{ $message }}</span> @enderror
                        </div>

                        <!-- Date & Time -->
                        <div class="date-time-row">
                            <div class="form-group">
                                <label for="preferred_date">Preferred Date <span class="required">*</span></label>
                                <input type="date" id="preferred_date" name="preferred_date"
                                    value="{{ old('preferred_date') }}" required min="{{ now()->addDay()->format('Y-m-d') }}">
                                @error('preferred_date') <span class="error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="preferred_time">Preferred Time <span class="required">*</span></label>
                                <select id="preferred_time" name="preferred_time" required>
                                    <option value="">Select time</option>
                                    <option value="09:00 AM" {{ old('preferred_time') == '09:00 AM' ? 'selected' : '' }}>09:00 AM</option>
                                    <option value="10:00 AM" {{ old('preferred_time') == '10:00 AM' ? 'selected' : '' }}>10:00 AM</option>
                                    <option value="11:00 AM" {{ old('preferred_time') == '11:00 AM' ? 'selected' : '' }}>11:00 AM</option>
                                    <option value="12:00 PM" {{ old('preferred_time') == '12:00 PM' ? 'selected' : '' }}>12:00 PM</option>
                                    <option value="01:00 PM" {{ old('preferred_time') == '01:00 PM' ? 'selected' : '' }}>01:00 PM</option>
                                    <option value="02:00 PM" {{ old('preferred_time') == '02:00 PM' ? 'selected' : '' }}>02:00 PM</option>
                                    <option value="03:00 PM" {{ old('preferred_time') == '03:00 PM' ? 'selected' : '' }}>03:00 PM</option>
                                    <option value="04:00 PM" {{ old('preferred_time') == '04:00 PM' ? 'selected' : '' }}>04:00 PM</option>
                                    <option value="05:00 PM" {{ old('preferred_time') == '05:00 PM' ? 'selected' : '' }}>05:00 PM</option>
                                </select>
                                @error('preferred_time') <span class="error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Timezone -->
                        <div class="form-group full-width">
                            <label for="timezone">Timezone <span class="required">*</span></label>
                            <select id="timezone" name="timezone" required>
                                <option value="Africa/Kampala" {{ old('timezone', 'Africa/Kampala') == 'Africa/Kampala' ? 'selected' : '' }}>East Africa Time (EAT, UTC+3)</option>
                                <option value="Africa/Nairobi" {{ old('timezone') == 'Africa/Nairobi' ? 'selected' : '' }}>Nairobi (EAT, UTC+3)</option>
                                <option value="Africa/Lagos" {{ old('timezone') == 'Africa/Lagos' ? 'selected' : '' }}>West Africa Time (WAT, UTC+1)</option>
                                <option value="Africa/Johannesburg" {{ old('timezone') == 'Africa/Johannesburg' ? 'selected' : '' }}>South Africa (SAST, UTC+2)</option>
                                <option value="Europe/London" {{ old('timezone') == 'Europe/London' ? 'selected' : '' }}>London (GMT/BST)</option>
                                <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>Eastern Time (EST/EDT)</option>
                                <option value="America/Chicago" {{ old('timezone') == 'America/Chicago' ? 'selected' : '' }}>Central Time (CST/CDT)</option>
                                <option value="Asia/Dubai" {{ old('timezone') == 'Asia/Dubai' ? 'selected' : '' }}>Gulf Time (GST, UTC+4)</option>
                            </select>
                            @error('timezone') <span class="error-text">{{ $message }}</span> @enderror
                        </div>

                        <!-- Message -->
                        <div class="form-group full-width">
                            <label for="message">What would you like to discuss?</label>
                            <textarea id="message" name="message" rows="3"
                                placeholder="Tell us about your projects, team size, or specific modules you're interested in...">{{ old('message') }}</textarea>
                            @error('message') <span class="error-text">{{ $message }}</span> @enderror
                        </div>

                        <!-- Submit -->
                        <div class="form-submit">
                            <button type="submit" class="btn btn-primary">
                                Schedule My Call
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <a href="/" class="logo" style="text-decoration:none;">
                <img src="{{ asset('logo/infrahub-logo-new.png') }}" alt="InfraHub"
                    style="height: 32px; border-radius: 8px;">
            </a>
            <small>© {{ date('Y') }} InfraHub. All rights reserved.</small>
        </footer>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('infrahub-theme', next);
        }

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
