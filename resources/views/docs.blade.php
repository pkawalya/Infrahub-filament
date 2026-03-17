<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>InfraHub — Help Center</title>
    <meta name="description"
        content="InfraHub Help Center. Learn how to manage construction projects, track tasks, handle documents, and collaborate with your team.">
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
            --blue-500: #3b82f6;
            --violet-500: #8b5cf6;
        }

        [data-theme="dark"] {
            --bg-body: #020617;
            --bg-card: rgba(15, 23, 42, 0.7);
            --bg-elevated: #0f172a;
            --bg-glass: rgba(15, 23, 42, 0.6);
            --border-subtle: #1e293b;
            --border-hover: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
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

        /* ── Background Effects ── */
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

        /* ── Mobile Menu Toggle ── */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 100;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--amber-400), var(--amber-500));
            color: var(--navy-700);
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(232, 162, 41, 0.3);
            transition: transform 0.2s;
        }

        .mobile-menu-btn:active {
            transform: scale(0.95);
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
            font-size: 1.05rem;
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
            margin-bottom: 0.5rem;
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

        /* ── Info Boxes ── */
        .info-box {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.88rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .info-box .box-icon {
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

        /* ── Feature Cards ── */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 1.25rem;
            transition: all 0.25s;
        }

        .feature-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        .feature-card .fc-icon {
            font-size: 1.5rem;
            margin-bottom: 0.6rem;
        }

        .feature-card .fc-title {
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.35rem;
            color: var(--text-primary);
        }

        .feature-card .fc-desc {
            font-size: 0.82rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* ── Steps ── */
        .step-list {
            list-style: none;
            padding-left: 0;
            counter-reset: steps;
        }

        .step-list li {
            counter-increment: steps;
            display: flex;
            gap: 1rem;
            margin-bottom: 1.25rem;
            align-items: flex-start;
        }

        .step-list li::before {
            content: counter(steps);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--amber-400), var(--amber-500));
            color: var(--navy-700);
            font-weight: 800;
            font-size: 0.82rem;
        }

        .step-content {
            padding-top: 0.25rem;
        }

        .step-content strong {
            display: block;
            margin-bottom: 0.2rem;
        }

        .step-content span {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* ── FAQ ── */
        .faq-item {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .faq-item:hover {
            border-color: var(--border-hover);
        }

        .faq-q {
            padding: 1rem 1.25rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            font-size: 0.9rem;
            user-select: none;
        }

        .faq-q .arrow {
            transition: transform 0.2s;
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .faq-item.open .faq-q .arrow {
            transform: rotate(180deg);
        }

        .faq-a {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-item.open .faq-a {
            max-height: 500px;
        }

        .faq-a-inner {
            padding: 0 1.25rem 1rem;
            font-size: 0.85rem;
            color: var(--text-secondary);
            border-top: 1px solid var(--border-subtle);
            padding-top: 1rem;
        }

        /* ── Search ── */
        .search-box {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border-radius: 12px;
            border: 1px solid var(--border-subtle);
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-box input:focus {
            border-color: var(--amber-400);
        }

        .search-box input::placeholder {
            color: var(--text-muted);
        }

        .search-box .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
            pointer-events: none;
        }

        /* ── Contact Card ── */
        .contact-card {
            background: linear-gradient(135deg, rgba(232, 162, 41, 0.08), rgba(30, 58, 95, 0.15));
            border: 1px solid rgba(232, 162, 41, 0.2);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            margin: 2rem 0;
        }

        .contact-card h3 {
            margin-bottom: 0.5rem !important;
            margin-top: 0 !important;
        }

        .contact-card p {
            margin-bottom: 1.5rem;
        }

        .contact-card .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--amber-400), var(--amber-500));
            color: var(--navy-700);
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .contact-card .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(232, 162, 41, 0.25);
            text-decoration: none;
        }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .docs-layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
                position: fixed;
                inset: 0;
                z-index: 50;
                width: 100%;
                height: 100vh;
                background: var(--bg-elevated);
            }

            .sidebar.open {
                display: block;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .docs-main {
                padding: 1.5rem 1.25rem;
            }

            .docs-main h1 {
                font-size: 1.5rem;
            }

            .docs-main h2 {
                font-size: 1.2rem;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <button class="mobile-menu-btn" onclick="document.querySelector('.sidebar').classList.toggle('open')"
        aria-label="Toggle menu">☰</button>

    <div class="docs-layout">
        <!-- ── Sidebar ── -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="/">
                    <div class="logo-icon">IH</div>
                    <h1>Help Center</h1>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-group">
                    <div class="sidebar-group-label">Getting Started</div>
                    <a href="#welcome" class="sidebar-link active"><span class="icon">👋</span> Welcome</a>
                    <a href="#first-steps" class="sidebar-link"><span class="icon">🚀</span> First Steps</a>
                    <a href="#navigation" class="sidebar-link"><span class="icon">🧭</span> Navigating the App</a>
                </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-label">Features</div>
                    <a href="#projects" class="sidebar-link"><span class="icon">📁</span> Projects</a>
                    <a href="#documents" class="sidebar-link"><span class="icon">📄</span> Documents</a>
                    <a href="#tasks" class="sidebar-link"><span class="icon">✅</span> Tasks</a>
                    <a href="#safety" class="sidebar-link"><span class="icon">⚠️</span> Safety & SHEQ</a>
                    <a href="#field" class="sidebar-link"><span class="icon">👷</span> Field Management</a>
                    <a href="#equipment" class="sidebar-link"><span class="icon">🚜</span> Equipment</a>
                    <a href="#financials" class="sidebar-link"><span class="icon">💰</span> Financials</a>
                    <a href="#inventory" class="sidebar-link"><span class="icon">📦</span> Inventory</a>
                    <a href="#contracts" class="sidebar-link"><span class="icon">🏗️</span> Contracts</a>
                </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-label">Using the App</div>
                    <a href="#offline" class="sidebar-link"><span class="icon">☁️</span> Working Offline</a>
                    <a href="#pwa" class="sidebar-link"><span class="icon">📱</span> Install on Phone</a>
                    <a href="#security" class="sidebar-link"><span class="icon">🔒</span> Account & Security</a>
                </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-label">Help</div>
                    <a href="#faq" class="sidebar-link"><span class="icon">❓</span> FAQ</a>
                    <a href="#contact" class="sidebar-link"><span class="icon">💬</span> Contact Support</a>
                </div>
            </nav>
        </aside>

        <!-- ── Main Content ── -->
        <main class="docs-main">
            <!-- Welcome -->
            <section id="welcome">
                <h1>Welcome to InfraHub</h1>
                <p class="subtitle">
                    Everything you need to know to manage your construction projects, teams, and documents — all in one
                    place.
                </p>

                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="helpSearch" placeholder="Search help topics..." autocomplete="off">
                </div>

                <div class="info-box info-note">
                    <span class="box-icon">💡</span>
                    <div>
                        <strong>New here?</strong> We recommend starting with <a href="#first-steps">First Steps</a> to
                        set up your account and create your first project.
                    </div>
                </div>
            </section>

            <!-- First Steps -->
            <section id="first-steps">
                <h2>🚀 First Steps</h2>
                <p>Get up and running in just a few minutes. Here's what you need to do after your account is created:
                </p>

                <ol class="step-list">
                    <li>
                        <div class="step-content">
                            <strong>Log in to your account</strong>
                            <span>Open the link from your invitation email and sign in with the credentials provided.
                                You'll be asked to change your password on first login.</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-content">
                            <strong>Set up your company profile</strong>
                            <span>Add your company logo, address, and currency preferences from <strong>Settings →
                                    Company</strong>. This information appears on invoices and reports.</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-content">
                            <strong>Create your first project</strong>
                            <span>Click <strong>Projects → New Project</strong>. Give it a name, code, and assign team
                                members. Each project becomes a workspace for tasks, documents, and reports.</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-content">
                            <strong>Invite your team</strong>
                            <span>Go to <strong>Settings → Users</strong> and invite team members by email. They'll
                                receive a link to join your company and access assigned projects.</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-content">
                            <strong>Enable modules for your project</strong>
                            <span>Open your project and click <strong>Modules</strong>. Turn on the features your team
                                needs — Tasks, Documents, Safety, Field Management, and more.</span>
                        </div>
                    </li>
                </ol>
            </section>

            <!-- Navigation -->
            <section id="navigation">
                <h2>🧭 Navigating the App</h2>
                <p>InfraHub is organized around <strong>projects</strong>. Here's how to find your way around:</p>

                <ul>
                    <li><strong>Dashboard</strong> — Your home screen showing active projects, recent tasks, and key
                        metrics at a glance.</li>
                    <li><strong>Left Sidebar</strong> — Access your projects, clients, company settings, and reports
                        from the navigation menu.</li>
                    <li><strong>Project View</strong> — Click any project to enter its workspace. Use the module tabs
                        (Tasks, Documents, Safety, etc.) to switch between features.</li>
                    <li><strong>Quick Actions</strong> — The <strong>+ New</strong> buttons throughout the app let you
                        quickly create tasks, upload documents, or log incidents.</li>
                    <li><strong>Search</strong> — Use the search bar at the top to find projects, tasks, documents, or
                        team members by name.</li>
                </ul>

                <div class="info-box info-tip">
                    <span class="box-icon">⌨️</span>
                    <div>
                        <strong>Keyboard shortcut:</strong> Press <strong>Ctrl + K</strong> (or <strong>Cmd + K</strong>
                        on Mac) to open the quick search from any page.
                    </div>
                </div>
            </section>

            <!-- Projects -->
            <section id="projects">
                <h2>📁 Projects</h2>
                <p>Projects are the heart of InfraHub. Every task, document, and report belongs to a project.</p>

                <h3>Creating a Project</h3>
                <ol>
                    <li>Click <strong>Projects</strong> in the sidebar, then click <strong>New Project</strong>.</li>
                    <li>Fill in the project name, code (e.g., PROJ-001), client, and location.</li>
                    <li>Set the start and end dates, budget, and project type.</li>
                    <li>Assign team members and set their roles (Project Manager, Engineer, Site Supervisor, etc.).</li>
                    <li>Click <strong>Create</strong> — your project workspace is ready!</li>
                </ol>

                <h3>Managing Team Access</h3>
                <p>Each project has its own team. To add or remove members:</p>
                <ul>
                    <li>Open the project and go to the <strong>Team</strong> tab.</li>
                    <li>Click <strong>Add Member</strong> and select users from your company.</li>
                    <li>Assign roles to control what each person can view and edit.</li>
                </ul>

                <div class="info-box info-warn">
                    <span class="box-icon">⚠️</span>
                    <div>
                        <strong>Important:</strong> Only company admins and project managers can add or remove team
                        members.
                    </div>
                </div>
            </section>

            <!-- Documents -->
            <section id="documents">
                <h2>📄 Documents & Drawings</h2>
                <p>InfraHub follows <strong>ISO 19650</strong> standards for document management. Upload, review,
                    approve, and version-control all your project files.</p>

                <h3>Uploading Documents</h3>
                <ol>
                    <li>Open your project and click the <strong>Documents</strong> tab.</li>
                    <li>Click <strong>Upload Document</strong> and select your files (PDF, DWG, BIM, images, etc.).</li>
                    <li>Add a title, description, discipline (e.g., Structural, Electrical), and revision number.</li>
                    <li>Click <strong>Upload</strong> — your document is saved with full version history.</li>
                </ol>

                <h3>Review & Approval Workflow</h3>
                <p>Documents go through a structured review process:</p>
                <ul>
                    <li><strong>Work in Progress</strong> — The document is being prepared.</li>
                    <li><strong>Under Review</strong> — Submitted for team review. Reviewers can comment and approve or
                        reject.</li>
                    <li><strong>Approved</strong> — The document is finalized and ready for use.</li>
                    <li><strong>Superseded</strong> — A newer revision has replaced this version.</li>
                </ul>

                <div class="info-box info-tip">
                    <span class="box-icon">💡</span>
                    <div>
                        <strong>Tip:</strong> All uploaded files are automatically scanned for viruses and validated to
                        block unsafe file types.
                    </div>
                </div>
            </section>

            <!-- Tasks -->
            <section id="tasks">
                <h2>✅ Tasks & Workflow</h2>
                <p>Assign work, track progress, and keep your team on schedule with Tasks.</p>

                <h3>Creating a Task</h3>
                <ol>
                    <li>Open your project and go to the <strong>Tasks</strong> tab.</li>
                    <li>Click <strong>New Task</strong>.</li>
                    <li>Enter the task title, description, priority (Low / Medium / High / Critical), and due date.</li>
                    <li>Assign it to a team member.</li>
                    <li>Click <strong>Create</strong>.</li>
                </ol>

                <h3>Tracking Progress</h3>
                <ul>
                    <li>Tasks move through stages: <strong>To Do → In Progress → Under Review → Completed</strong>.</li>
                    <li>Update the progress percentage to show how much work is done.</li>
                    <li>Add comments to discuss work or share updates with your team.</li>
                    <li>Attach files, drawings, or photos directly to the task.</li>
                </ul>
            </section>

            <!-- Safety -->
            <section id="safety">
                <h2>⚠️ Safety & SHEQ</h2>
                <p>Track safety incidents, conduct inspections, and run toolbox talks to keep your sites safe.</p>

                <h3>Reporting an Incident</h3>
                <ol>
                    <li>Go to the project's <strong>Safety</strong> tab and click <strong>Report Incident</strong>.</li>
                    <li>Describe what happened, the severity (Near Miss / Minor / Major / Critical), and the location.
                    </li>
                    <li>Upload photos of the scene.</li>
                    <li>Submit the report — the safety officer and project manager are notified immediately.</li>
                </ol>

                <h3>Inspections & Toolbox Talks</h3>
                <ul>
                    <li><strong>Inspections</strong> — Schedule and record site safety inspections with checklists.</li>
                    <li><strong>Toolbox Talks</strong> — Log safety briefings with attendance records and topics
                        covered.</li>
                </ul>
            </section>

            <!-- Field Management -->
            <section id="field">
                <h2>👷 Field Management</h2>
                <p>Record what happens on site every day — weather conditions, work activities, crew attendance, and
                    more.</p>

                <h3>Daily Site Diaries</h3>
                <p>Create a diary entry for each day on site:</p>
                <ul>
                    <li>Record the <strong>weather conditions</strong> and how they affected work.</li>
                    <li>Log <strong>activities completed</strong> and materials used.</li>
                    <li>Note any <strong>delays, visitors, or issues</strong> encountered.</li>
                    <li>Add photos to document site conditions.</li>
                </ul>

                <h3>Crew Attendance</h3>
                <p>Track who's on site each day:</p>
                <ul>
                    <li>Mark workers as <strong>Present, Absent, Late,</strong> or <strong>On Leave</strong>.</li>
                    <li>Record overtime hours.</li>
                    <li>View attendance summaries by week or month.</li>
                </ul>

                <div class="info-box info-tip">
                    <span class="box-icon">📱</span>
                    <div>
                        <strong>Works offline!</strong> Site diary entries and attendance records can be filled in
                        without internet. They'll sync automatically when you're back online.
                    </div>
                </div>
            </section>

            <!-- Equipment -->
            <section id="equipment">
                <h2>🚜 Equipment & Assets</h2>
                <p>Track all your plant, machinery, and tools — where they are, who's using them, and their condition.
                </p>

                <h3>What You Can Do</h3>
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="fc-icon">📋</div>
                        <div class="fc-title">Allocate to Projects</div>
                        <div class="fc-desc">Assign equipment to projects with start and end dates. Know exactly where
                            each asset is.</div>
                    </div>
                    <div class="feature-card">
                        <div class="fc-icon">⛽</div>
                        <div class="fc-title">Fuel Logs</div>
                        <div class="fc-desc">Record fuel consumption for each piece of equipment. Track costs per
                            project.</div>
                    </div>
                    <div class="feature-card">
                        <div class="fc-icon">🔧</div>
                        <div class="fc-title">Maintenance</div>
                        <div class="fc-desc">Schedule servicing, log repairs, and get alerts when maintenance is due.
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="fc-icon">📊</div>
                        <div class="fc-title">Utilization Reports</div>
                        <div class="fc-desc">See which equipment is idle, overworked, or due for return from site.</div>
                    </div>
                </div>
            </section>

            <!-- Financials -->
            <section id="financials">
                <h2>💰 Financials</h2>
                <p>Manage your Bills of Quantities, invoices, payment certificates, and cost tracking all in one place.
                </p>

                <ul>
                    <li><strong>BOQ (Bills of Quantities)</strong> — Define line items with rates and quantities. Track
                        actual vs budgeted costs.</li>
                    <li><strong>Invoices</strong> — Generate professional invoices with your company branding and send
                        them to clients.</li>
                    <li><strong>Payment Certificates</strong> — Create interim and final payment certificates based on
                        work completed.</li>
                    <li><strong>Change Orders</strong> — Document scope changes with cost and time implications. Track
                        approvals.</li>
                    <li><strong>Expense Tracking</strong> — Log project expenses and categorize them for clear financial
                        reporting.</li>
                </ul>
            </section>

            <!-- Inventory -->
            <section id="inventory">
                <h2>📦 Inventory & Procurement</h2>
                <p>Control your materials from request to delivery.</p>

                <h3>The Procurement Workflow</h3>
                <ol class="step-list">
                    <li>
                        <div class="step-content">
                            <strong>Material Requisition</strong>
                            <span>A site engineer requests materials by filling out a requisition form with quantities
                                and required-by dates.</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-content">
                            <strong>Manager Approval</strong>
                            <span>The project manager reviews and approves (or rejects) the requisition.</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-content">
                            <strong>Purchase Order</strong>
                            <span>An approved requisition becomes a Purchase Order sent to the supplier.</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-content">
                            <strong>Goods Received</strong>
                            <span>When materials arrive, record a Goods Received Note (GRN) to update stock
                                levels.</span>
                        </div>
                    </li>
                    <li>
                        <div class="step-content">
                            <strong>Issue to Site</strong>
                            <span>Materials are issued from the store to the construction site, tracked by
                                project.</span>
                        </div>
                    </li>
                </ol>
            </section>

            <!-- Contracts -->
            <section id="contracts">
                <h2>🏗️ Contracts & Subcontractors</h2>
                <p>Manage your subcontractor relationships, scope of work, and contract variations.</p>

                <ul>
                    <li><strong>Subcontractor Profiles</strong> — Store contact details, specialties, insurance, and
                        performance ratings.</li>
                    <li><strong>Work Packages</strong> — Define scope, pricing, and schedule for each subcontracted
                        package.</li>
                    <li><strong>Change Orders</strong> — Track contract variations with justifications and approval
                        workflows.</li>
                    <li><strong>RFIs</strong> — Send Requests for Information to resolve design or construction
                        questions.</li>
                    <li><strong>Submittals</strong> — Submit shop drawings, samples, and technical documents for review.
                    </li>
                </ul>
            </section>

            <!-- Offline -->
            <section id="offline">
                <h2>☁️ Working Offline</h2>
                <p>Construction sites often have poor internet. InfraHub is designed to work <strong>without a
                        connection</strong> so your field team is never blocked.</p>

                <h3>How It Works</h3>
                <ul>
                    <li><strong>Every page you visit is saved</strong> — You can browse previously viewed pages even
                        without internet.</li>
                    <li><strong>Fill in forms offline</strong> — Create site diaries, record attendance, and report
                        incidents without a connection.</li>
                    <li><strong>Automatic sync</strong> — When internet returns, your data uploads automatically in the
                        background.</li>
                    <li><strong>No data loss</strong> — Everything is stored safely on your device until it syncs.</li>
                </ul>

                <div class="info-box info-note">
                    <span class="box-icon">💡</span>
                    <div>
                        <strong>Offline shortcut:</strong> Press <strong>Ctrl + Shift + S</strong> on any form page to
                        save your current work offline.
                    </div>
                </div>

                <h3>What Works Offline</h3>
                <ul>
                    <li>✅ Viewing any previously visited page</li>
                    <li>✅ Creating and editing tasks, work orders, and daily diaries</li>
                    <li>✅ Recording crew attendance</li>
                    <li>✅ Reporting safety incidents</li>
                    <li>✅ All form submissions (auto-synced when online)</li>
                    <li>📎 File uploads require internet (they'll queue and upload when connected)</li>
                </ul>
            </section>

            <!-- PWA -->
            <section id="pwa">
                <h2>📱 Install InfraHub on Your Phone</h2>
                <p>InfraHub works like a native app on your phone, tablet, or desktop — no app store needed.</p>

                <h3>On Android / Chrome</h3>
                <ol>
                    <li>Open <strong>{{ config('app.url') }}/app</strong> in Chrome.</li>
                    <li>Tap the <strong>"Install"</strong> banner at the bottom — or tap the <strong>⋮ menu → Install
                            App</strong>.</li>
                    <li>InfraHub appears on your home screen with its own icon, just like a regular app.</li>
                </ol>

                <h3>On iPhone / iPad</h3>
                <ol>
                    <li>Open <strong>{{ config('app.url') }}/app</strong> in Safari.</li>
                    <li>Tap the <strong>Share button</strong> (square with arrow).</li>
                    <li>Scroll down and tap <strong>"Add to Home Screen"</strong>.</li>
                    <li>Tap <strong>Add</strong> — the app icon appears on your home screen.</li>
                </ol>

                <h3>On Desktop (Windows / Mac)</h3>
                <ol>
                    <li>Open <strong>{{ config('app.url') }}/app</strong> in Chrome or Edge.</li>
                    <li>Click the <strong>install icon</strong> in the address bar (or <strong>⋮ → Install
                            InfraHub</strong>).</li>
                    <li>The app opens in its own window with offline support.</li>
                </ol>
            </section>

            <!-- Account & Security -->
            <section id="security">
                <h2>🔒 Account & Security</h2>
                <p>InfraHub takes the security of your data seriously. Here's how your account is protected:</p>

                <h3>Your Account</h3>
                <ul>
                    <li><strong>Change your password</strong> — Go to <strong>Settings → Profile</strong> and click
                        <strong>Change Password</strong>. Old passwords cannot be reused.</li>
                    <li><strong>Two-Factor Authentication (2FA)</strong> — When enabled, you'll receive a one-time code
                        by email each time you log in. This adds an extra layer of security.</li>
                    <li><strong>Active Sessions</strong> — View all devices where you're logged in. You can log out of
                        any session remotely from <strong>Settings → Sessions</strong>.</li>
                </ul>

                <h3>Data Protection</h3>
                <ul>
                    <li>All data is encrypted in transit (HTTPS) and at rest.</li>
                    <li>Your company's data is completely isolated from other companies.</li>
                    <li>File uploads are scanned for viruses before being stored.</li>
                    <li>Dangerous file types (executables, scripts) are automatically blocked.</li>
                    <li>Login attempts are rate-limited to prevent brute-force attacks.</li>
                </ul>

                <div class="info-box info-warn">
                    <span class="box-icon">🔐</span>
                    <div>
                        <strong>Security tip:</strong> Never share your login credentials. If you suspect unauthorized
                        access, change your password immediately and contact your company admin.
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section id="faq">
                <h2>❓ Frequently Asked Questions</h2>

                <div class="faq-item" data-search="password reset forgot login">
                    <div class="faq-q" onclick="this.parentElement.classList.toggle('open')">
                        I forgot my password. How do I reset it?
                        <span class="arrow">▼</span>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">
                            On the login page, click <strong>"Forgot Password"</strong>. Enter your email address and
                            you'll receive a link to create a new password. The link expires in 60 minutes.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-search="add team member invite user">
                    <div class="faq-q" onclick="this.parentElement.classList.toggle('open')">
                        How do I add a new team member?
                        <span class="arrow">▼</span>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">
                            Go to <strong>Settings → Users → Add User</strong>. Enter their name and email address.
                            They'll receive an invitation email with login instructions. After they accept, you can
                            assign them to projects.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-search="offline data sync internet connection">
                    <div class="faq-q" onclick="this.parentElement.classList.toggle('open')">
                        What happens to my data if I lose internet on site?
                        <span class="arrow">▼</span>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">
                            Your data is safe! InfraHub stores all your work locally on your device. When internet
                            returns, everything syncs automatically. You'll see a notification confirming the sync. You
                            can also check pending uploads at <strong>Settings → Offline Forms</strong>.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-search="modules enable disable features">
                    <div class="faq-q" onclick="this.parentElement.classList.toggle('open')">
                        How do I enable additional modules for my project?
                        <span class="arrow">▼</span>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">
                            Open your project, go to the <strong>Modules</strong> tab, and toggle on the modules you
                            need (e.g., Tasks, Safety, Inventory). Note: Your company subscription determines which
                            modules are available. Contact your admin if you need access to additional modules.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-search="upload file document size limit format">
                    <div class="faq-q" onclick="this.parentElement.classList.toggle('open')">
                        What file types and sizes can I upload?
                        <span class="arrow">▼</span>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">
                            You can upload PDFs, images (JPG, PNG, WebP), CAD files (DWG, DXF), BIM models (IFC, RVT),
                            spreadsheets, and documents. The maximum file size depends on your company plan (typically
                            50MB–200MB per file). Executable files (.exe, .bat, .php) are blocked for security.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-search="mobile app phone tablet download">
                    <div class="faq-q" onclick="this.parentElement.classList.toggle('open')">
                        Is there a mobile app I can download?
                        <span class="arrow">▼</span>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">
                            InfraHub is a Progressive Web App (PWA) — it works like a native app without needing to
                            download from an app store. See the <a href="#pwa">Install on Phone</a> section for
                            step-by-step instructions for Android, iPhone, and desktop.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-search="export report download data csv pdf">
                    <div class="faq-q" onclick="this.parentElement.classList.toggle('open')">
                        Can I export my data?
                        <span class="arrow">▼</span>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">
                            Yes! Most tables have an <strong>Export</strong> button that lets you download data as CSV
                            or Excel. Reports can be exported as PDF. Your company admin can also request a full data
                            export from Settings.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-search="client portal external access">
                    <div class="faq-q" onclick="this.parentElement.classList.toggle('open')">
                        Can my clients access InfraHub?
                        <span class="arrow">▼</span>
                    </div>
                    <div class="faq-a">
                        <div class="faq-a-inner">
                            Yes! InfraHub has a dedicated <strong>Client Portal</strong> where your clients can view
                            project progress, documents shared with them, invoices, and quotations. They get their own
                            login at <strong>{{ config('app.url') }}/client</strong>.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact -->
            <section id="contact">
                <h2>💬 Contact Support</h2>
                <p>Can't find what you're looking for? Our team is here to help.</p>

                <div class="contact-card">
                    <h3>Need Help?</h3>
                    <p>Our support team is available Monday through Friday, 8:00 AM — 6:00 PM (EAT).</p>
                    <a href="mailto:support@infrahub.click" class="btn">
                        ✉️ Email Support
                    </a>
                </div>

                <div class="info-box info-tip">
                    <span class="box-icon">💡</span>
                    <div>
                        <strong>Before contacting support:</strong> Include your company name, the page where the issue
                        occurred, and a screenshot if possible. This helps us resolve your issue faster.
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <div
                style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--border-subtle); text-align: center;">
                <p style="font-size: 0.78rem; color: var(--text-muted);">
                    InfraHub v{{ config('app.version') }} · © {{ date('Y') }} All rights reserved.<br>
                    <a href="/" style="color: var(--amber-400);">← Back to Home</a> ·
                    <a href="/app" style="color: var(--amber-400);">Open App →</a>
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

        // Search functionality
        const searchInput = document.getElementById('helpSearch');
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const allSections = document.querySelectorAll('section[id]');

            if (!query) {
                allSections.forEach(s => s.style.display = '');
                document.querySelectorAll('.faq-item').forEach(f => f.style.display = '');
                return;
            }

            allSections.forEach(section => {
                const text = section.textContent.toLowerCase();
                section.style.display = text.includes(query) ? '' : 'none';
            });

            // Also search FAQ data-search attributes
            document.querySelectorAll('.faq-item').forEach(faq => {
                const searchData = (faq.dataset.search || '') + ' ' + faq.textContent.toLowerCase();
                if (searchData.includes(query)) {
                    faq.style.display = '';
                    faq.classList.add('open');
                }
            });
        });

        // Close mobile sidebar when clicking a link
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => {
                document.querySelector('.sidebar').classList.remove('open');
            });
        });
    </script>
</body>

</html>