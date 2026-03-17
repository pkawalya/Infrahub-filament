@extends('mobile.layout')
@section('title', 'Dashboard — InfraHub')

@section('content')
    <div class="m-page-title" id="greeting">Good day 👋</div>
    <div class="m-page-subtitle" id="user-name">Loading...</div>

    {{-- Stats --}}
    <div class="m-stats" id="stats">
        <div class="m-stat accent">
            <div class="m-stat-value" id="s-projects">–</div>
            <div class="m-stat-label">Projects</div>
        </div>
        <div class="m-stat warning">
            <div class="m-stat-value" id="s-tasks">–</div>
            <div class="m-stat-label">Open Tasks</div>
        </div>
        <div class="m-stat danger">
            <div class="m-stat-value" id="s-overdue">–</div>
            <div class="m-stat-label">Overdue</div>
        </div>
        <div class="m-stat success">
            <div class="m-stat-value" id="s-docs">–</div>
            <div class="m-stat-label">Documents</div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="m-section"><span class="m-section-title">Quick Actions</span></div>
    <div class="m-actions">
        <a href="/mobile/forms#diary" class="m-action">
            <div class="m-action-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;">📋</div>
            <span class="m-action-label">Site Diary</span>
        </a>
        <a href="/mobile/forms#attendance" class="m-action">
            <div class="m-action-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;">👷</div>
            <span class="m-action-label">Attendance</span>
        </a>
        <a href="/mobile/forms#safety" class="m-action">
            <div class="m-action-icon" style="background:rgba(239,68,68,0.15);color:#f87171;">⚠️</div>
            <span class="m-action-label">Safety</span>
        </a>
        <a href="/mobile/tasks" class="m-action">
            <div class="m-action-icon" style="background:rgba(99,102,241,0.15);color:#a5b4fc;">✅</div>
            <span class="m-action-label">My Tasks</span>
        </a>
    </div>

    {{-- My Tasks --}}
    <div class="m-section">
        <span class="m-section-title">My Tasks</span>
        <a href="/mobile/tasks" class="m-section-link">View All →</a>
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
    <div class="m-section">
        <span class="m-section-title">Projects</span>
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
        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }

            const user = API.getUser();
            if (user) {
                const hour = new Date().getHours();
                const greet = hour < 12 ? 'Good morning' : hour < 17 ? 'Good afternoon' : 'Good evening';
                document.getElementById('greeting').textContent = greet + ' 👋';
                document.getElementById('user-name').textContent = user.name || user.email || '';
            }

            // Load projects
            try {
                const projData = await API.get('/projects?per_page=50');
                if (projData?.data) {
                    const projects = projData.data;
                    document.getElementById('s-projects').textContent = projects.length;

                    // Cache for offline
                    localStorage.setItem('m_projects', JSON.stringify(projects));

                    // Render project cards (latest 3)
                    const listEl = document.getElementById('project-list');
                    listEl.innerHTML = projects.slice(0, 3).map(p => `
                    <a href="/mobile/projects/${p.id}" class="m-card">
                        <div class="m-card-header">
                            <div>
                                <div class="m-card-title">${esc(p.name)}</div>
                                <div class="m-card-subtitle">${esc(p.code || '')}${p.client?.name ? ' · ' + esc(p.client.name) : ''}</div>
                            </div>
                            <span class="m-pill ${p.status}">${esc(p.status)}</span>
                        </div>
                    </a>
                `).join('') || '<div class="m-empty"><div class="m-empty-icon">📁</div><div class="m-empty-title">No projects yet</div></div>';
                }
            } catch {
                // Offline: use cached
                const cached = localStorage.getItem('m_projects');
                if (cached) {
                    const projects = JSON.parse(cached);
                    document.getElementById('s-projects').textContent = projects.length;
                    const listEl = document.getElementById('project-list');
                    listEl.innerHTML = projects.slice(0, 3).map(p => `
                    <a href="/mobile/projects/${p.id}" class="m-card">
                        <div class="m-card-header">
                            <div><div class="m-card-title">${esc(p.name)}</div></div>
                            <span class="m-pill ${p.status}">${esc(p.status)}</span>
                        </div>
                    </a>
                `).join('');
                }
            }

            // Load tasks across all projects (aggregate)
            try {
                const cached = localStorage.getItem('m_projects');
                const projects = cached ? JSON.parse(cached) : [];
                let allTasks = [];
                let openCount = 0, overdueCount = 0;

                // Get tasks from first few projects
                for (const p of projects.slice(0, 5)) {
                    try {
                        const td = await API.get(`/projects/${p.id}/tasks?per_page=20`);
                        if (td?.data) {
                            td.data.forEach(t => { t._project = p.name; });
                            allTasks = allTasks.concat(td.data);
                        }
                    } catch { }
                }

                // Count stats
                const now = new Date().toISOString().slice(0, 10);
                allTasks.forEach(t => {
                    if (!['done', 'cancelled'].includes(t.status)) {
                        openCount++;
                        if (t.due_date && t.due_date < now) overdueCount++;
                    }
                });

                document.getElementById('s-tasks').textContent = openCount;
                document.getElementById('s-overdue').textContent = overdueCount;

                // Sort: overdue first, then by due date
                const openTasks = allTasks
                    .filter(t => !['done', 'cancelled'].includes(t.status))
                    .sort((a, b) => (a.due_date || '9999') > (b.due_date || '9999') ? 1 : -1)
                    .slice(0, 4);

                localStorage.setItem('m_tasks', JSON.stringify(allTasks));

                const taskEl = document.getElementById('task-list');
                taskEl.innerHTML = openTasks.map(t => {
                    const isOverdue = t.due_date && t.due_date < now;
                    return `
                <a href="/mobile/tasks/${t.id}?pid=${t.cde_project_id}" class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">${esc(t.title)}</div>
                            <div class="m-card-subtitle">${esc(t._project || '')}</div>
                        </div>
                        <span class="m-pill ${isOverdue ? 'overdue' : t.status}">${isOverdue ? 'Overdue' : esc(t.status)}</span>
                    </div>
                    ${t.due_date ? `<div class="m-card-footer">📅 Due: ${t.due_date}</div>` : ''}
                </a>`;
                }).join('') || '<div class="m-empty"><div class="m-empty-icon">✅</div><div class="m-empty-title">No open tasks</div><div class="m-empty-text">You\'re all caught up!</div></div>';
            } catch (e) { console.warn('Tasks load error', e); }

            document.getElementById('s-docs').textContent = '—';
        });

        function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    </script>
@endpush