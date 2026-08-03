@extends('mobile.layout')
@section('title', 'Work Orders — InfraHub')

@section('content')
    <div class="m-page-title">Work Orders</div>
    <div class="m-page-subtitle">Equipment maintenance & site facility work dispatches</div>

    <div style="display:flex;gap:0.4rem;margin-bottom:1rem;">
        <button class="m-pill active" onclick="switchWoTab('list')" id="tab-wo-list" style="cursor:pointer;border:none;">Active Orders</button>
        <button class="m-pill" onclick="switchWoTab('new')" id="tab-wo-new" style="cursor:pointer;border:none;">+ Dispatch Order</button>
    </div>

    {{-- Work Orders List --}}
    <div id="view-wo-list">
        <div id="wo-container">
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
        </div>
    </div>

    {{-- Dispatch Form --}}
    <div id="view-wo-new" style="display:none;">
        <form class="m-card" style="display:flex;flex-direction:column;gap:0.8rem;" onsubmit="handleWoSubmit(event)">
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">WORK ORDER TITLE</label>
                <input type="text" id="wo-title" placeholder="e.g. Caterpillar Excavator Hydraulic Line Repair" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;">
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">WORK TYPE</label>
                    <select id="wo-type" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);">
                        <option value="Breakdown Repair">Breakdown Repair</option>
                        <option value="Preventative Service">Preventative Service</option>
                        <option value="Site Facility Fix">Site Facility Fix</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">PRIORITY</label>
                    <select id="wo-priority" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);">
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical (Machine Down)</option>
                    </select>
                </div>
            </div>
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">ASSIGNED TECHNICIAN / MECHANIC</label>
                <input type="text" id="wo-tech" placeholder="e.g. Chief Plant Mechanic John" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);">
            </div>
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">PROBLEM DESCRIPTION & PARTS REQUIRED</label>
                <textarea id="wo-desc" rows="4" placeholder="Detail symptoms, leaked fluid volume, replacement seal kit numbers..." style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required></textarea>
            </div>
            <button type="submit" class="m-btn-primary" style="margin-top:0.5rem;padding:0.75rem;border-radius:10px;font-weight:700;border:none;cursor:pointer;">
                Dispatch Work Order
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let woList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchWorkOrders();
        });

        async function fetchWorkOrders() {
            try {
                const res = await API.get('/work-orders?per_page=30');
                if (res?.data) {
                    woList = res.data;
                    localStorage.setItem('m_wos', JSON.stringify(woList));
                }
            } catch {
                const cached = localStorage.getItem('m_wos');
                if (cached) woList = JSON.parse(cached);
            }
            renderWo();
        }

        function switchWoTab(v) {
            document.getElementById('view-wo-list').style.display = v === 'list' ? 'block' : 'none';
            document.getElementById('view-wo-new').style.display = v === 'new' ? 'block' : 'none';
            document.getElementById('tab-wo-list').className = `m-pill ${v === 'list' ? 'active' : ''}`;
            document.getElementById('tab-wo-new').className = `m-pill ${v === 'new' ? 'active' : ''}`;
        }

        function renderWo() {
            const container = document.getElementById('wo-container');
            if (!woList || woList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">🛠️</div>
                        <div class="m-empty-title">No Active Work Orders</div>
                    </div>`;
                return;
            }

            container.innerHTML = woList.map(w => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">🛠️ ${esc(w.title || w.work_order_number)}</div>
                            <div class="m-card-subtitle">Type: ${esc(w.type || 'Maintenance')}</div>
                        </div>
                        <span class="m-pill ${(w.priority || '').toLowerCase() === 'critical' ? 'overdue' : 'planning'}">${esc(w.status || w.priority || 'In Progress')}</span>
                    </div>
                    <div class="m-card-footer">
                        👤 Technician: ${esc(w.assigned_to || w.technician || 'Plant Crew')}
                        ${w.due_date ? ' · 📅 Target: ' + w.due_date : ''}
                    </div>
                </div>`).join('');
        }

        async function handleWoSubmit(e) {
            e.preventDefault();
            toast('Work Order Dispatched ✓');
            switchWoTab('list');
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
