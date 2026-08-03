@extends('mobile.layout', ['active' => 'projects'])
@section('title', 'Project Details — InfraHub Mobile')

@section('content')
    <div style="margin-bottom:0.75rem;">
        <a href="/mobile/projects" style="color:var(--accent);font-size:0.82rem;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:0.3rem;">
            ← Back to Projects
        </a>
    </div>

    <div class="m-page-title" id="proj-name">Loading project...</div>
    <div class="m-page-subtitle" id="proj-sub"></div>

    {{-- Stats Grid --}}
    <div class="m-stats" id="proj-stats">
        <x-mobile.stat-card variant="accent" value="–" label="Tasks" id="ps-tasks" />
        <x-mobile.stat-card variant="warning" value="–" label="Documents" id="ps-docs" />
        <x-mobile.stat-card variant="danger" value="–" label="Incidents" id="ps-incidents" />
        <x-mobile.stat-card variant="success" value="–" label="RFIs" id="ps-rfis" />
    </div>

    {{-- Project Field Modules --}}
    <div class="m-section" style="margin-top:1.25rem;">
        <span class="m-section-title">Project Operational Modules</span>
    </div>
    <div class="m-actions" style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.55rem;margin-bottom:1.5rem;" id="modules">
        <a href="javascript:void(0)" class="m-action" onclick="goModule('diary')">
            <div class="m-action-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;">
                <x-mobile.icon name="diaries" size="20" />
            </div>
            <span class="m-action-label">Diaries</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('attendance')">
            <div class="m-action-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;">
                <x-mobile.icon name="attendance" size="20" />
            </div>
            <span class="m-action-label">Attendance</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('tasks')">
            <div class="m-action-icon" style="background:rgba(99,102,241,0.15);color:#a5b4fc;">
                <x-mobile.icon name="tasks" size="20" />
            </div>
            <span class="m-action-label">Tasks</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('safety')">
            <div class="m-action-icon" style="background:rgba(239,68,68,0.15);color:#f87171;">
                <x-mobile.icon name="safety" size="20" />
            </div>
            <span class="m-action-label">Safety</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('rfis')">
            <div class="m-action-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;">
                <x-mobile.icon name="rfis" size="20" />
            </div>
            <span class="m-action-label">RFIs</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('drawings')">
            <div class="m-action-icon" style="background:rgba(168,85,247,0.15);color:#c084fc;">
                <x-mobile.icon name="drawings" size="20" />
            </div>
            <span class="m-action-label">Drawings</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('equipment')">
            <div class="m-action-icon" style="background:rgba(14,165,233,0.15);color:#38bdf8;">
                <x-mobile.icon name="equipment" size="20" />
            </div>
            <span class="m-action-label">Fleet</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('boq')">
            <div class="m-action-icon" style="background:rgba(20,184,166,0.15);color:#2dd4bf;">
                <x-mobile.icon name="boq" size="20" />
            </div>
            <span class="m-action-label">BOQ</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('financials')">
            <div class="m-action-icon" style="background:rgba(16,185,129,0.15);color:#34d399;">
                <x-mobile.icon name="financials" size="20" />
            </div>
            <span class="m-action-label">Financials</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('quality')">
            <div class="m-action-icon" style="background:rgba(236,72,153,0.15);color:#f472b6;">
                <x-mobile.icon name="quality" size="20" />
            </div>
            <span class="m-action-label">QA/QC</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('materials')">
            <div class="m-action-icon" style="background:rgba(168,85,247,0.15);color:#c084fc;">
                <x-mobile.icon name="materials" size="20" />
            </div>
            <span class="m-action-label">Materials</span>
        </a>
        <a href="javascript:void(0)" class="m-action" onclick="goModule('reporting')">
            <div class="m-action-icon" style="background:rgba(99,102,241,0.15);color:#818cf8;">
                <x-mobile.icon name="reporting" size="20" />
            </div>
            <span class="m-action-label">Reporting</span>
        </a>
    </div>

    {{-- Recent Tasks --}}
    <div class="m-section">
        <span class="m-section-title">Project Tasks</span>
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
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }

            // Load project detail
            try {
                const data = await MobileAPI.get(`/projects/${projectId}`);
                if (data?.data) {
                    const p = data.data;
                    document.getElementById('proj-name').textContent = p.name;
                    document.getElementById('proj-sub').textContent =
                        [p.code, p.client?.name, p.status?.toUpperCase()].filter(Boolean).join(' · ');
                }
            } catch {
                document.getElementById('proj-name').textContent = 'Project Site';
            }

            // Load stats
            try {
                const stats = await MobileAPI.get(`/projects/${projectId}/stats`);
                if (stats?.data) {
                    const s = stats.data;
                    document.getElementById('ps-tasks').textContent = s.tasks?.total ?? '0';
                    document.getElementById('ps-docs').textContent = s.documents ?? '0';
                    document.getElementById('ps-incidents').textContent = s.safety_incidents ?? '0';
                    document.getElementById('ps-rfis').textContent = s.rfis?.total ?? '0';
                }
            } catch { }

            // Load tasks
            try {
                const td = await MobileAPI.get(`/projects/${projectId}/tasks?per_page=10`);
                if (td?.data) {
                    const now = new Date().toISOString().slice(0, 10);
                    const el = document.getElementById('task-list');
                    el.innerHTML = td.data.map(t => {
                        const overdue = t.due_date && t.due_date < now && !['done', 'cancelled'].includes(t.status);
                        const statusStr = overdue ? 'overdue' : t.status;
                        const labelStr = overdue ? 'Overdue' : t.status;
                        return `
                    <div class="m-card">
                        <div class="m-card-header">
                            <div class="m-card-title">${esc(t.title)}</div>
                            <span class="m-pill ${statusStr}"><span class="m-pill-dot"></span><span class="m-pill-text">${esc(labelStr)}</span></span>
                        </div>
                        ${t.due_date ? `<div class="m-card-footer">Due: ${t.due_date}${t.assignee?.name ? ' · Assignee: ' + esc(t.assignee.name) : ''}</div>` : ''}
                    </div>`;
                    }).join('') || '<div class="m-empty"><div class="m-empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div><div class="m-empty-title">No tasks found</div></div>';
                }
            } catch { }
        });

        function goModule(mod) {
            const map = {
                tasks: `/mobile/tasks?project_id=${projectId}`,
                drawings: `/mobile/drawings?project_id=${projectId}`,
                documents: `/mobile/drawings?project_id=${projectId}`,
                safety: `/mobile/safety?project_id=${projectId}`,
                rfis: `/mobile/rfis?project_id=${projectId}`,
                diary: `/mobile/diaries?project_id=${projectId}`,
                attendance: `/mobile/attendance?project_id=${projectId}`,
                equipment: `/mobile/equipment?project_id=${projectId}`,
                boq: `/mobile/boq?project_id=${projectId}`,
                planning: `/mobile/planning?project_id=${projectId}`,
                financials: `/mobile/financials?project_id=${projectId}`,
                subcontractors: `/mobile/subcontractors?project_id=${projectId}`,
                tenders: `/mobile/tenders?project_id=${projectId}`,
                materials: `/mobile/materials?project_id=${projectId}`,
                'change-orders': `/mobile/change-orders?project_id=${projectId}`,
                'work-orders': `/mobile/work-orders?project_id=${projectId}`,
                quality: `/mobile/quality?project_id=${projectId}`,
                approvals: `/mobile/approvals?project_id=${projectId}`,
                reporting: `/mobile/reporting?project_id=${projectId}`,
                suggestions: `/mobile/suggestions?project_id=${projectId}`,
            };
            const targetUrl = map[mod] || `/mobile/projects/${projectId}`;
            window.location.href = targetUrl;
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush