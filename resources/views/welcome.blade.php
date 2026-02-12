<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --slate-950: #020617;
            --sky-400: #38bdf8;
            --sky-500: #0ea5e9;
            --sky-600: #0284c7;
            --blue-500: #3b82f6;
            --blue-600: #2563eb;
            --indigo-500: #6366f1;
            --indigo-600: #4f46e5;
            --emerald-400: #34d399;
            --emerald-500: #10b981;
            --amber-400: #fbbf24;
            --amber-500: #f59e0b;
            --rose-500: #f43f5e;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--slate-950);
            color: #fff;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* --- Background --- */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(56, 189, 248, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(56, 189, 248, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .bg-glow {
            position: fixed;
            z-index: 0;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            pointer-events: none;
        }

        .bg-glow-1 {
            top: -200px;
            right: -100px;
            background: var(--sky-500);
        }

        .bg-glow-2 {
            bottom: -200px;
            left: -100px;
            background: var(--indigo-500);
        }

        .bg-glow-3 {
            top: 40%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--emerald-500);
            opacity: 0.08;
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

        .logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--sky-500), var(--indigo-500));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.3);
        }

        .logo-text {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .logo-text span {
            color: var(--sky-400);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            color: var(--slate-400);
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
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
            background: linear-gradient(135deg, var(--sky-500), var(--blue-600));
            color: #fff;
            box-shadow: 0 4px 20px rgba(14, 165, 233, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(14, 165, 233, 0.4);
        }

        .btn-ghost {
            color: var(--slate-300);
            border: 1px solid var(--slate-700);
            background: rgba(255, 255, 255, 0.03);
        }

        .btn-ghost:hover {
            border-color: var(--sky-500);
            color: #fff;
            background: rgba(56, 189, 248, 0.05);
        }

        .btn-lg {
            padding: 16px 32px;
            font-size: 16px;
            border-radius: 14px;
        }

        /* --- Hero --- */
        .hero {
            padding: 80px 0 60px;
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.2);
            padding: 8px 18px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            color: var(--sky-400);
            margin-bottom: 28px;
        }

        .hero-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--emerald-400);
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

        .hero h1 {
            font-size: clamp(40px, 6vw, 72px);
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: -2px;
            margin-bottom: 24px;
        }

        .hero h1 .gradient {
            background: linear-gradient(135deg, var(--sky-400) 0%, var(--blue-500) 40%, var(--indigo-500) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 18px;
            color: var(--slate-400);
            max-width: 640px;
            margin: 0 auto 40px;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* --- Stats Bar --- */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--slate-800);
            border-radius: 16px;
            overflow: hidden;
            margin: 80px 0;
            border: 1px solid var(--slate-800);
        }

        .stat-item {
            background: var(--slate-900);
            padding: 32px;
            text-align: center;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, var(--sky-400), var(--emerald-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 13px;
            color: var(--slate-500);
            margin-top: 4px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
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
            color: var(--slate-400);
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
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--slate-800);
            border-radius: 16px;
            padding: 32px;
            transition: all 0.3s;
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
            background: linear-gradient(90deg, transparent, var(--sky-500), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .module-card:hover {
            border-color: var(--slate-700);
            background: rgba(255, 255, 255, 0.04);
            transform: translateY(-4px);
        }

        .module-card:hover::before {
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
        }

        .module-card h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .module-card p {
            font-size: 14px;
            color: var(--slate-400);
            line-height: 1.6;
        }

        /* --- CTA --- */
        .cta {
            text-align: center;
            padding: 80px 40px;
            margin-bottom: 80px;
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.05), rgba(99, 102, 241, 0.05));
            border: 1px solid var(--slate-800);
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
            background: linear-gradient(90deg, transparent, var(--sky-500), transparent);
        }

        .cta h2 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .cta p {
            color: var(--slate-400);
            margin-bottom: 32px;
            font-size: 16px;
        }

        /* --- Footer --- */
        footer {
            border-top: 1px solid var(--slate-800);
            padding: 32px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        footer small {
            color: var(--slate-600);
            font-size: 13px;
        }

        /* --- Responsive --- */
        @media (max-width: 768px) {
            .stats-bar {
                grid-template-columns: repeat(2, 1fr);
            }

            .modules-grid {
                grid-template-columns: 1fr;
            }

            .hero {
                padding: 48px 0 40px;
            }

            footer {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }

            .nav-links .nav-link {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    <div class="bg-glow bg-glow-3"></div>

    <div class="container">
        <!-- Nav -->
        <nav>
            <a href="/" class="logo">
                <img src="{{ asset('logo/infrahub-logo-dark.svg') }}" alt="InfraHub" style="height: 36px;">
            </a>

            @if (Route::has('login'))
                <div class="nav-links">
                    @auth
                        <a href="{{ url('/app') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ url('/app/login') }}" class="nav-link">Log in</a>
                        <a href="{{ url('/app/login') }}" class="btn btn-primary">Get Started</a>
                    @endauth
                </div>
            @endif
        </nav>

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
                <a href="/app/login" class="btn btn-primary btn-lg">
                    Launch Platform
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

        <!-- Stats -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number">10+</div>
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

        <!-- Modules -->
        <section id="modules">
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

        <!-- CTA -->
        <div class="cta">
            <h2>Ready to Build Better?</h2>
            <p>Join construction teams who trust InfraHub to deliver projects on time, on budget, and safely.</p>
            <a href="/app/login" class="btn btn-primary btn-lg">
                Start Managing Projects
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </div>

        <!-- Footer -->
        <footer>
            <a href="/" class="logo" style="text-decoration:none;">
                <img src="{{ asset('logo/infrahub-logo-dark.svg') }}" alt="InfraHub" style="height: 28px;">
            </a>
            <small>© {{ date('Y') }} InfraHub. Built with Laravel & Filament.</small>
        </footer>
    </div>
</body>

</html>