<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>InfraHub — User Manual</title>
    <meta name="description" content="InfraHub Modular Project Management System User Manual. Comprehensive workflows, specs, and modules usage.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo/infrahub-icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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
            opacity: 0.15;
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
            grid-template-columns: 280px 1fr;
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
            display: flex;
            flex-direction: column;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--border-subtle);
            border-radius: 4px;
        }

        .sidebar-header {
            padding: 0 1.25rem 1.25rem;
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
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .sidebar-nav {
            padding: 0 0.75rem;
            overflow-y: auto;
            flex-grow: 1;
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
            font-size: 0.8rem;
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

        .sidebar-footer {
            padding: 1rem 1.25rem 0;
            border-top: 1px solid var(--border-subtle);
            margin-top: auto;
        }

        .back-to-app {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            width: 100%;
        }

        .back-to-app:hover {
            background: rgba(232, 162, 41, 0.1);
            border-color: var(--amber-400);
            color: var(--amber-400);
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
            padding: 3rem 4rem;
            max-width: 1000px;
            min-width: 0;
            position: relative;
            z-index: 2;
        }

        /* Live Search Bar */
        .search-box {
            position: relative;
            margin-bottom: 2rem;
            max-width: 600px;
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

        /* Markdown-rendered HTML Styling */
        .docs-content {
            font-size: 0.92rem;
            line-height: 1.8;
            color: var(--text-secondary);
        }

        .docs-content h1 {
            font-size: 2.2rem;
            font-weight: 900;
            letter-spacing: -1px;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--amber-300), var(--amber-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .docs-content h2 {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin: 3.5rem 0 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-primary);
        }

        .docs-content h3 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 2.25rem 0 0.85rem;
            color: var(--amber-300);
        }

        .docs-content h4 {
            font-size: 0.98rem;
            font-weight: 600;
            margin: 1.5rem 0 0.75rem;
            color: var(--text-primary);
        }

        .docs-content p {
            margin-bottom: 1.25rem;
        }

        .docs-content ul,
        .docs-content ol {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .docs-content li {
            margin-bottom: 0.5rem;
        }

        .docs-content strong {
            color: var(--text-primary);
            font-weight: 600;
        }

        .docs-content a {
            color: var(--amber-400);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s;
        }

        .docs-content a:hover {
            text-decoration: underline;
            color: var(--amber-300);
        }

        .docs-content img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            border: 1px solid var(--border-subtle);
            margin: 1.5rem 0 0.5rem;
            display: block;
            background: rgba(0, 0, 0, 0.2);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .docs-content em {
            font-size: 0.82rem;
            color: var(--text-muted);
            font-style: normal;
            display: block;
            margin-bottom: 2rem;
            padding-left: 0.25rem;
            border-left: 2px solid var(--amber-400);
        }

        .docs-content hr {
            border: 0;
            border-top: 1px solid var(--border-subtle);
            margin: 3.5rem 0;
        }

        .docs-content code {
            font-family: monospace;
            background: rgba(255, 255, 255, 0.08);
            padding: 0.15rem 0.35rem;
            border-radius: 4px;
            font-size: 0.85em;
            color: #fca5a5;
        }

        .docs-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            font-size: 0.85rem;
        }

        .docs-content th,
        .docs-content td {
            border: 1px solid var(--border-subtle);
            padding: 0.6rem 0.8rem;
            text-align: left;
        }

        .docs-content th {
            background: rgba(255, 255, 255, 0.03);
            font-weight: 600;
            color: var(--text-primary);
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
                display: flex;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .docs-main {
                padding: 2rem 1.5rem;
            }

            .docs-content h1 {
                font-size: 1.75rem;
            }

            .docs-content h2 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <button class="mobile-menu-btn" onclick="document.querySelector('.sidebar').classList.toggle('open')" aria-label="Toggle menu">☰</button>

    <div class="docs-layout">
        <!-- ── Sidebar ── -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="/app">
                    <div class="logo-icon">IH</div>
                    <h1>User Manual</h1>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-group">
                    <div class="sidebar-group-label">Introduction</div>
                    <a href="#1-system-navigation--layout" class="sidebar-link active"><span class="icon">🧭</span> Navigation</a>
                </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-label">Operations</div>
                    <a href="#project-schedule--tasks" class="sidebar-link"><span class="icon">📅</span> Schedule & Tasks</a>
                    <a href="#work-order-management-core-fsm" class="sidebar-link"><span class="icon">🔧</span> Work Orders</a>
                    <a href="#planning--progress-milestones" class="sidebar-link"><span class="icon">🚩</span> Milestones</a>
                </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-label">Site & Resources</div>
                    <a href="#inventory--store-management" class="sidebar-link"><span class="icon">📦</span> Inventory</a>
                    <a href="#sheq-safety-health-environment-quality--social" class="sidebar-link"><span class="icon">⚠️</span> SHEQ & Safety</a>
                    <a href="#plant--equipment-management" class="sidebar-link"><span class="icon">🚜</span> Equipment</a>
                    <a href="#field-operations-daily-site-logs" class="sidebar-link"><span class="icon">👷</span> Daily Site Diaries</a>
                </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-label">Commercial & Cost</div>
                    <a href="#bill-of-quantities-boq" class="sidebar-link"><span class="icon">📊</span> BOQ</a>
                    <a href="#financial-tracking-invoicing--expenses" class="sidebar-link"><span class="icon">💳</span> Financials</a>
                    <a href="#cost--contract-management" class="sidebar-link"><span class="icon">📝</span> Contracts Manager</a>
                    <a href="#subcontractor-management" class="sidebar-link"><span class="icon">🤝</span> Subcontractors</a>
                    <a href="#tenders--bids" class="sidebar-link"><span class="icon">💸</span> Tenders & Bids</a>
                </div>

                <div class="sidebar-group">
                    <div class="sidebar-group-label">Collaboration</div>
                    <a href="#cde-common-data-environment-document-management" class="sidebar-link"><span class="icon">📁</span> Document CDE</a>
                    <a href="#rfis--submittals" class="sidebar-link"><span class="icon">❓</span> RFIs & Submittals</a>
                    <a href="#reporting-ai-analytics--exports" class="sidebar-link"><span class="icon">📈</span> AI Reports & Exports</a>
                    <a href="#suggestion-box" class="sidebar-link"><span class="icon">📥</span> Suggestion Box</a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <a href="/app" class="back-to-app">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.2rem;height:1.2rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to App
                </a>
            </div>
        </aside>

        <!-- ── Main Content ── -->
        <main class="docs-main">
            <!-- Search -->
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="manualSearch" placeholder="Search user manual topics..." autocomplete="off">
            </div>

            <!-- Content Area -->
            <article class="docs-content">
                {!! $content !!}
            </article>
        </main>
    </div>

    <script>
        // ── Slugify headings to match anchor links in Markdown Table of Contents ──
        function slugify(text) {
            return text
                .toLowerCase()
                .replace(/&/g, '') // Remove ampersands
                .replace(/[^\w\s-]/g, '') // Remove other special characters
                .trim()
                .replace(/\s+/g, '-'); // Replace spaces/multiple spaces with dashes
        }

        // Apply slugs dynamically to markdown headers on load
        document.querySelectorAll('.docs-content h1, .docs-content h2, .docs-content h3').forEach(header => {
            const slug = slugify(header.textContent);
            header.id = slug;
        });

        // ── Active Navigation Links Highlighter on Scroll ──
        const navLinks = document.querySelectorAll('.sidebar-link');
        const sections = Array.from(document.querySelectorAll('.docs-content h2, .docs-content h3')).filter(h => h.id);

        function highlightSection() {
            let scrollPosition = window.scrollY + 150;
            let activeId = null;

            for (const section of sections) {
                if (scrollPosition >= section.offsetTop) {
                    activeId = section.id;
                } else {
                    break;
                }
            }

            if (activeId) {
                navLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href === '#' + activeId) {
                        link.classList.add('active');
                        // Close sidebar on mobile after clicking
                        if (window.innerWidth <= 768) {
                            document.querySelector('.sidebar').classList.remove('open');
                        }
                    } else {
                        link.classList.remove('active');
                    }
                });
            }
        }

        window.addEventListener('scroll', highlightSection);
        highlightSection();

        // ── Live Filter Search through manual content ──
        const searchInput = document.getElementById('manualSearch');
        const contentElements = document.querySelectorAll('.docs-content p, .docs-content li, .docs-content h2, .docs-content h3, .docs-content h4, .docs-content img');

        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase().trim();

            if (!query) {
                // Show everything if query is empty
                contentElements.forEach(el => {
                    el.style.display = '';
                    el.style.opacity = '1';
                });
                return;
            }

            // Simple text scan filter
            contentElements.forEach(el => {
                const text = el.textContent.toLowerCase();
                if (text.includes(query) || (el.tagName === 'IMG' && el.alt.toLowerCase().includes(query))) {
                    el.style.display = '';
                    el.style.opacity = '1';
                } else {
                    // Hide elements that do not match
                    el.style.display = 'none';
                }
            });
            
            // Keep section dividers/headers visible if their child paragraphs match
            document.querySelectorAll('.docs-content h2, .docs-content h3').forEach(header => {
                header.style.display = '';
            });
        });
    </script>
</body>

</html>
