@extends('mobile.layout')
@section('title', 'Dashboard — InfraHub Mobile')

@section('content')
    {{-- User Header Card --}}
    <div style="background:linear-gradient(135deg, rgba(99,102,241,0.18), rgba(139,92,246,0.12));border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;margin-bottom:1.25rem;box-shadow:0 8px 30px rgba(0,0,0,0.35);backdrop-filter:blur(16px);">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div class="m-page-title" id="greeting" style="margin:0;font-size:1.4rem;">Good day</div>
                <div class="m-page-subtitle" id="user-name" style="margin:0.25rem 0 0 0;font-size:0.85rem;color:var(--text-muted);">Loading profile...</div>
            </div>
            <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;color:white;box-shadow:0 4px 16px rgba(99,102,241,0.45);border:2px solid rgba(255,255,255,0.2);" id="user-avatar-initial">
                IH
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="m-stats" id="stats">
        <x-mobile.stat-card variant="accent" value="–" label="Projects" id="s-projects" />
        <x-mobile.stat-card variant="warning" value="–" label="Open Tasks" id="s-tasks" />
        <x-mobile.stat-card variant="danger" value="–" label="Overdue" id="s-overdue" />
        <x-mobile.stat-card variant="success" value="18" label="Modules" id="s-docs" />
    </div>

    {{-- Quick Launcher Grid --}}
    <div class="m-section">
        <span class="m-section-title">Field Operations Suite</span>
        <button type="button" onclick="toggleModulesMenu()" class="m-section-link" style="background:none;border:none;cursor:pointer;">All 18 Modules →</button>
    </div>
    <div class="m-actions" style="display:grid;grid-template-columns:repeat(4, 1fr);gap:0.55rem;">
        <a href="/mobile/diaries" class="m-action">
            <div class="m-action-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;">
                <x-mobile.icon name="diaries" size="20" stroke="2" />
            </div>
            <span class="m-action-label">Diaries</span>
        </a>
        <a href="/mobile/attendance" class="m-action">
            <div class="m-action-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;">
                <x-mobile.icon name="attendance" size="20" stroke="2" />
            </div>
            <span class="m-action-label">Attendance</span>
        </a>
        <a href="/mobile/safety" class="m-action">
            <div class="m-action-icon" style="background:rgba(239,68,68,0.15);color:#f87171;">
                <x-mobile.icon name="safety" size="20" stroke="2" />
            </div>
            <span class="m-action-label">Safety</span>
        </a>
        <a href="/mobile/equipment" class="m-action">
            <div class="m-action-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;">
                <x-mobile.icon name="equipment" size="20" stroke="2" />
            </div>
            <span class="m-action-label">Fleet</span>
        </a>
        <a href="/mobile/drawings" class="m-action">
            <div class="m-action-icon" style="background:rgba(168,85,247,0.15);color:#c084fc;">
                <x-mobile.icon name="drawings" size="20" stroke="2" />
            </div>
            <span class="m-action-label">Drawings</span>
        </a>
        <a href="/mobile/financials" class="m-action">
            <div class="m-action-icon" style="background:rgba(16,185,129,0.15);color:#34d399;">
                <x-mobile.icon name="financials" size="20" stroke="2" />
            </div>
            <span class="m-action-label">Financials</span>
        </a>
        <a href="/mobile/rfis" class="m-action">
            <div class="m-action-icon" style="background:rgba(236,72,153,0.15);color:#f472b6;">
                <x-mobile.icon name="rfis" size="20" stroke="2" />
            </div>
            <span class="m-action-label">RFIs</span>
        </a>
        <a href="/mobile/quality" class="m-action">
            <div class="m-action-icon" style="background:rgba(14,165,233,0.15);color:#38bdf8;">
                <x-mobile.icon name="quality" size="20" stroke="2" />
            </div>
            <span class="m-action-label">QA/QC</span>
        </a>
    </div>

    {{-- My Tasks & Action List --}}
    <div class="m-section" style="margin-top:1.25rem;">
        <span class="m-section-title">My Tasks & Priorities</span>
        <a href="/mobile/tasks" class="m-section-link">View All →</a>
    </div>

    {{-- Task Search Bar --}}
    <div class="m-search-box" style="margin-bottom:0.75rem;">
        <svg class="m-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <input type="text" id="m-task-search" class="m-search-input" placeholder="Filter tasks by title or project..." oninput="filterDashboardTasks(this.value)">
    </div>

    <div id="task-list">
        <div class="m-card">
            <div class="m-skeleton" style="height:60px;"></div>
        </div>
        <div class="m-card">
            <div class="m-skeleton" style="height:60px;"></div>
        </div>
    </div>

    {{-- Recent Projects --}}
    <div class="m-section" style="margin-top:1.25rem;">
        <span class="m-section-title">Active Projects</span>
        <a href="/mobile/projects" class="m-section-link">View All →</a>
    </div>
    <div id="project-list">
        <div class="m-card">
            <div class="m-skeleton" style="height:70px;"></div>
        </div>
        <div class="m-card">
            <div class="m-skeleton" style="height:70px;"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let cachedTasks = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }

            const user = MobileAPI.getUser();
            if (user) {
                const hour = new Date().getHours();
                const greet = hour < 12 ? 'Good morning' : hour < 17 ? 'Good afternoon' : 'Good evening';
                document.getElementById('greeting').textContent = greet;
                const nameStr = user.name || user.email || 'Engineer';
                document.getElementById('user-name').textContent = nameStr;

                const initials = nameStr.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() || 'IH';
                const avatarEl = document.getElementById('user-avatar-initial');
                if (avatarEl) avatarEl.textContent = initials;
            }

            // Load projects
            try {
                const projData = await MobileAPI.get('/projects?per_page=50', 'projects');
                if (projData?.data) {
                    const projects = projData.data;
                    document.getElementById('s-projects').textContent = projects.length;
                    renderProjects(projects.slice(0, 3));
                }
            } catch {
                const cached = localStorage.getItem('m_cache_projects') || localStorage.getItem('m_projects');
                if (cached) {
                    const projects = JSON.parse(cached);
                    document.getElementById('s-projects').textContent = projects.length;
                    renderProjects(projects.slice(0, 3));
                }
            }

            // Load tasks across all projects
            try {
                const cached = localStorage.getItem('m_cache_projects') || localStorage.getItem('m_projects');
                const projects = cached ? JSON.parse(cached) : [];
                let allTasks = [];
                let openCount = 0, overdueCount = 0;

                for (const p of projects.slice(0, 5)) {
                    try {
                        const td = await MobileAPI.get(`/projects/${p.id}/tasks?per_page=20`);
                        if (td?.data) {
                            td.data.forEach(t => { t._project = p.name; });
                            allTasks = allTasks.concat(td.data);
                        }
                    } catch { }
                }

                const now = new Date().toISOString().slice(0, 10);
                allTasks.forEach(t => {
                    if (!['done', 'cancelled'].includes(t.status)) {
                        openCount++;
                        if (t.due_date && t.due_date < now) overdueCount++;
                    }
                });

                document.getElementById('s-tasks').textContent = openCount;
                document.getElementById('s-overdue').textContent = overdueCount;

                cachedTasks = allTasks
                    .filter(t => !['done', 'cancelled'].includes(t.status))
                    .sort((a, b) => (a.due_date || '9999') > (b.due_date || '9999') ? 1 : -1);

                renderTasks(cachedTasks.slice(0, 4));
            } catch (e) { console.warn('Tasks load error', e); }
        });

        function renderProjects(projects) {
            const listEl = document.getElementById('project-list');
            if (!listEl) return;
            listEl.innerHTML = projects.map(p => `
                <a href="/mobile/projects/${p.id}" class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">${esc(p.name)}</div>
                            <div class="m-card-subtitle">${esc(p.code || '')}${p.client?.name ? ' · ' + esc(p.client.name) : ''}</div>
                        </div>
                        <span class="m-pill ${p.status}"><span class="m-pill-dot"></span><span class="m-pill-text">${esc(p.status)}</span></span>
                    </div>
                </a>
            `).join('') || '<div class="m-empty"><div class="m-empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg></div><div class="m-empty-title">No projects found</div></div>';
        }

        function renderTasks(tasks) {
            const now = new Date().toISOString().slice(0, 10);
            const taskEl = document.getElementById('task-list');
            if (!taskEl) return;
            taskEl.innerHTML = tasks.map(t => {
                const isOverdue = t.due_date && t.due_date < now;
                const statusStr = isOverdue ? 'overdue' : t.status;
                const labelStr = isOverdue ? 'Overdue' : t.status;
                return `
                <a href="/mobile/tasks/${t.id}?pid=${t.cde_project_id}" class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">${esc(t.title)}</div>
                            <div class="m-card-subtitle">${esc(t._project || '')}</div>
                        </div>
                        <span class="m-pill ${statusStr}"><span class="m-pill-dot"></span><span class="m-pill-text">${esc(labelStr)}</span></span>
                    </div>
                    ${t.due_date ? `<div class="m-card-footer">Due: ${t.due_date}</div>` : ''}
                </a>`;
            }).join('') || '<div class="m-empty"><div class="m-empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div><div class="m-empty-title">No open tasks</div><div class="m-empty-text">You\'re all caught up!</div></div>';
        }

        function filterDashboardTasks(query) {
            const q = query.toLowerCase().trim();
            const filtered = cachedTasks.filter(t => 
                (t.title && t.title.toLowerCase().includes(q)) || 
                (t._project && t._project.toLowerCase().includes(q))
            );
            renderTasks(filtered.slice(0, 4));
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    </script>
@endpush