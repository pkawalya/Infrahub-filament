@extends('mobile.layout')
@section('title', 'Document Management & CDE Drawings — InfraHub Mobile')

@section('content')
    <div class="m-page-title" style="display:flex;align-items:center;gap:0.5rem;">
        <x-mobile.icon name="drawings" size="24" class="text-purple-400" /> CDE Drawings & Documents
    </div>
    <div class="m-page-subtitle">Blueprints, specifications & Common Data Environment docs</div>

    <div style="margin-bottom:0.85rem;">
        <input type="text" id="drawing-search" oninput="filterDrawings()" placeholder="Search document number, discipline, or title..." class="m-input" style="padding-left:2.4rem;">
    </div>

    <div class="m-category-tabs">
        <button type="button" class="m-category-tab active" onclick="switchDocTab('all')" id="tab-doc-all">All Docs</button>
        <button type="button" class="m-category-tab" onclick="switchDocTab('architectural')" id="tab-doc-architectural">Architectural</button>
        <button type="button" class="m-category-tab" onclick="switchDocTab('structural')" id="tab-doc-structural">Structural</button>
        <button type="button" class="m-category-tab" onclick="switchDocTab('mep')" id="tab-doc-mep">MEP</button>
        <button type="button" class="m-category-tab" onclick="switchDocTab('specs')" id="tab-doc-specs">Specs & Reports</button>
    </div>

    <div id="drawings-container">
        <div class="m-card"><div class="m-skeleton" style="height:75px;"></div></div>
        <div class="m-card"><div class="m-skeleton" style="height:75px;"></div></div>
    </div>
@endsection

@push('scripts')
    <script>
        let drawingsList = [];
        let activeDocTab = 'all';
        const urlParams = new URLSearchParams(window.location.search);
        const filterProjectId = urlParams.get('project_id');

        document.addEventListener('DOMContentLoaded', async () => {
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchDrawings();
        });

        async function fetchDrawings() {
            try {
                let endpoint = '/drawings?per_page=50';
                if (filterProjectId) {
                    endpoint += `&project_id=${filterProjectId}`;
                }
                const res = await MobileAPI.get(endpoint);
                if (res?.data) {
                    drawingsList = res.data;
                    localStorage.setItem('m_drawings', JSON.stringify(drawingsList));
                }
            } catch {
                const cached = localStorage.getItem('m_drawings');
                if (cached) drawingsList = JSON.parse(cached);
            }
            filterDrawings();
        }

        function switchDocTab(tab) {
            activeDocTab = tab;
            document.querySelectorAll('.m-category-tabs button').forEach(b => b.classList.remove('active'));
            const btn = document.getElementById(`tab-doc-${tab}`);
            if (btn) btn.classList.add('active');
            filterDrawings();
        }

        function filterDrawings() {
            const query = (document.getElementById('drawing-search')?.value || '').toLowerCase().trim();
            const filtered = drawingsList.filter(d => {
                const docNum = (d.document_number || d.drawing_number || '').toLowerCase();
                const title = (d.title || '').toLowerCase();
                const disc = (d.discipline || '').toLowerCase();

                const matchesQuery = !query || docNum.includes(query) || title.includes(query) || disc.includes(query);
                if (!matchesQuery) return false;

                if (activeDocTab === 'all') return true;
                if (activeDocTab === 'architectural') return disc.includes('arch');
                if (activeDocTab === 'structural') return disc.includes('struct');
                if (activeDocTab === 'mep') return disc.includes('mep') || disc.includes('elec') || disc.includes('plumb');
                if (activeDocTab === 'specs') return disc.includes('spec') || disc.includes('report') || disc.includes('cde');
                return true;
            });
            renderDrawings(filtered);
        }

        function renderDrawings(list) {
            const container = document.getElementById('drawings-container');
            if (!list || list.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg></div>
                        <div class="m-empty-title">No Documents Found</div>
                        <div class="m-empty-text">CDE blueprints & specification documents for active projects will appear here.</div>
                    </div>`;
                return;
            }

            container.innerHTML = list.map(d => {
                const st = (d.status || 'wip').toLowerCase();
                const pillStatus = st === 'approved' || st === 'published' ? 'done' : (st === 'under_review' ? 'active' : 'planning');
                return `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">${esc(d.document_number || d.drawing_number || 'DOC-' + d.id)} — ${esc(d.title || 'Project Document')}</div>
                            <div class="m-card-subtitle">Discipline: ${esc(d.discipline || 'General')} · Revision ${esc(d.revision || 'A')} ${d.project?.name ? '· ' + esc(d.project.name) : ''}</div>
                        </div>
                        <span class="m-pill ${pillStatus}"><span class="m-pill-dot"></span><span class="m-pill-text">${esc(d.status ? d.status.toUpperCase() : 'WIP')}</span></span>
                    </div>
                    ${d.description ? `<div style="font-size:0.82rem;color:var(--text-muted);margin:0.4rem 0;line-height:1.4;">${esc(d.description)}</div>` : ''}
                    <div class="m-card-footer" style="margin-top:0.5rem;display:flex;justify-content:space-between;align-items:center;">
                        <span>Uploaded: ${esc(d.updated_at ? d.updated_at.slice(0,10) : 'Recent')}</span>
                        <span class="m-pill active"><span class="m-pill-dot"></span><span class="m-pill-text">ISO 19650 CDE</span></span>
                    </div>
                </div>`;
            }).join('');
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
