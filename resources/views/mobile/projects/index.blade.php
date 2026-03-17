@extends('mobile.layout')
@section('title', 'Projects — InfraHub')

@section('content')
    <div class="m-page-title">Projects</div>
    <div class="m-page-subtitle">Your company projects</div>

    {{-- Search --}}
    <div class="m-form-group" style="margin-bottom:1rem;">
        <input type="search" class="m-input" id="project-search" placeholder="🔍 Search projects..."
            oninput="filterProjects()">
    </div>

    {{-- Filter pills --}}
    <div style="display:flex; gap:0.4rem; flex-wrap:wrap; margin-bottom:1rem;" id="status-filters">
        <button class="m-pill active" onclick="setFilter('all')" data-filter="all"
            style="cursor:pointer;border:none;">All</button>
        <button class="m-pill planning" onclick="setFilter('active')" data-filter="active"
            style="cursor:pointer;border:none;">Active</button>
        <button class="m-pill on_hold" onclick="setFilter('on_hold')" data-filter="on_hold"
            style="cursor:pointer;border:none;">On Hold</button>
        <button class="m-pill completed" onclick="setFilter('completed')" data-filter="completed"
            style="cursor:pointer;border:none;">Completed</button>
    </div>

    <div id="project-list">
        <div class="m-card">
            <div class="m-skeleton" style="height:70px;"></div>
        </div>
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
        let allProjects = [];
        let currentFilter = 'all';

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }

            try {
                const data = await API.get('/projects?per_page=100');
                if (data?.data) {
                    allProjects = data.data;
                    localStorage.setItem('m_projects', JSON.stringify(allProjects));
                }
            } catch {
                const cached = localStorage.getItem('m_projects');
                if (cached) allProjects = JSON.parse(cached);
            }

            renderProjects();
        });

        function setFilter(f) {
            currentFilter = f;
            document.querySelectorAll('#status-filters button').forEach(b => {
                b.style.outline = b.dataset.filter === f ? '2px solid var(--accent)' : 'none';
            });
            renderProjects();
        }

        function filterProjects() { renderProjects(); }

        function renderProjects() {
            const search = (document.getElementById('project-search')?.value || '').toLowerCase();
            let filtered = allProjects;

            if (currentFilter !== 'all') filtered = filtered.filter(p => p.status === currentFilter);
            if (search) filtered = filtered.filter(p =>
                (p.name || '').toLowerCase().includes(search) ||
                (p.code || '').toLowerCase().includes(search) ||
                (p.client?.name || '').toLowerCase().includes(search)
            );

            const el = document.getElementById('project-list');
            el.innerHTML = filtered.map(p => `
            <a href="/mobile/projects/${p.id}" class="m-card">
                <div class="m-card-header">
                    <div>
                        <div class="m-card-title">${esc(p.name)}</div>
                        <div class="m-card-subtitle">${esc(p.code || '')}${p.client?.name ? ' · ' + esc(p.client.name) : ''}</div>
                    </div>
                    <span class="m-pill ${p.status}">${esc(p.status)}</span>
                </div>
                ${p.manager?.name ? `<div class="m-card-footer">👤 ${esc(p.manager.name)}</div>` : ''}
            </a>
        `).join('') || `<div class="m-empty"><div class="m-empty-icon">📁</div><div class="m-empty-title">No projects found</div></div>`;
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    </script>
@endpush