@extends('mobile.layout', ['active' => 'projects'])
@section('title', 'Projects — InfraHub Mobile')

@section('content')
    <div class="m-page-title">Projects Directory</div>
    <div class="m-page-subtitle">Company capital works & active sites</div>

    {{-- Search --}}
    <div class="m-search-box">
        <svg class="m-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <input type="search" class="m-search-input" id="project-search" placeholder="Search by name, code, or client..." oninput="filterProjects()">
    </div>

    {{-- Filter tabs --}}
    <div class="m-category-tabs" id="status-filters">
        <button type="button" class="m-category-tab active" onclick="setFilter('all', this)" data-filter="all">All Projects</button>
        <button type="button" class="m-category-tab" onclick="setFilter('active', this)" data-filter="active">Active Sites</button>
        <button type="button" class="m-category-tab" onclick="setFilter('on_hold', this)" data-filter="on_hold">On Hold</button>
        <button type="button" class="m-category-tab" onclick="setFilter('completed', this)" data-filter="completed">Completed</button>
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
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }

            try {
                const data = await MobileAPI.get('/projects?per_page=100');
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

        function setFilter(f, btn) {
            currentFilter = f;
            document.querySelectorAll('#status-filters button').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
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
                    <span class="m-pill ${p.status}"><span class="m-pill-dot"></span><span class="m-pill-text">${esc(p.status)}</span></span>
                </div>
                ${p.manager?.name ? `<div class="m-card-footer">Project Manager: ${esc(p.manager.name)}</div>` : ''}
            </a>
        `).join('') || `<div class="m-empty"><div class="m-empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg></div><div class="m-empty-title">No projects found</div></div>`;
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    </script>
@endpush