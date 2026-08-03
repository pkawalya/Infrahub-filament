@extends('mobile.layout')
@section('title', 'Planning & Progress — InfraHub')

@section('content')
    <div class="m-page-title">Planning & Milestones</div>
    <div class="m-page-subtitle">Schedule tracking, Gantt milestones & site progress</div>

    <div id="planning-container">
        <div class="m-card"><div class="m-skeleton" style="height:75px;"></div></div>
        <div class="m-card"><div class="m-skeleton" style="height:75px;"></div></div>
    </div>
@endsection

@push('scripts')
    <script>
        let milestonesList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchPlanning();
        });

        async function fetchPlanning() {
            try {
                const res = await API.get('/project-milestones?per_page=30');
                if (res?.data) milestonesList = res.data;
                localStorage.setItem('m_milestones', JSON.stringify(milestonesList));
            } catch {
                const cached = localStorage.getItem('m_milestones');
                if (cached) milestonesList = JSON.parse(cached);
            }
            renderPlanning();
        }

        function renderPlanning() {
            const container = document.getElementById('planning-container');

            if (!milestonesList || milestonesList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">📅</div>
                        <div class="m-empty-title">No Milestones Found</div>
                        <div class="m-empty-text">Project schedule & Gantt progress milestones will appear here.</div>
                    </div>`;
                return;
            }

            container.innerHTML = milestonesList.map(item => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">🚩 ${esc(item.title || item.name || 'Schedule Milestone')}</div>
                            <div class="m-card-subtitle">${esc(item.cde_project?.name || item.project_name || 'Site Progress')}</div>
                        </div>
                        <span class="m-pill ${item.progress_percent >= 100 ? 'done' : 'in_progress'}">${esc(item.status || (item.progress_percent >= 100 ? 'Completed' : 'On Track'))}</span>
                    </div>
                    <div style="margin:0.6rem 0;">
                        <div style="display:flex;justify-content:space-between;font-size:0.75rem;margin-bottom:0.25rem;color:var(--text-muted);">
                            <span>Target Completion</span>
                            <span><strong>${item.progress_percent || 0}% Complete</strong></span>
                        </div>
                        <div class="m-progress">
                            <div class="m-progress-bar" style="width:${item.progress_percent || 0}%;"></div>
                        </div>
                    </div>
                    <div class="m-card-footer">
                        📅 Due: ${esc(item.due_date || item.target_date ? (item.due_date || item.target_date).slice(0,10) : 'Upcoming')}
                        ${item.owner ? ' · 👤 Owner: ' + esc(item.owner.name) : ''}
                    </div>
                </div>`).join('');
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
