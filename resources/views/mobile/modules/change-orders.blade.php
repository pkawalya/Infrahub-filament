@extends('mobile.layout')
@section('title', 'Change Orders — InfraHub')

@section('content')
    <div class="m-page-title">Change Orders & Variations</div>
    <div class="m-page-subtitle">Site scope variations, cost impact & approval tracking</div>

    <div style="display:flex;gap:0.4rem;margin-bottom:1rem;">
        <button class="m-pill active" onclick="switchCoTab('list')" id="tab-co-list" style="cursor:pointer;border:none;">Variations List</button>
        <button class="m-pill" onclick="switchCoTab('new')" id="tab-co-new" style="cursor:pointer;border:none;">+ Request Change</button>
    </div>

    {{-- Change Order List --}}
    <div id="view-co-list">
        <div id="co-container">
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
        </div>
    </div>

    {{-- Request Form --}}
    <div id="view-co-new" style="display:none;">
        <form class="m-card" style="display:flex;flex-direction:column;gap:0.8rem;" onsubmit="handleCoSubmit(event)">
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">VARIATION TITLE / HEADING</label>
                <input type="text" id="co-title" placeholder="e.g. Additional Retaining Wall Height +1.5m" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;">
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">ESTIMATED COST IMPACT ($)</label>
                    <input type="number" id="co-cost" placeholder="12500" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
                </div>
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">SCHEDULE IMPACT (DAYS)</label>
                    <input type="number" id="co-days" placeholder="5" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);">
                </div>
            </div>
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">REASON & TECHNICAL JUSTIFICATION</label>
                <textarea id="co-reason" rows="4" placeholder="Explain soil condition changes or client request leading to this variation..." style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required></textarea>
            </div>
            <button type="submit" class="m-btn-primary" style="margin-top:0.5rem;padding:0.75rem;border-radius:10px;font-weight:700;border:none;cursor:pointer;">
                Submit Variation Order Request
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let coList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchChangeOrders();
        });

        async function fetchChangeOrders() {
            try {
                const res = await API.get('/change-orders?per_page=30');
                if (res?.data) {
                    coList = res.data;
                    localStorage.setItem('m_cos', JSON.stringify(coList));
                }
            } catch {
                const cached = localStorage.getItem('m_cos');
                if (cached) coList = JSON.parse(cached);
            }
            renderCo();
        }

        function switchCoTab(v) {
            document.getElementById('view-co-list').style.display = v === 'list' ? 'block' : 'none';
            document.getElementById('view-co-new').style.display = v === 'new' ? 'block' : 'none';
            document.getElementById('tab-co-list').className = `m-pill ${v === 'list' ? 'active' : ''}`;
            document.getElementById('tab-co-new').className = `m-pill ${v === 'new' ? 'active' : ''}`;
        }

        function renderCo() {
            const container = document.getElementById('co-container');
            if (!coList || coList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">🔀</div>
                        <div class="m-empty-title">No Change Orders Requested</div>
                    </div>`;
                return;
            }

            container.innerHTML = coList.map(c => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">🔀 ${esc(c.title || c.reference_number)}</div>
                            <div class="m-card-subtitle">Impact: ${c.time_extension_days ? '+' + c.time_extension_days + ' Days' : 'No Schedule Delay'}</div>
                        </div>
                        <span class="m-pill ${(c.status || '').toLowerCase() === 'approved' ? 'active' : 'planning'}">${esc(c.status || 'Pending')}</span>
                    </div>
                    <div style="font-size:1.1rem;font-weight:800;color:var(--success);margin:0.4rem 0;">
                        ${c.amount ? '$' + parseFloat(c.amount).toLocaleString() : 'Cost TBD'}
                    </div>
                    <div class="m-card-footer">
                        📅 Requested: ${esc(c.created_at ? c.created_at.slice(0,10) : 'Recent')}
                    </div>
                </div>`).join('');
        }

        async function handleCoSubmit(e) {
            e.preventDefault();
            toast('Change Order Submitted ✓');
            switchCoTab('list');
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
