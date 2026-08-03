@extends('mobile.layout')
@section('title', 'QA/QC Inspection — InfraHub')

@section('content')
    <div class="m-page-title">QA/QC & Punch List</div>
    <div class="m-page-subtitle">Quality inspection logs & defect punch items</div>

    <div style="display:flex;gap:0.4rem;margin-bottom:1rem;">
        <button class="m-pill active" onclick="switchQaTab('list')" id="tab-qa-list" style="cursor:pointer;border:none;">Punch Items</button>
        <button class="m-pill" onclick="switchQaTab('new')" id="tab-qa-new" style="cursor:pointer;border:none;">+ Log Defect</button>
    </div>

    {{-- Punch List View --}}
    <div id="view-qa-list">
        <div id="qa-container">
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
        </div>
    </div>

    {{-- Log Defect View --}}
    <div id="view-qa-new" style="display:none;">
        <form class="m-card" style="display:flex;flex-direction:column;gap:0.8rem;" onsubmit="handleQaSubmit(event)">
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">DEFECT TITLE / OBSERVATION</label>
                <input type="text" id="qa-title" placeholder="e.g. Concrete Honeycombing on Column C3" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;">
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">LOCATION / ZONE</label>
                    <input type="text" id="qa-zone" placeholder="Level 2 - West Grid" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
                </div>
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">RESPONSIBLE TRADE</label>
                    <select id="qa-trade" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);">
                        <option value="Concrete Works">Concrete Works</option>
                        <option value="Masonry">Masonry</option>
                        <option value="Plumbing">Plumbing</option>
                        <option value="Electrical">Electrical</option>
                        <option value="Finishes">Finishes / Paint</option>
                    </select>
                </div>
            </div>
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">CORRECTIVE ACTION REQUIRED</label>
                <textarea id="qa-action" rows="4" placeholder="Chipping, epoxy grout injection, and non-shrink mortar patching..." style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required></textarea>
            </div>
            <button type="submit" class="m-btn-primary" style="margin-top:0.5rem;padding:0.75rem;border-radius:10px;font-weight:700;border:none;cursor:pointer;">
                Log Punch List Item
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let qaList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchQualityItems();
        });

        async function fetchQualityItems() {
            try {
                const res = await API.get('/quality?per_page=30');
                if (res?.data) {
                    qaList = res.data;
                    localStorage.setItem('m_qas', JSON.stringify(qaList));
                }
            } catch {
                const cached = localStorage.getItem('m_qas');
                if (cached) qaList = JSON.parse(cached);
            }
            renderQa();
        }

        function switchQaTab(v) {
            document.getElementById('view-qa-list').style.display = v === 'list' ? 'block' : 'none';
            document.getElementById('view-qa-new').style.display = v === 'new' ? 'block' : 'none';
            document.getElementById('tab-qa-list').className = `m-pill ${v === 'list' ? 'active' : ''}`;
            document.getElementById('tab-qa-new').className = `m-pill ${v === 'new' ? 'active' : ''}`;
        }

        function renderQa() {
            const container = document.getElementById('qa-container');
            if (!qaList || qaList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">🔍</div>
                        <div class="m-empty-title">No Punch List Defect Logged</div>
                    </div>`;
                return;
            }

            container.innerHTML = qaList.map(q => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">🔍 ${esc(q.title || q.defect_name)}</div>
                            <div class="m-card-subtitle">📍 ${esc(q.location || q.zone)} · Trade: ${esc(q.trade || 'General')}</div>
                        </div>
                        <span class="m-pill ${(q.status || '').toLowerCase() === 'rectified' ? 'active' : 'overdue'}">${esc(q.status || 'Open Defect')}</span>
                    </div>
                    <div style="font-size:0.85rem;color:var(--text-dim);margin:0.4rem 0;">
                        Action: ${esc(q.corrective_action || q.description || 'Pending Rectification')}
                    </div>
                </div>`).join('');
        }

        async function handleQaSubmit(e) {
            e.preventDefault();
            toast('Punch Item Logged ✓');
            switchQaTab('list');
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
