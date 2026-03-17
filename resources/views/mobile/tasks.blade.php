@extends('mobile.layout')
@section('title', 'My Tasks — InfraHub')

@section('content')
    <div class="m-page-title">My Tasks</div>
    <div class="m-page-subtitle">Tasks assigned to you</div>

    <div style="display:flex;gap:0.4rem;flex-wrap:wrap;margin-bottom:1rem;">
        <button class="m-pill active" onclick="setFilter('open')" data-f="open" id="f-open"
            style="cursor:pointer;border:none;outline:2px solid var(--accent);">Open</button>
        <button class="m-pill done" onclick="setFilter('done')" data-f="done" id="f-done"
            style="cursor:pointer;border:none;">Done</button>
        <button class="m-pill overdue" onclick="setFilter('overdue')" data-f="overdue" id="f-overdue"
            style="cursor:pointer;border:none;">Overdue</button>
    </div>

    <div id="task-list">
        <div class="m-card">
            <div class="m-skeleton" style="height:60px;"></div>
        </div>
        <div class="m-card">
            <div class="m-skeleton" style="height:60px;"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let allTasks = [];
        let taskFilter = 'open';

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }

            try {
                const cached = localStorage.getItem('m_projects');
                const projects = cached ? JSON.parse(cached) : [];

                for (const p of projects.slice(0, 10)) {
                    try {
                        const td = await API.get(`/projects/${p.id}/tasks?per_page=50`);
                        if (td?.data) {
                            td.data.forEach(t => { t._project = p.name; t._pid = p.id; });
                            allTasks = allTasks.concat(td.data);
                        }
                    } catch { }
                }

                localStorage.setItem('m_tasks', JSON.stringify(allTasks));
            } catch {
                const cached = localStorage.getItem('m_tasks');
                if (cached) allTasks = JSON.parse(cached);
            }

            renderTasks();
        });

        function setFilter(f) {
            taskFilter = f;
            document.querySelectorAll('[data-f]').forEach(b => b.style.outline = 'none');
            document.getElementById('f-' + f).style.outline = '2px solid var(--accent)';
            renderTasks();
        }

        function renderTasks() {
            const now = new Date().toISOString().slice(0, 10);
            let filtered;

            if (taskFilter === 'open') {
                filtered = allTasks.filter(t => !['done', 'cancelled'].includes(t.status));
            } else if (taskFilter === 'done') {
                filtered = allTasks.filter(t => t.status === 'done');
            } else {
                filtered = allTasks.filter(t => t.due_date && t.due_date < now && !['done', 'cancelled'].includes(t.status));
            }

            filtered.sort((a, b) => (a.due_date || '9999') > (b.due_date || '9999') ? 1 : -1);

            const el = document.getElementById('task-list');
            el.innerHTML = filtered.map(t => {
                const overdue = t.due_date && t.due_date < now && !['done', 'cancelled'].includes(t.status);
                return `
            <div class="m-card">
                <div class="m-card-header">
                    <div>
                        <div class="m-card-title">${esc(t.title)}</div>
                        <div class="m-card-subtitle">${esc(t._project || '')}</div>
                    </div>
                    <span class="m-pill ${overdue ? 'overdue' : t.status}">${overdue ? 'Overdue' : esc(t.status)}</span>
                </div>
                <div class="m-card-footer">
                    ${t.due_date ? '📅 ' + t.due_date : ''}
                    ${t.assignee?.name ? ' · 👤 ' + esc(t.assignee.name) : ''}
                    ${t.priority ? ' · ⚡ ' + esc(t.priority) : ''}
                </div>
            </div>`;
            }).join('') || `<div class="m-empty"><div class="m-empty-icon">${taskFilter === 'done' ? '🎉' : '✅'}</div><div class="m-empty-title">${taskFilter === 'done' ? 'No completed tasks' : 'No tasks found'}</div></div>`;
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    </script>
@endpush