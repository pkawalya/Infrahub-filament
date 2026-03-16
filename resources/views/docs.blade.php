<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>InfraHub v{{ config('app.version') }} — Documentation</title>
    <meta name="description"
        content="InfraHub API & Platform documentation. REST API reference, module guides, offline capabilities, and integration help.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo/infrahub-icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap"
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
            --blue-500: #3b82f6;
            --violet-500: #8b5cf6;
        }

        [data-theme="dark"] {
            --bg-body: #020617;
            --bg-card: rgba(15, 23, 42, 0.7);
            --bg-elevated: #0f172a;
            --bg-glass: rgba(15, 23, 42, 0.6);
            --bg-code: #0c1222;
            --border-subtle: #1e293b;
            --border-hover: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --text-code: #e2e8f0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            line-height: 1.7;
        }

        /* ── Grid Background ── */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(232, 162, 41, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(232, 162, 41, 0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .bg-glow {
            position: fixed;
            z-index: 0;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            opacity: 0.1;
        }

        .bg-glow-1 {
            top: -200px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: var(--amber-400);
        }

        .bg-glow-2 {
            bottom: -200px;
            left: -100px;
            width: 500px;
            height: 500px;
            background: var(--navy-600);
        }

        /* ── Layout ── */
        .docs-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            border-right: 1px solid var(--border-subtle);
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            padding: 1.5rem 0;
        }

        .sidebar::-webkit-scrollbar {
            width: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--border-subtle);
            border-radius: 3px;
        }

        .sidebar-header {
            padding: 0 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 1rem;
        }

        .sidebar-header a {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            color: var(--text-primary);
        }

        .sidebar-header .logo-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--amber-400), var(--amber-500));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 14px;
            color: var(--navy-700);
        }

        .sidebar-header h1 {
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .sidebar-header .version {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            margin-left: 0.25rem;
        }

        .sidebar-nav {
            padding: 0 0.75rem;
        }

        .sidebar-group {
            margin-bottom: 1.25rem;
        }

        .sidebar-group-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            padding: 0 0.5rem;
            margin-bottom: 0.35rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.65rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.82rem;
            font-weight: 500;
            transition: all 0.15s;
            line-height: 1.4;
        }

        .sidebar-link:hover {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.04);
        }

        .sidebar-link.active {
            color: var(--amber-400);
            background: rgba(232, 162, 41, 0.08);
            font-weight: 600;
        }

        .sidebar-link .icon {
            font-size: 1rem;
            width: 1.2rem;
            text-align: center;
            flex-shrink: 0;
        }

        /* ── Main Content ── */
        .docs-main {
            padding: 2.5rem 3rem;
            max-width: 900px;
            min-width: 0;
        }

        .docs-main h1 {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: -1px;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--amber-300), var(--amber-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .docs-main .subtitle {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            max-width: 640px;
        }

        .docs-main h2 {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin: 3rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        .docs-main h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 2rem 0 0.75rem;
            color: var(--text-primary);
        }

        .docs-main p {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .docs-main ul,
        .docs-main ol {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            padding-left: 1.5rem;
            font-size: 0.9rem;
        }

        .docs-main li {
            margin-bottom: 0.35rem;
        }

        .docs-main strong {
            color: var(--text-primary);
            font-weight: 600;
        }

        .docs-main a {
            color: var(--amber-400);
            text-decoration: none;
            font-weight: 500;
        }

        .docs-main a:hover {
            text-decoration: underline;
        }

        /* ── Code Blocks ── */
        code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            background: rgba(99, 102, 241, 0.1);
            color: #c7d2fe;
            padding: 0.15rem 0.4rem;
            border-radius: 5px;
        }

        pre {
            background: var(--bg-code);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 1.25rem;
            overflow-x: auto;
            margin-bottom: 1.5rem;
            position: relative;
        }

        pre code {
            background: none;
            padding: 0;
            color: var(--text-code);
            font-size: 0.8rem;
            line-height: 1.7;
        }

        .code-label {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.3rem 0.75rem;
            border-radius: 0 12px 0 8px;
            background: rgba(99, 102, 241, 0.12);
            color: #818cf8;
        }

        /* ── Endpoint Cards ── */
        .endpoint {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 0.75rem;
            transition: border-color 0.2s;
        }

        .endpoint:hover {
            border-color: var(--border-hover);
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.35rem;
        }

        .method {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            letter-spacing: 0.05em;
            flex-shrink: 0;
        }

        .method-get {
            background: rgba(34, 197, 94, 0.12);
            color: #4ade80;
        }

        .method-post {
            background: rgba(59, 130, 246, 0.12);
            color: #60a5fa;
        }

        .method-put {
            background: rgba(232, 162, 41, 0.12);
            color: var(--amber-400);
        }

        .method-patch {
            background: rgba(168, 85, 247, 0.12);
            color: #c084fc;
        }

        .method-delete {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
        }

        .endpoint-path {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .endpoint-desc {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-left: 3.5rem;
        }

        /* ── Info Boxes ── */
        .info-box {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .info-box .icon {
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .info-tip {
            background: rgba(34, 197, 94, 0.08);
            border: 1px solid rgba(34, 197, 94, 0.15);
            color: #a7f3d0;
        }

        .info-warn {
            background: rgba(232, 162, 41, 0.08);
            border: 1px solid rgba(232, 162, 41, 0.15);
            color: #fcd34d;
        }

        .info-note {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.15);
            color: #93c5fd;
        }

        /* ── Module Grid ── */
        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .module-item {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.2s;
            font-size: 0.85rem;
        }

        .module-item:hover {
            border-color: var(--border-hover);
            transform: translateY(-1px);
        }

        .module-item .m-icon {
            font-size: 1.1rem;
        }

        .module-item .m-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .module-item .m-desc {
            font-size: 0.72rem;
            color: var(--text-muted);
        }

        /* ── Table ── */
        .docs-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }

        .docs-table th {
            text-align: left;
            padding: 0.6rem 0.75rem;
            font-weight: 700;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            border-bottom: 2px solid var(--border-subtle);
        }

        .docs-table td {
            padding: 0.6rem 0.75rem;
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-secondary);
        }

        .docs-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .docs-table code {
            font-size: 0.78rem;
        }

        /* ── Badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .badge-auth {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
        }

        .badge-public {
            background: rgba(34, 197, 94, 0.12);
            color: #4ade80;
        }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .docs-layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
            }

            .docs-main {
                padding: 1.5rem 1rem;
            }

            .docs-main h1 {
                font-size: 1.5rem;
            }

            .docs-main h2 {
                font-size: 1.2rem;
            }

            .module-grid {
                grid-template-columns: 1fr;
            }

            .endpoint-desc {
                margin-left: 0;
                margin-top: 0.25rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <div class="docs-layout">
        <!-- ── Sidebar ── -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="/">
                    <div class="logo-icon">IH</div>
                    <h1>InfraHub <span class="version">v{{ config('app.version') }}</span></h1>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-group">
                    <div class="sidebar-group-label">Getting Started</div>
                    <a href="#overview" class="sidebar-link active"><span class="icon">📖</span> Overview</a>
                    <a href="#authentication" class="sidebar-link"><span class="icon">🔑</span> Authentication</a>
                    <a href="#quick-start" class="sidebar-link"><span class="icon">⚡</span> Quick Start</a>
                </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-label">Platform</div>
                    <a href="#modules" class="sidebar-link"><span class="icon">🧱</span> Modules</a>
                    <a href="#offline" class="sidebar-link"><span class="icon">☁️</span> Offline Mode</a>
                    <a href="#pwa" class="sidebar-link"><span class="icon">📱</span> PWA Install</a>
                    <a href="#security" class="sidebar-link"><span class="icon">🛡️</span> Security & Access</a>
                </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-label">API Reference</div>
                    <a href="#api-projects" class="sidebar-link"><span class="icon">📁</span> Projects</a>
                    <a href="#api-tasks" class="sidebar-link"><span class="icon">✅</span> Tasks</a>
                    <a href="#api-documents" class="sidebar-link"><span class="icon">📄</span> Documents</a>
                    <a href="#api-safety" class="sidebar-link"><span class="icon">⚠️</span> Safety</a>
                    <a href="#api-attendance" class="sidebar-link"><span class="icon">👷</span> Attendance</a>
                    <a href="#api-work-orders" class="sidebar-link"><span class="icon">🔧</span> Work Orders</a>
                    <a href="#api-rfis" class="sidebar-link"><span class="icon">❓</span> RFIs</a>
                    <a href="#api-submittals" class="sidebar-link"><span class="icon">📋</span> Submittals</a>
                    <a href="#api-equipment" class="sidebar-link"><span class="icon">🚜</span> Equipment</a>
                    <a href="#api-offline-sync" class="sidebar-link"><span class="icon">🔄</span> Offline Sync</a>
                </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-label">Reference</div>
                    <a href="#errors" class="sidebar-link"><span class="icon">🚫</span> Error Codes</a>
                    <a href="#rate-limits" class="sidebar-link"><span class="icon">⏱️</span> Rate Limits</a>
                    <a href="#health" class="sidebar-link"><span class="icon">💚</span> Health Check</a>
                </div>
            </nav>
        </aside>

        <!-- ── Main Content ── -->
        <main class="docs-main">
            <!-- Overview -->
            <section id="overview">
                <h1>InfraHub Documentation</h1>
                <p class="subtitle">
                    Complete reference for the InfraHub construction management platform.
                    REST API v{{ config('app.api_version') }} · App v{{ config('app.version') }} · Laravel
                    {{ app()->version() }}
                </p>

                <div class="info-box info-note">
                    <span class="icon">ℹ️</span>
                    <div>
                        <strong>Base URL:</strong> <code>{{ config('app.url') }}/api/v1</code><br>
                        All API endpoints require authentication via <strong>Bearer token</strong> (Laravel Sanctum)
                        unless marked as public.
                    </div>
                </div>
            </section>

            <!-- Authentication -->
            <section id="authentication">
                <h2>🔑 Authentication</h2>
                <p>InfraHub uses <strong>Laravel Sanctum</strong> for API authentication. Obtain a token via the login
                    endpoint, then include it in subsequent requests.</p>

                <h3>Login</h3>
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method method-post">POST</span>
                        <span class="endpoint-path">/api/v1/auth/login</span>
                        <span class="badge badge-public">PUBLIC</span>
                    </div>
                    <div class="endpoint-desc">Authenticate and receive a Bearer token</div>
                </div>

                <pre><code><span class="code-label">Request</span>
{
    "email": "user@company.com",
    "password": "your-password"
}</code></pre>

                <pre><code><span class="code-label">Response 200</span>
{
    "token": "1|abc123def456...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@company.com",
        "company_id": 1
    }
}</code></pre>

                <h3>Using the Token</h3>
                <p>Include the token in the <code>Authorization</code> header of all subsequent requests:</p>
                <pre><code>Authorization: Bearer 1|abc123def456...</code></pre>

                <h3>Other Auth Endpoints</h3>
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method method-post">POST</span>
                        <span class="endpoint-path">/api/v1/auth/register</span>
                        <span class="badge badge-public">PUBLIC</span>
                    </div>
                    <div class="endpoint-desc">Register a new user account</div>
                </div>
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method method-post">POST</span>
                        <span class="endpoint-path">/api/v1/auth/logout</span>
                        <span class="badge badge-auth">AUTH</span>
                    </div>
                    <div class="endpoint-desc">Revoke the current token</div>
                </div>
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method method-get">GET</span>
                        <span class="endpoint-path">/api/v1/auth/me</span>
                        <span class="badge badge-auth">AUTH</span>
                    </div>
                    <div class="endpoint-desc">Get authenticated user profile</div>
                </div>
            </section>

            <!-- Quick Start -->
            <section id="quick-start">
                <h2>⚡ Quick Start</h2>
                <p>Get started in 3 steps:</p>

                <h3>1. Get a Token</h3>
                <pre><code>curl -X POST {{ config('app.url') }}/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"you@company.com","password":"secret"}'</code></pre>

                <h3>2. List Your Projects</h3>
                <pre><code>curl {{ config('app.url') }}/api/v1/projects \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"</code></pre>

                <h3>3. Create a Task</h3>
                <pre><code>curl -X POST {{ config('app.url') }}/api/v1/projects/1/tasks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"title":"Pour foundation","priority":"high","due_date":"2026-04-01"}'</code></pre>
            </section>

            <!-- Modules -->
            <section id="modules">
                <h2>🧱 Platform Modules</h2>
                <p>InfraHub is a modular platform. Each company subscribes to the modules they need.</p>

                <div class="module-grid">
                    <div class="module-item"><span class="m-icon">📁</span>
                        <div>
                            <div class="m-name">CDE (ISO 19650)</div>
                            <div class="m-desc">Document management & version control</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">📊</span>
                        <div>
                            <div class="m-name">BOQ & Cost</div>
                            <div class="m-desc">Bills of quantities, cost tracking</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">✅</span>
                        <div>
                            <div class="m-name">Tasks & Workflow</div>
                            <div class="m-desc">Task assignment, dependencies, Kanban</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">📐</span>
                        <div>
                            <div class="m-name">Planning & Progress</div>
                            <div class="m-desc">Gantt charts, EVM, milestones</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">⚠️</span>
                        <div>
                            <div class="m-name">SHEQ</div>
                            <div class="m-desc">Safety incidents, inspections, toolbox talks</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">👷</span>
                        <div>
                            <div class="m-name">Field Management</div>
                            <div class="m-desc">Site diaries, attendance, daily logs</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">🚜</span>
                        <div>
                            <div class="m-name">Equipment</div>
                            <div class="m-desc">Allocations, fuel logs, maintenance</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">💰</span>
                        <div>
                            <div class="m-name">Financials</div>
                            <div class="m-desc">Invoices, payment certificates, expenses</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">📦</span>
                        <div>
                            <div class="m-name">Inventory</div>
                            <div class="m-desc">Stock, requisitions, POs, GRNs</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">🏗️</span>
                        <div>
                            <div class="m-name">Contracts</div>
                            <div class="m-desc">Subcontractors, change orders, claims</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">❓</span>
                        <div>
                            <div class="m-name">RFIs & Submittals</div>
                            <div class="m-desc">Requests for information, shop drawings</div>
                        </div>
                    </div>
                    <div class="module-item"><span class="m-icon">📈</span>
                        <div>
                            <div class="m-name">Reporting</div>
                            <div class="m-desc">Dashboards, analytics, exports</div>
                        </div>
                    </div>
                </div>
    </div>
    </section>

    <!-- Security & Access Control -->
    <section id="security">
        <h2>🛡️ Security & Access Control</h2>
        <p>InfraHub incorporates enterprise-grade security hardening natively, limiting exposure and validating inputs
            effectively.</p>

        <h3>Geographic Access & IP Blocking</h3>
        <p>The platform bounds access to safe operating regions:</p>
        <ul>
            <li><strong>Geo-Restriction:</strong> Allowed countries can be managed in the Admin panel. Requests from
                restricted regions receive an HTTP 403 Forbidden. Caching optimizes lookup performance.</li>
            <li><strong>IP Blocking:</strong> Specific threatening IPs or CIDR boundaries (e.g. <code>10.0.0.0/8</code>)
                can be dynamically blocked with expiration controls.</li>
            <li><strong>API Overrides:</strong> Select external APIs (e.g. Webhooks) safely bypass the geo-middleware
                but remain strictly limited to whitelisted supplier IPs.</li>
        </ul>

        <h3>User Session Hardening</h3>
        <ul>
            <li><strong>Enforced 2FA:</strong> Two-Factor Authentication (Email OTP) can be globally triggered for all
                internal and client users, enforcing multi-factor checks on subsequent logins.</li>
            <li><strong>Session Management:</strong> Active login sessions across all devices are trackable. Users can
                invalidate dormant or suspicious concurrent instances remotely from their settings panel.</li>
            <li><strong>Destructive Action Limits:</strong> Operations like Project termination require
                re-authentication of the user's password prior to final deletion.</li>
        </ul>

        <h3>Common Data Environment (CDE)</h3>
        <ul>
            <li><strong>Malware Scanning (ClamAV):</strong> When enabled, every uploaded BIM, CAD, or PDF file passes
                through dynamic daemon scanning before persistence.</li>
            <li><strong>Aggressive File Validation:</strong> Absolute blockage of executable vectors (<code>.exe</code>,
                <code>.php</code>, <code>.bat</code>) and double extensions.</li>
        </ul>
    </section>

    <!-- Offline -->
    <section id="offline">
        <h2>☁️ Offline Mode</h2>
        <p>InfraHub works offline for field workers with poor connectivity. Every page you visit is cached.</p>

        <div class="info-box info-tip">
            <span class="icon">💡</span>
            <div>
                <strong>Keyboard shortcut:</strong> Press <code>Ctrl+Shift+S</code> on any Create or Edit form
                page to save the data offline. It will sync automatically when you reconnect.
            </div>
        </div>

        <h3>How It Works</h3>
        <ul>
            <li><strong>Service Worker</strong> caches all visited pages (network-first with 8s timeout)</li>
            <li><strong>IndexedDB</strong> stores form submissions locally when offline</li>
            <li><strong>Background Sync API</strong> replays queued data when connectivity returns</li>
            <li><strong>Livewire Interceptor</strong> captures form data from any Filament page</li>
        </ul>

        <h3>Supported Offline Operations</h3>
        <ul>
            <li>✅ View any previously visited page</li>
            <li>✅ Create/edit records for all 15 resource types</li>
            <li>✅ Dedicated forms: Site Diary, Crew Attendance, Safety Incident</li>
            <li>✅ Auto-sync when back online</li>
            <li>✅ Manual sync via the Offline Forms page</li>
            <li>⚠️ File uploads require connectivity</li>
        </ul>

        <h3>Offline Sync API</h3>
        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method method-post">POST</span>
                <span class="endpoint-path">/api/v1/offline-sync/generic</span>
                <span class="badge badge-auth">AUTH</span>
            </div>
            <div class="endpoint-desc">Sync any resource type from offline queue</div>
        </div>
        <pre><code><span class="code-label">Request</span>
{
    "resource": "tasks",
    "action": "create",
    "record_id": null,
    "data": {
        "title": "Pour foundation Level 3",
        "priority": "high",
        "cde_project_id": 25
    }
}</code></pre>

        <h3>Supported Resource Types</h3>
        <table class="docs-table">
            <thead>
                <tr>
                    <th>Slug</th>
                    <th>Model</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>tasks</code></td>
                    <td>Task</td>
                    <td>Project tasks</td>
                </tr>
                <tr>
                    <td><code>work-orders</code></td>
                    <td>WorkOrder</td>
                    <td>Work instructions</td>
                </tr>
                <tr>
                    <td><code>daily-site-diaries</code></td>
                    <td>DailySiteDiary</td>
                    <td>Daily site records</td>
                </tr>
                <tr>
                    <td><code>crew-attendances</code></td>
                    <td>CrewAttendance</td>
                    <td>Worker attendance</td>
                </tr>
                <tr>
                    <td><code>safety-incidents</code></td>
                    <td>SafetyIncident</td>
                    <td>Safety reports</td>
                </tr>
                <tr>
                    <td><code>invoices</code></td>
                    <td>Invoice</td>
                    <td>Financial invoices</td>
                </tr>
                <tr>
                    <td><code>assets</code></td>
                    <td>Asset</td>
                    <td>Company assets</td>
                </tr>
                <tr>
                    <td><code>clients</code></td>
                    <td>Client</td>
                    <td>Client records</td>
                </tr>
                <tr>
                    <td><code>subcontractors</code></td>
                    <td>Subcontractor</td>
                    <td>Subcontractor firms</td>
                </tr>
                <tr>
                    <td><code>tenders</code></td>
                    <td>Tender</td>
                    <td>Bid/tender submissions</td>
                </tr>
                <tr>
                    <td><code>drawings</code></td>
                    <td>Drawing</td>
                    <td>Engineering drawings</td>
                </tr>
                <tr>
                    <td><code>payment-certificates</code></td>
                    <td>PaymentCertificate</td>
                    <td>Payment certs</td>
                </tr>
                <tr>
                    <td><code>cde-projects</code></td>
                    <td>CdeProject</td>
                    <td>Projects</td>
                </tr>
                <tr>
                    <td><code>change-orders</code></td>
                    <td>ChangeOrder</td>
                    <td>Contract variations</td>
                </tr>
                <tr>
                    <td><code>snag-items</code></td>
                    <td>SnagItem</td>
                    <td>Defect/snag list</td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- PWA -->
    <section id="pwa">
        <h2>📱 PWA Installation</h2>
        <p>InfraHub is a Progressive Web App. Install it on your device for the best experience.</p>
        <ol>
            <li>Open <code>{{ config('app.url') }}/app</code> in Chrome or Edge</li>
            <li>Click the <strong>Install</strong> icon in the address bar (or ⋮ → Install App)</li>
            <li>The app launches in its own window with offline support</li>
        </ol>
    </section>

    <!-- API: Projects -->
    <section id="api-projects">
        <h2>📁 Projects API</h2>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/projects</span></div>
            <div class="endpoint-desc">List all projects for the authenticated company</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects</span></div>
            <div class="endpoint-desc">Create a new project</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/projects/{id}</span></div>
            <div class="endpoint-desc">Get project details with stats</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-put">PUT</span><span
                    class="endpoint-path">/api/v1/projects/{id}</span></div>
            <div class="endpoint-desc">Update a project</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-delete">DELETE</span><span
                    class="endpoint-path">/api/v1/projects/{id}</span></div>
            <div class="endpoint-desc">Delete a project</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/projects/{id}/stats</span></div>
            <div class="endpoint-desc">Get project statistics (tasks, documents, etc.)</div>
        </div>
    </section>

    <!-- API: Tasks -->
    <section id="api-tasks">
        <h2>✅ Tasks API</h2>
        <p>Tasks are scoped under a project.</p>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/projects/{project}/tasks</span></div>
            <div class="endpoint-desc">List all tasks for a project</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/tasks</span></div>
            <div class="endpoint-desc">Create a new task</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/projects/{project}/tasks/{task}</span></div>
            <div class="endpoint-desc">Get task details</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-put">PUT</span><span
                    class="endpoint-path">/api/v1/projects/{project}/tasks/{task}</span></div>
            <div class="endpoint-desc">Update a task</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-patch">PATCH</span><span
                    class="endpoint-path">/api/v1/projects/{project}/tasks/{task}/progress</span></div>
            <div class="endpoint-desc">Update task progress percentage</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-delete">DELETE</span><span
                    class="endpoint-path">/api/v1/projects/{project}/tasks/{task}</span></div>
            <div class="endpoint-desc">Delete a task</div>
        </div>
    </section>

    <!-- API: Documents -->
    <section id="api-documents">
        <h2>📄 Documents API</h2>
        <p>ISO 19650 compliant document management with versioning and approval workflows.</p>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/projects/{project}/documents</span></div>
            <div class="endpoint-desc">List all documents in a project</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/documents</span></div>
            <div class="endpoint-desc">Upload a new document</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/documents/{doc}/submit-for-review</span>
            </div>
            <div class="endpoint-desc">Submit document for review</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/documents/{doc}/approve</span></div>
            <div class="endpoint-desc">Approve a document</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/documents/{doc}/reject</span></div>
            <div class="endpoint-desc">Reject a document</div>
        </div>
    </section>

    <!-- API: Safety -->
    <section id="api-safety">
        <h2>⚠️ Safety Incidents API</h2>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/safety-incidents</span></div>
            <div class="endpoint-desc">List all safety incidents for the company</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/safety-incidents</span></div>
            <div class="endpoint-desc">Report a new safety incident</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/safety-incidents/{id}</span></div>
            <div class="endpoint-desc">Get incident details</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-put">PUT</span><span
                    class="endpoint-path">/api/v1/safety-incidents/{id}</span></div>
            <div class="endpoint-desc">Update incident status / investigation</div>
        </div>
    </section>

    <!-- API: Attendance -->
    <section id="api-attendance">
        <h2>👷 Crew Attendance API</h2>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/attendance</span></div>
            <div class="endpoint-desc">List attendance records</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/attendance</span></div>
            <div class="endpoint-desc">Record worker attendance</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/attendance/today</span></div>
            <div class="endpoint-desc">Get today's attendance summary</div>
        </div>
    </section>

    <!-- API: Work Orders -->
    <section id="api-work-orders">
        <h2>🔧 Work Orders API</h2>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/projects/{project}/work-orders</span></div>
            <div class="endpoint-desc">List work orders for a project</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/work-orders</span></div>
            <div class="endpoint-desc">Create a work order</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-put">PUT</span><span
                    class="endpoint-path">/api/v1/projects/{project}/work-orders/{wo}</span></div>
            <div class="endpoint-desc">Update a work order</div>
        </div>
    </section>

    <!-- API: RFIs -->
    <section id="api-rfis">
        <h2>❓ RFIs API</h2>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/projects/{project}/rfis</span></div>
            <div class="endpoint-desc">List requests for information</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/rfis</span></div>
            <div class="endpoint-desc">Create an RFI</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/rfis/{rfi}/answer</span></div>
            <div class="endpoint-desc">Answer an RFI</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/rfis/{rfi}/close</span></div>
            <div class="endpoint-desc">Close an RFI</div>
        </div>
    </section>

    <!-- API: Submittals -->
    <section id="api-submittals">
        <h2>📋 Submittals API</h2>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/projects/{project}/submittals</span></div>
            <div class="endpoint-desc">List submittals</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/submittals</span></div>
            <div class="endpoint-desc">Create a submittal</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/projects/{project}/submittals/{sub}/review</span></div>
            <div class="endpoint-desc">Review a submittal (approve/reject)</div>
        </div>
    </section>

    <!-- API: Equipment -->
    <section id="api-equipment">
        <h2>🚜 Equipment API</h2>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/equipment/allocations</span></div>
            <div class="endpoint-desc">List equipment allocations</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/equipment/allocations</span></div>
            <div class="endpoint-desc">Allocate equipment to a project</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/equipment/fuel-logs</span></div>
            <div class="endpoint-desc">List fuel consumption logs</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/equipment/fuel-logs</span></div>
            <div class="endpoint-desc">Record a fuel log entry</div>
        </div>
    </section>

    <!-- API: Offline Sync -->
    <section id="api-offline-sync">
        <h2>🔄 Offline Sync API</h2>
        <p>Endpoints used by the offline queue engine. Typically called automatically by the Service Worker.</p>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/offline-sync/generic</span></div>
            <div class="endpoint-desc">Sync any whitelisted resource (see table above)</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/offline-sync/site-diaries</span></div>
            <div class="endpoint-desc">Sync a site diary (with deduplication)</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/offline-sync/attendance</span></div>
            <div class="endpoint-desc">Sync an attendance record (with deduplication)</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-post">POST</span><span
                    class="endpoint-path">/api/v1/offline-sync/safety-incidents</span></div>
            <div class="endpoint-desc">Sync a safety incident report</div>
        </div>
        <div class="endpoint">
            <div class="endpoint-header"><span class="method method-get">GET</span><span
                    class="endpoint-path">/api/v1/offline-sync/workers</span></div>
            <div class="endpoint-desc">Get worker list for offline dropdown caching</div>
        </div>
    </section>

    <!-- Errors -->
    <section id="errors">
        <h2>🚫 Error Codes</h2>
        <table class="docs-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Meaning</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>200</code></td>
                    <td>OK</td>
                    <td>Request succeeded</td>
                </tr>
                <tr>
                    <td><code>201</code></td>
                    <td>Created</td>
                    <td>Resource created successfully</td>
                </tr>
                <tr>
                    <td><code>401</code></td>
                    <td>Unauthenticated</td>
                    <td>Missing or invalid token</td>
                </tr>
                <tr>
                    <td><code>403</code></td>
                    <td>Forbidden</td>
                    <td>Insufficient permissions or module access</td>
                </tr>
                <tr>
                    <td><code>404</code></td>
                    <td>Not Found</td>
                    <td>Resource does not exist or is not in your company</td>
                </tr>
                <tr>
                    <td><code>422</code></td>
                    <td>Validation Error</td>
                    <td>Request body failed validation. Check <code>errors</code> field.</td>
                </tr>
                <tr>
                    <td><code>429</code></td>
                    <td>Rate Limited</td>
                    <td>Too many requests. Wait and retry.</td>
                </tr>
                <tr>
                    <td><code>500</code></td>
                    <td>Server Error</td>
                    <td>Unexpected error. Contact support.</td>
                </tr>
            </tbody>
        </table>
        <pre><code><span class="code-label">Error Response</span>
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "title": ["The title field is required."],
        "due_date": ["The due date must be a date after today."]
    }
}</code></pre>
    </section>

    <!-- Rate Limits -->
    <section id="rate-limits">
        <h2>⏱️ Rate Limits</h2>
        <table class="docs-table">
            <thead>
                <tr>
                    <th>Endpoint Group</th>
                    <th>Limit</th>
                    <th>Window</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Login</td>
                    <td>5 requests</td>
                    <td>1 minute</td>
                </tr>
                <tr>
                    <td>Registration</td>
                    <td>3 requests</td>
                    <td>1 minute</td>
                </tr>
                <tr>
                    <td>Health Check</td>
                    <td>30 requests</td>
                    <td>1 minute</td>
                </tr>
                <tr>
                    <td>All Authenticated APIs</td>
                    <td>60 requests</td>
                    <td>1 minute</td>
                </tr>
            </tbody>
        </table>
        <div class="info-box info-warn">
            <span class="icon">⚡</span>
            <div>When rate limited, the API returns <code>429 Too Many Requests</code>. Include retry logic in
                your integration with exponential backoff.</div>
        </div>
    </section>

    <!-- Health -->
    <section id="health">
        <h2>💚 Health Check</h2>
        <div class="endpoint">
            <div class="endpoint-header">
                <span class="method method-get">GET</span>
                <span class="endpoint-path">/api/health</span>
                <span class="badge badge-public">PUBLIC</span>
            </div>
            <div class="endpoint-desc">Infrastructure health check — no authentication required</div>
        </div>
        <pre><code><span class="code-label">Response 200</span>
{
    "status": "healthy",
    "version": "{{ config('app.version') }}",
    "api_version": "{{ config('app.api_version') }}",
    "timestamp": "2026-03-14T10:30:00.000000Z",
    "checks": {
        "database": { "status": "ok", "response_ms": 1.2 },
        "cache": { "status": "ok", "driver": "redis" },
        "queue": { "status": "ok", "pending_jobs": 0, "failed_jobs": 0 },
        "storage": { "status": "ok" },
        "system": {
            "php": "8.4.x",
            "laravel": "12.x",
            "app_version": "{{ config('app.version') }}",
            "api_version": "{{ config('app.api_version') }}"
        }
    }
}</code></pre>
    </section>

    <!-- Footer -->
    <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--border-subtle); text-align: center;">
        <p style="font-size: 0.78rem; color: var(--text-muted);">
            InfraHub v{{ config('app.version') }} · API {{ config('app.api_version') }} · Built with Laravel
            {{ app()->version() }} & Filament<br>
            <a href="/" style="color: var(--amber-400);">← Back to Home</a> · <a href="/app"
                style="color: var(--amber-400);">Open App →</a>
        </p>
    </div>
    </main>
    </div>

    <script>
        // Active sidebar link highlighting on scroll
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.sidebar-link');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    navLinks.forEach(link => link.classList.remove('active'));
                    const activeLink = document.querySelector(`.sidebar-link[href="#${entry.target.id}"]`);
                    if (activeLink) activeLink.classList.add('active');
                }
            });
        }, { rootMargin: '-20% 0px -75% 0px' });

        sections.forEach(section => observer.observe(section));
    </script>
</body>

</html>