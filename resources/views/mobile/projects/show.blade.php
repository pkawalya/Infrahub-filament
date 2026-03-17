@extends('mobile.layout')
@section('title', 'Project — InfraHub')

@section('content')
    <div style="margin-bottom:0.5rem;">
        <a href="/mobile/projects" style="color:var(--accent);font-size:0.78rem;text-decoration:none;">← Back to
            Projects</a>
    </div>

    <div class="m-page-title" id="proj-name">Loading...</div>
    <div class="m-page-subtitle" id="proj-sub"></div>

    {{-- Stats --}}
    <div class="m-stats" id="proj-stats">
        <div class="m-stat accent">
            <div class="m-stat-value" id="ps-tasks">–</div>
            <div class="m-stat-label">Tasks</div>
        </div>
        <div class="m-stat warning">
            <div class="m-stat-value" id="ps-docs">–</div>
            <div class="m-stat-label">Documents</div>
        </div>
        <div class="m-stat danger">
            <div class="m-stat-value" id="ps-incidents">–</div>
            <div class="m-stat-label">Incidents</div>
        </div>
        <div class="m-stat success">
            <div class="m-stat-value" id="ps-rfis">–</div>
            <div class="m-stat-label">RFIs</div>
        </div>
    </div>

    {{-- Module Shortcuts --}}
    <div class="m-section"><span class="m-section-title">Modules</span></div>
    <div class="m-actions" style="grid-template-columns:repeat(3,1fr);margin-bottom:1.5rem;" id="modules">
        <a href="#" class="m-action" onclick="goModule('tasks')">
            <div class="m-action-icon" style="background:rgba(99,102,241,0.15);color:#a5b4fc;">✅</div>
            <span class="m-action-label">Tasks</span>
        </a>
        <a href="#" class="m-action" onclick="goModule('documents')">
            <div class="m-action-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;">📄</div>
            <span class="m-action-label">Documents</span>
        </a>
        <a href="#" class="m-action" onclick="goModule('safety')">
            <div class="m-action-icon" style="background:rgba(239,68,68,0.15);color:#f87171;">⚠️</div>
            <span class="m-action-label">Safety</span>
        </a>
        <a href="#" class="m-action" onclick="goModule('rfis')">
            <div class="m-action-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;">📝</div>
            <span class="m-action-label">RFIs</span>
        </a>
        <a href="#" class="m-action" onclick="goModule('diary')">
            <div class="m-action-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;">📋</div>
            <span class="m-action-label">Site Diary</span>
        </a>
        <a href="#" class="m-action" onclick="goModule('suggestions')">
            <div class="m-action-icon" style="background:rgba(251,191,36,0.15);color:#fbbf24;">💡</div>
            <span class="m-action-label">Suggestions</span>
        </a>
    </div>

    {{-- Recent Tasks --}}
    <div class="m-section">
        <span class="m-section-title">Recent Tasks</span>
    </div>
    <div id="task-list">
        <div class="m-card">
            <div class="m-skeleton" style="height:60px;"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const projectId = {{ $id }};

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }

            // Load project detail
            try {
                const data = await API.get(`/projects/${projectId}`);
                if (data?.data) {
                    const p = data.data;
                    document.getElementById('proj-name').textContent = p.name;
                    document.getElementById('proj-sub').textContent =
                        [p.code, p.client?.name, p.status?.toUpperCase()].filter(Boolean).join(' · ');
                }
            } catch {
                document.getElementById('proj-name').textContent = 'Project';
            }

            // Load stats
            try {
                const stats = await API.get(`/projects/${projectId}/stats`);
                if (stats?.data) {
                    const s = stats.data;
                    document.getElementById('ps-tasks').textContent = s.tasks?.total ?? '–';
                    document.getElementById('ps-docs').textContent = s.documents ?? '–';
                    document.getElementById('ps-incidents').textContent = s.safety_incidents ?? '–';
                    document.getElementById('ps-rfis').textContent = s.rfis?.total ?? '–';
                }
            } catch { }

            // Load tasks
            try {
                const td = await API.get(`/projects/${projectId}/tasks?per_page=10`);
                if (td?.data) {
                    const now = new Date().toISOString().slice(0, 10);
                    const el = document.getElementById('task-list');
                    el.innerHTML = td.data.map(t => {
                        const overdue = t.due_date && t.due_date < now && !['done', 'cancelled'].includes(t.status);
                        return `
                    <div class="m-card">
                        <div class="m-card-header">
                            <div class="m-card-title">${esc(t.title)}</div>
                            <span class="m-pill ${overdue ? 'overdue' : t.status}">${overdue ? 'Overdue' : esc(t.status)}</span>
                        </div>
                        ${t.due_date ? `<div class="m-card-footer">📅 ${t.due_date}${t.assignee?.name ? ' · 👤 ' + esc(t.assignee.name) : ''}</div>` : ''}
                    </div>`;
                    }).join('') || '<div class="m-empty"><div class="m-empty-icon">✅</div><div class="m-empty-title">No tasks</div></div>';
                }
            } catch { }
        });

        function goModule(mod) {
            const base = `/app/cde-projects/${projectId}`;
            const map = {
                tasks: base + '/tasks',
                documents: base + '/documents',
                safety: base + '/sheq',
                rfis: base + '/rfi-submittals',
                diary: base + '/field-management',
                suggestions: base + '/suggestions',
            };
            window.location.href = map[mod] || base;
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    </script>
@endpush