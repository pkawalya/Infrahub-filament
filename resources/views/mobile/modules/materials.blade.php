@extends('mobile.layout')
@section('title', 'Materials & Inventory — InfraHub')

@section('content')
    <div class="m-page-title">Materials & Inventory</div>
    <div class="m-page-subtitle">Site stock requisitions & gate delivery logs</div>

    <div style="display:flex;gap:0.4rem;margin-bottom:1rem;">
        <button class="m-pill active" onclick="switchMatTab('stock')" id="tab-mat-stock" style="cursor:pointer;border:none;">Site Stock</button>
        <button class="m-pill" onclick="switchMatTab('req')" id="tab-mat-req" style="cursor:pointer;border:none;">+ Requisition</button>
    </div>

    {{-- Stock View --}}
    <div id="view-mat-stock">
        <div id="materials-container">
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
        </div>
    </div>

    {{-- Requisition View --}}
    <div id="view-mat-req" style="display:none;">
        <form class="m-card" style="display:flex;flex-direction:column;gap:0.8rem;" onsubmit="handleReqSubmit(event)">
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">MATERIAL ITEM NAME</label>
                <input type="text" id="req-item" placeholder="e.g. Portland Cement Grade 42.5" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;">
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">QUANTITY NEEDED</label>
                    <input type="number" id="req-qty" placeholder="50" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
                </div>
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">UNIT</label>
                    <select id="req-unit" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);">
                        <option value="Bags">Bags</option>
                        <option value="Tons">Tons</option>
                        <option value="Meters">Meters</option>
                        <option value="Pieces">Pieces</option>
                        <option value="Liters">Liters</option>
                    </select>
                </div>
            </div>
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">REQUIRED DELIVERY DATE</label>
                <input type="date" id="req-date" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
            </div>
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">PURPOSE / ZONE LOCATION</label>
                <input type="text" id="req-zone" placeholder="e.g. Block B Slab Pouring" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);">
            </div>
            <button type="submit" class="m-btn-primary" style="margin-top:0.5rem;padding:0.75rem;border-radius:10px;font-weight:700;border:none;cursor:pointer;">
                Submit Stock Requisition
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let materialsList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchMaterials();
        });

        async function fetchMaterials() {
            try {
                const res = await API.get('/materials?per_page=50');
                if (res?.data) {
                    materialsList = res.data;
                    localStorage.setItem('m_materials', JSON.stringify(materialsList));
                }
            } catch {
                const cached = localStorage.getItem('m_materials');
                if (cached) materialsList = JSON.parse(cached);
            }
            renderMaterials();
        }

        function switchMatTab(v) {
            document.getElementById('view-mat-stock').style.display = v === 'stock' ? 'block' : 'none';
            document.getElementById('view-mat-req').style.display = v === 'req' ? 'block' : 'none';
            document.getElementById('tab-mat-stock').className = `m-pill ${v === 'stock' ? 'active' : ''}`;
            document.getElementById('tab-mat-req').className = `m-pill ${v === 'req' ? 'active' : ''}`;
        }

        function renderMaterials() {
            const container = document.getElementById('materials-container');
            if (!materialsList || materialsList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">📦</div>
                        <div class="m-empty-title">No Inventory Logged</div>
                    </div>`;
                return;
            }

            container.innerHTML = materialsList.map(m => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">📦 ${esc(m.name || m.item_name)}</div>
                            <div class="m-card-subtitle">Category: ${esc(m.category || 'Construction Materials')}</div>
                        </div>
                        <span class="m-pill active">${esc(m.current_stock || m.quantity || 'In Stock')} ${esc(m.unit || '')}</span>
                    </div>
                    <div class="m-card-footer">
                        📍 Location: ${esc(m.storage_location || 'Main Yard')}
                        ${m.reorder_level ? ' · Min Level: ' + m.reorder_level : ''}
                    </div>
                </div>`).join('');
        }

        async function handleReqSubmit(e) {
            e.preventDefault();
            toast('Requisition Submitted ✓');
            switchMatTab('stock');
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
