@extends('mobile.layout', ['active' => 'tasks'])
@section('title', 'My Tasks — InfraHub Mobile')

@section('content')
    <div class="m-page-title">My Action Items</div>
    <div class="m-page-subtitle">Field tasks & assignments</div>

    <div class="m-category-tabs" id="task-filters">
        <button type="button" class="m-category-tab active" onclick="setFilter('open', this)">Open Tasks</button>
        <button type="button" class="m-category-tab" onclick="setFilter('overdue', this)">Overdue</button>
        <button type="button" class="m-category-tab" onclick="setFilter('done', this)">Completed</button>
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
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }

            try {
                const cached = localStorage.getItem('m_projects');
                const projects = cached ? JSON.parse(cached) : [];

                for (const p of projects.slice(0, 10)) {
                    try {
                        const td = await MobileAPI.get(`/projects/${p.id}/tasks?per_page=50`);
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

        function setFilter(f, btn) {
            taskFilter = f;
            document.querySelectorAll('#task-filters button').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
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
                const statusStr = overdue ? 'overdue' : t.status;
                const labelStr = overdue ? 'Overdue' : t.status;
                return `
            <div class="m-card">
                <div class="m-card-header">
                    <div>
                        <div class="m-card-title">${esc(t.title)}</div>
                        <div class="m-card-subtitle">${esc(t._project || '')}</div>
                    </div>
                    <span class="m-pill ${statusStr}"><span class="m-pill-dot"></span><span class="m-pill-text">${esc(labelStr)}</span></span>
                </div>
                <div class="m-card-footer">
                    ${t.due_date ? 'Due: ' + t.due_date : ''}
                    ${t.assignee?.name ? ' · Assignee: ' + esc(t.assignee.name) : ''}
                    ${t.priority ? ' · Priority: ' + esc(t.priority) : ''}
                </div>
            </div>`;
            }).join('') || `<div class="m-empty"><div class="m-empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div><div class="m-empty-title">${taskFilter === 'done' ? 'No completed tasks' : 'No tasks found'}</div></div>`;
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    </script>
@endpush