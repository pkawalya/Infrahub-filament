<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#030712">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/images/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
    <title>@yield('title', 'InfraHub Enterprise')</title>
    <link rel="preload" href="/css/mobile.css?v={{ filemtime(public_path('css/mobile.css')) }}" as="style">
    <link rel="stylesheet" href="/css/mobile.css?v={{ filemtime(public_path('css/mobile.css')) }}">
    @stack('head')
</head>

<body>
    {{-- Offline Banner --}}
    <div class="m-offline-bar" id="offline-bar">
        <span class="m-offline-dot"></span>
        Offline mode active — Local changes queued for auto-sync
    </div>

    {{-- Header --}}
    <header class="m-header">
        <div style="display:flex;align-items:center;gap:0.6rem;">
            <a href="/mobile" class="m-header-brand">
                <img src="/logo/infrahub-logo-dark.png" alt="InfraHub" class="logo">
            </a>
            <div class="m-company-badge" onclick="openSheet('sheet-company-switcher')" title="Switch active company context">
                <span class="m-company-dot"></span>
                <span id="m-company-name-text">InfraHub Enterprise</span>
                <span style="font-size:0.65rem;opacity:0.7;">▾</span>
            </div>
        </div>
        <div class="m-header-actions">
            <button onclick="toggleModulesMenu()" type="button" class="m-header-icon" title="All Modules">
                <x-mobile.icon name="cde" size="20" stroke="2" />
            </button>
            <a href="/mobile/notifications" class="m-header-icon" id="notif-bell">
                <x-mobile.icon name="bell" size="20" stroke="2" />
                <span class="m-badge" id="notif-count" style="display:none">0</span>
            </a>
            <a href="/mobile/profile" class="m-header-icon">
                <x-mobile.icon name="profile" size="20" stroke="2" />
            </a>
        </div>
    </header>

    {{-- Searchable Modules Launcher Overlay Modal --}}
    <div id="m-modules-modal" style="display:none;position:fixed;inset:0;z-index:9990;background:rgba(3,7,18,0.94);backdrop-filter:blur(24px);padding:1.25rem;overflow-y:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <div>
                <h3 style="margin:0;font-size:1.25rem;font-weight:800;color:var(--text);">Enterprise Launcher</h3>
                <p style="margin:0.2rem 0 0;font-size:0.78rem;color:var(--text-dim);">18 Field Management Suite</p>
            </div>
            <button onclick="toggleModulesMenu()" style="background:rgba(30,41,59,0.6);border:1px solid var(--border);color:var(--text-dim);width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:1.1rem;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        {{-- Live Search Input --}}
        <div class="m-search-box">
            <svg class="m-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="text" id="m-launcher-search" class="m-search-input" placeholder="Search module name or key..." oninput="filterModules(this.value, document.querySelector('.m-category-tab.active')?.getAttribute('data-cat') || 'all')">
        </div>

        {{-- Category Tabs --}}
        <div class="m-category-tabs">
            <button type="button" class="m-category-tab active" data-cat="all" onclick="filterModules(document.getElementById('m-launcher-search').value, 'all')">All Suite</button>
            <button type="button" class="m-category-tab" data-cat="operations" onclick="filterModules(document.getElementById('m-launcher-search').value, 'operations')">Site Ops</button>
            <button type="button" class="m-category-tab" data-cat="commercial" onclick="filterModules(document.getElementById('m-launcher-search').value, 'commercial')">Commercial</button>
            <button type="button" class="m-category-tab" data-cat="qahse" onclick="filterModules(document.getElementById('m-launcher-search').value, 'qahse')">QA & HSE</button>
            <button type="button" class="m-category-tab" data-cat="analytics" onclick="filterModules(document.getElementById('m-launcher-search').value, 'analytics')">Analytics</button>
        </div>

        {{-- Grid of 18 Modules --}}
        <div id="m-launcher-grid" style="display:grid;grid-template-columns:repeat(2, 1fr);gap:0.75rem;">
            <x-mobile.module-card href="/mobile/diaries" icon="diaries" color="blue" title="Site Diaries" subtitle="Daily weather & work logs" category="operations" />
            <x-mobile.module-card href="/mobile/attendance" icon="attendance" color="green" title="Attendance" subtitle="Crew shift clock-in" category="operations" />
            <x-mobile.module-card href="/mobile/safety" icon="safety" color="red" title="Safety Incidents" subtitle="HSE hazard triage" category="qahse" />
            <x-mobile.module-card href="/mobile/equipment" icon="equipment" color="amber" title="Equipment Fleet" subtitle="Machinery & fuel logs" category="operations" />
            <x-mobile.module-card href="/mobile/drawings" icon="drawings" color="purple" title="Drawings & CDE" subtitle="Blueprint revisions & docs" category="operations" />
            <x-mobile.module-card href="/mobile/boq" icon="boq" color="cyan" title="BOQ Management" subtitle="Bill of quantities & rates" category="commercial" />
            <x-mobile.module-card href="/mobile/planning" icon="planning" color="indigo" title="Planning & Progress" subtitle="Schedule milestones" category="operations" />
            <x-mobile.module-card href="/mobile/financials" icon="financials" color="emerald" title="Financials" subtitle="IPC certificates" category="commercial" />
            <x-mobile.module-card href="/mobile/subcontractors" icon="subcontractors" color="blue" title="Subcontractors" subtitle="Vendor directory" category="commercial" />
            <x-mobile.module-card href="/mobile/tenders" icon="tenders" color="amber" title="Tenders & Bids" subtitle="Procurement pipeline" category="commercial" />
            <x-mobile.module-card href="/mobile/rfis" icon="rfis" color="pink" title="RFIs & Submittals" subtitle="Technical queries" category="operations" />
            <x-mobile.module-card href="/mobile/materials" icon="materials" color="purple" title="Materials" subtitle="Stock & delivery log" category="operations" />
            <x-mobile.module-card href="/mobile/change-orders" icon="change-orders" color="cyan" title="Change Orders" subtitle="Scope variations" category="commercial" />
            <x-mobile.module-card href="/mobile/work-orders" icon="work-orders" color="amber" title="Work Orders" subtitle="Plant maintenance" category="operations" />
            <x-mobile.module-card href="/mobile/quality" icon="quality" color="cyan" title="QA/QC Punch List" subtitle="Defect inspection" category="qahse" />
            <x-mobile.module-card href="/mobile/approvals" icon="approvals" color="green" title="Approvals" subtitle="Manager sign-offs" category="commercial" />
            <x-mobile.module-card href="/mobile/reporting" icon="reporting" color="indigo" title="Reporting & KPIs" subtitle="Field summaries" category="analytics" />
            <x-mobile.module-card href="/mobile/suggestions" icon="suggestions" color="amber" title="Suggestion Box" subtitle="Team feedback" category="analytics" />
        </div>
    </div>

    {{-- Main Content --}}
    <main class="m-content">
        @yield('content')
    </main>

    {{-- Floating Action Button (FAB) Speed Dial --}}
    <div class="m-fab-container" id="m-fab-container">
        <div class="m-fab-menu">
            <button type="button" onclick="openSheet('sheet-clock-in')" class="m-fab-item">
                <span class="m-fab-item-icon" style="background:rgba(34,197,94,0.2);color:#4ade80;">
                    <x-mobile.icon name="attendance" size="16" stroke="2" />
                </span>
                Crew Clock-In
            </button>
            <button type="button" onclick="openSheet('sheet-diary')" class="m-fab-item">
                <span class="m-fab-item-icon" style="background:rgba(59,130,246,0.2);color:#60a5fa;">
                    <x-mobile.icon name="diaries" size="16" stroke="2" />
                </span>
                Log Daily Site Diary
            </button>
            <button type="button" onclick="openSheet('sheet-safety')" class="m-fab-item">
                <span class="m-fab-item-icon" style="background:rgba(239,68,68,0.2);color:#f87171;">
                    <x-mobile.icon name="safety" size="16" stroke="2" />
                </span>
                Report Safety Hazard
            </button>
        </div>
        <button type="button" class="m-fab-main" onclick="toggleFAB()" aria-label="Quick site actions">
            <svg class="m-fab-main-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>
    </div>

    {{-- Bottom Sheet Modal Backdrop --}}
    <div class="m-sheet-backdrop" id="m-sheet-backdrop" onclick="closeSheet()"></div>

    {{-- Quick Clock-In Action Sheet --}}
    <div class="m-sheet" id="sheet-clock-in">
        <div class="m-sheet-handle"></div>
        <div class="m-sheet-header">
            <div class="m-sheet-title">
                <x-mobile.icon name="attendance" size="22" class="text-emerald-400" />
                Rapid Crew Clock-In
            </div>
            <button type="button" class="m-sheet-close" onclick="closeSheet('sheet-clock-in')">✕</button>
        </div>
        <form onsubmit="event.preventDefault(); MobileActions.quickClockIn(this);">
            <div class="m-form-group">
                <label class="m-label">Project</label>
                <select name="project_id" class="m-select" required id="sheet-clockin-projects">
                    <option value="">Select active project...</option>
                </select>
            </div>
            <div class="m-form-group">
                <label class="m-label">Crew Name / Gang</label>
                <input type="text" name="crew_name" class="m-input" placeholder="e.g. Concrete Gang Alpha" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Headcount</label>
                    <input type="number" name="headcount" class="m-input" value="1" min="1" required>
                </div>
                <div class="m-form-group">
                    <label class="m-label">Shift</label>
                    <select name="shift_type" class="m-select">
                        <option value="day">Day Shift</option>
                        <option value="night">Night Shift</option>
                    </select>
                </div>
            </div>
            <div class="m-form-group">
                <label class="m-label">Notes (Optional)</label>
                <input type="text" name="notes" class="m-input" placeholder="Location, task scope...">
            </div>
            <button type="submit" class="m-btn m-btn-primary">Clock-In Crew Now</button>
        </form>
    </div>

    {{-- Quick Site Diary Action Sheet --}}
    <div class="m-sheet" id="sheet-diary">
        <div class="m-sheet-handle"></div>
        <div class="m-sheet-header">
            <div class="m-sheet-title">
                <x-mobile.icon name="diaries" size="22" class="text-blue-400" />
                Daily Site Diary Log
            </div>
            <button type="button" class="m-sheet-close" onclick="closeSheet('sheet-diary')">✕</button>
        </div>
        <form onsubmit="event.preventDefault(); MobileActions.quickLogDiary(this);">
            <div class="m-form-group">
                <label class="m-label">Project</label>
                <select name="project_id" class="m-select" required id="sheet-diary-projects">
                    <option value="">Select active project...</option>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Date</label>
                    <input type="date" name="date" class="m-input" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="m-form-group">
                    <label class="m-label">Weather</label>
                    <select name="weather" class="m-select">
                        <option value="Clear / Sunny">Clear / Sunny</option>
                        <option value="Overcast">Overcast</option>
                        <option value="Rain / Storm">Rain / Storm</option>
                    </select>
                </div>
            </div>
            <div class="m-form-group">
                <label class="m-label">Daily Work Summary</label>
                <textarea name="summary" class="m-textarea" placeholder="Describe today's activities & site progress..." required></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-primary">Submit Daily Log</button>
        </form>
    </div>

    {{-- Quick HSE Hazard Action Sheet --}}
    <div class="m-sheet" id="sheet-safety">
        <div class="m-sheet-handle"></div>
        <div class="m-sheet-header">
            <div class="m-sheet-title">
                <x-mobile.icon name="safety" size="22" class="text-red-400" />
                HSE Safety Hazard Report
            </div>
            <button type="button" class="m-sheet-close" onclick="closeSheet('sheet-safety')">✕</button>
        </div>
        <form onsubmit="event.preventDefault(); MobileActions.quickReportHazard(this);">
            <div class="m-form-group">
                <label class="m-label">Project</label>
                <select name="project_id" class="m-select" required id="sheet-safety-projects">
                    <option value="">Select active project...</option>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Severity Level</label>
                    <select name="severity" class="m-select">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High / Critical</option>
                    </select>
                </div>
                <div class="m-form-group">
                    <label class="m-label">Category</label>
                    <select name="category" class="m-select">
                        <option value="Unsafe Act">Unsafe Act</option>
                        <option value="Unsafe Condition">Unsafe Condition</option>
                        <option value="Near Miss">Near Miss</option>
                        <option value="PPE Deficit">PPE Deficit</option>
                    </select>
                </div>
            </div>
            <div class="m-form-group">
                <label class="m-label">Hazard Description</label>
                <textarea name="description" class="m-textarea" placeholder="Detail observed hazard location & situation..." required></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-danger">Report Safety Hazard</button>
        </form>
    </div>

    {{-- Company Context Switcher Action Sheet --}}
    <div class="m-sheet" id="sheet-company-switcher">
        <div class="m-sheet-handle"></div>
        <div class="m-sheet-header">
            <div>
                <div class="m-sheet-title">
                    <x-mobile.icon name="company" size="20" />
                    Active Company Context
                </div>
                <div style="font-size:0.75rem;color:var(--text-dim);margin-top:0.2rem;">Select company workspace for field operations</div>
            </div>
            <button type="button" class="m-sheet-close" onclick="closeSheet('sheet-company-switcher')">✕</button>
        </div>

        <div id="m-company-switcher-list" style="margin-top:0.5rem;">
            <div class="m-company-card active" data-company-id="1" onclick="MobileUI.switchCompany(1, 'InfraHub Enterprise', 'Senior Engineer')">
                <div>
                    <div style="font-weight:800;font-size:0.92rem;color:var(--text);display:flex;align-items:center;gap:0.4rem;">
                        <x-mobile.icon name="company" size="18" class="text-indigo-400" />
                        InfraHub Enterprise
                    </div>
                    <div style="font-size:0.75rem;color:var(--text-dim);margin-top:0.15rem;">Senior Site Engineer · Primary Tenant</div>
                </div>
                <x-mobile.pill status="active" label="Active" />
            </div>
            <div class="m-company-card" data-company-id="2" onclick="MobileUI.switchCompany(2, 'BuildCorp Infrastructure', 'Project Manager')">
                <div>
                    <div style="font-weight:800;font-size:0.92rem;color:var(--text);display:flex;align-items:center;gap:0.4rem;">
                        <x-mobile.icon name="building" size="18" class="text-blue-400" />
                        BuildCorp Infrastructure
                    </div>
                    <div style="font-size:0.75rem;color:var(--text-dim);margin-top:0.15rem;">Consultant / PM · 3 Active Projects</div>
                </div>
                <x-mobile.pill status="planning" label="Switch" />
            </div>
            <div class="m-company-card" data-company-id="3" onclick="MobileUI.switchCompany(3, 'GeoWorld Contractors', 'Subcontractor Lead')">
                <div>
                    <div style="font-weight:800;font-size:0.92rem;color:var(--text);display:flex;align-items:center;gap:0.4rem;">
                        <x-mobile.icon name="subcontractors" size="18" class="text-emerald-400" />
                        GeoWorld Contractors
                    </div>
                    <div style="font-size:0.75rem;color:var(--text-dim);margin-top:0.15rem;">Surveying Partner · 1 Active Project</div>
                </div>
                <x-mobile.pill status="planning" label="Switch" />
            </div>
        </div>
    </div>

    {{-- Bottom Navigation --}}
    <nav class="m-nav">
        <a href="/mobile" class="m-nav-item {{ ($active ?? '') === 'home' ? 'active' : '' }}">
            <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Home
        </a>
        <a href="/mobile/projects" class="m-nav-item {{ ($active ?? '') === 'projects' ? 'active' : '' }}">
            <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0V21" />
            </svg>
            Projects
        </a>
        <a href="/mobile/tasks" class="m-nav-item {{ ($active ?? '') === 'tasks' ? 'active' : '' }}">
            <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Tasks
        </a>
        <a href="/mobile/forms" class="m-nav-item {{ ($active ?? '') === 'forms' ? 'active' : '' }}">
            <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Forms
        </a>
        <a href="/mobile/profile" class="m-nav-item {{ ($active ?? '') === 'profile' ? 'active' : '' }}">
            <svg class="m-nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Profile
        </a>
    </nav>

    {{-- Toast Container --}}
    <div id="m-toast" class="m-toast"></div>

    {{-- Modular Core Scripts --}}
    <script src="/js/mobile-api.js"></script>
    <script src="/js/mobile-ui.js"></script>
    <script src="/js/mobile-actions.js"></script>
    <script src="/js/offline-db.js"></script>
    <script src="/js/offline-ui.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const populateProjects = (projects) => {
                const selects = ['sheet-clockin-projects', 'sheet-diary-projects', 'sheet-safety-projects'];
                selects.forEach(id => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    const val = el.value;
                    el.innerHTML = '<option value="">Select active project...</option>' +
                        projects.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
                    if (val) el.value = val;
                });
            };

            try {
                const storedNotifs = localStorage.getItem('m_notifications_data');
                if (storedNotifs) {
                    const items = JSON.parse(storedNotifs);
                    const unread = items.filter(n => !n.read).length;
                    const badge = document.getElementById('notif-count');
                    if (badge) {
                        badge.textContent = unread;
                        badge.style.display = unread > 0 ? 'flex' : 'none';
                    }
                }
            } catch (e) { }

            try {
                const cached = localStorage.getItem('m_projects');
                if (cached) populateProjects(JSON.parse(cached));
                
                if (MobileAPI.isLoggedIn()) {
                    const data = await MobileAPI.get('/projects?per_page=50');
                    if (data?.data) {
                        localStorage.setItem('m_projects', JSON.stringify(data.data));
                        populateProjects(data.data);
                    }
                }
            } catch (e) { console.warn('Project populate warning', e); }
        });

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => { });
        }
    </script>

    <x-pwa-install-prompt />
    @stack('scripts')
</body>

</html>