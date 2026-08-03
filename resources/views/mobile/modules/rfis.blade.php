@extends('mobile.layout')
@section('title', 'RFIs & Submittals — InfraHub Mobile')

@section('content')
    <div class="m-page-title" style="display:flex;align-items:center;gap:0.5rem;">
        <x-mobile.icon name="rfis" size="24" class="text-amber-400" /> RFIs & Submittals
    </div>
    <div class="m-page-subtitle">Field technical queries & engineering submittals</div>

    <div class="m-category-tabs">
        <button type="button" class="m-category-tab active" onclick="switchRfiTab('list')" id="tab-rfi-list">RFI Registry</button>
        <button type="button" class="m-category-tab" onclick="switchRfiTab('new')" id="tab-rfi-new">+ Create RFI</button>
    </div>

    {{-- RFI List View --}}
    <div id="view-rfi-list">
        <div id="rfi-container">
            <div class="m-card"><div class="m-skeleton" style="height:70px;"></div></div>
            <div class="m-card"><div class="m-skeleton" style="height:70px;"></div></div>
        </div>
    </div>

    {{-- Create RFI View --}}
    <div id="view-rfi-new" style="display:none;">
        <form id="rfi-form" class="m-card" style="display:flex;flex-direction:column;gap:0.8rem;" onsubmit="handleRfiSubmit(event)">
            <div class="m-form-group">
                <label class="m-label">RFI Subject / Query *</label>
                <input type="text" id="rfi-subject" placeholder="e.g. Foundation rebar spacing query..." class="m-input" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Priority *</label>
                    <select id="rfi-priority" class="m-select" required>
                        <option value="medium">Medium (3-5 days)</option>
                        <option value="high">High (24-48 hours)</option>
                        <option value="urgent">Urgent (Stop Work)</option>
                    </select>
                </div>
                <div class="m-form-group">
                    <label class="m-label">Discipline</label>
                    <select id="rfi-discipline" class="m-select">
                        <option value="Structural">Structural</option>
                        <option value="Architectural">Architectural</option>
                        <option value="MEP">MEP / Electrical</option>
                        <option value="Geotechnical">Geotechnical</option>
                    </select>
                </div>
            </div>
            <div class="m-form-group">
                <label class="m-label">Drawing Reference / Sheet #</label>
                <input type="text" id="rfi-drawing" placeholder="e.g. DWG-ST-04 Rev B" class="m-input">
            </div>
            <div class="m-form-group">
                <label class="m-label">Technical Question Details *</label>
                <textarea id="rfi-question" class="m-textarea" rows="4" placeholder="Detail the discrepancy or clarification requested from consultant..." required></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-primary">
                Submit RFI Technical Query
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let rfisList = [];
        const urlParams = new URLSearchParams(window.location.search);
        const filterProjectId = urlParams.get('project_id');

        document.addEventListener('DOMContentLoaded', async () => {
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchRfis();
        });

        async function fetchRfis() {
            try {
                let endpoint = '/rfis?per_page=50';
                if (filterProjectId) {
                    endpoint += `&project_id=${filterProjectId}`;
                }
                const res = await MobileAPI.get(endpoint);
                if (res?.data) {
                    rfisList = res.data;
                    localStorage.setItem('m_rfis', JSON.stringify(rfisList));
                }
            } catch {
                const cached = localStorage.getItem('m_rfis');
                if (cached) rfisList = JSON.parse(cached);
            }
            renderRfis();
        }

        function switchRfiTab(v) {
            document.getElementById('view-rfi-list').style.display = v === 'list' ? 'block' : 'none';
            document.getElementById('view-rfi-new').style.display = v === 'new' ? 'block' : 'none';
            document.querySelectorAll('.m-category-tabs button').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-rfi-' + v).classList.add('active');
        }

        function renderRfis() {
            const container = document.getElementById('rfi-container');
            if (!rfisList || rfisList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg></div>
                        <div class="m-empty-title">No RFIs Found</div>
                    </div>`;
                return;
            }

            container.innerHTML = rfisList.map(r => {
                const isUrgent = (r.priority || '').toLowerCase() === 'urgent' || (r.priority || '').toLowerCase() === 'high';
                const pillStatus = isUrgent ? 'overdue' : 'active';
                return `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">${esc(r.rfi_number || 'RFI')} — ${esc(r.subject || r.title)}</div>
                            <div class="m-card-subtitle">${esc(r.discipline || 'Engineering')} ${r.drawing_number ? '· ' + esc(r.drawing_number) : ''} ${r.project?.name ? '· ' + esc(r.project.name) : ''}</div>
                        </div>
                        <span class="m-pill ${pillStatus}"><span class="m-pill-dot"></span><span class="m-pill-text">${esc(r.status || r.priority || 'Open')}</span></span>
                    </div>
                    <div style="font-size:0.82rem;color:var(--text-muted);margin:0.5rem 0;line-height:1.4;">
                        ${esc(r.question || r.description || '')}
                    </div>
                    <div class="m-card-footer">
                        Date: ${esc(r.created_at ? r.created_at.slice(0,10) : 'Recent')}
                        ${r.raised_by?.name ? ' · Raised by: ' + esc(r.raised_by.name) : ''}
                    </div>
                </div>`;
            }).join('');
        }

        async function handleRfiSubmit(e) {
            e.preventDefault();
            const payload = {
                subject: document.getElementById('rfi-subject').value,
                priority: document.getElementById('rfi-priority').value,
                discipline: document.getElementById('rfi-discipline').value,
                drawing_number: document.getElementById('rfi-drawing').value,
                question: document.getElementById('rfi-question').value,
                status: 'open',
                cde_project_id: filterProjectId || null,
            };

            try {
                const res = await MobileAPI.post('/rfis', payload);
                if (res) {
                    MobileUI.toast('RFI Technical Query Submitted ✓');
                    switchRfiTab('list');
                    fetchRfis();
                }
            } catch {
                MobileUI.toast('RFI queued for sync', 'info');
                switchRfiTab('list');
            }
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
