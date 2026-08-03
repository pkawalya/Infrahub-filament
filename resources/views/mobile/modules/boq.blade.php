@extends('mobile.layout')
@section('title', 'BOQ Management — InfraHub')

@section('content')
    <div class="m-page-title">BOQ Management</div>
    <div class="m-page-subtitle">Bill of Quantities, material take-offs & variance tracking</div>

    <div style="display:flex;gap:0.4rem;margin-bottom:1rem;">
        <button class="m-pill active" onclick="switchBoqTab('all')" id="tab-all" style="cursor:pointer;border:none;">All Items</button>
        <button class="m-pill" onclick="switchBoqTab('materials')" id="tab-materials" style="cursor:pointer;border:none;">Materials</button>
        <button class="m-pill" onclick="switchBoqTab('labor')" id="tab-labor" style="cursor:pointer;border:none;">Labor/Plant</button>
    </div>

    <div id="boq-container">
        <div class="m-card"><div class="m-skeleton" style="height:75px;"></div></div>
        <div class="m-card"><div class="m-skeleton" style="height:75px;"></div></div>
    </div>
@endsection

@push('scripts')
    <script>
        let boqItems = [];
        let activeTab = 'all';

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchBOQ();
        });

        async function fetchBOQ() {
            try {
                const res = await API.get('/boq-items?per_page=30');
                if (res?.data) boqItems = res.data;
                localStorage.setItem('m_boq_items', JSON.stringify(boqItems));
            } catch {
                const cached = localStorage.getItem('m_boq_items');
                if (cached) boqItems = JSON.parse(cached);
            }
            renderBOQ();
        }

        function switchBoqTab(tab) {
            activeTab = tab;
            document.getElementById('tab-all').className = `m-pill ${tab === 'all' ? 'active' : ''}`;
            document.getElementById('tab-materials').className = `m-pill ${tab === 'materials' ? 'active' : ''}`;
            document.getElementById('tab-labor').className = `m-pill ${tab === 'labor' ? 'active' : ''}`;
            renderBOQ();
        }

        function renderBOQ() {
            const container = document.getElementById('boq-container');
            const filtered = boqItems.filter(item => {
                if (activeTab === 'materials') return item.type === 'material' || item.category?.toLowerCase().includes('material');
                if (activeTab === 'labor') return item.type === 'labor' || item.type === 'equipment';
                return true;
            });

            if (!filtered || filtered.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">🧮</div>
                        <div class="m-empty-title">No BOQ Items Found</div>
                        <div class="m-empty-text">Bill of quantities items for active projects will appear here.</div>
                    </div>`;
                return;
            }

            container.innerHTML = filtered.map(item => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">📐 ${esc(item.item_code || 'BOQ-' + item.id)}: ${esc(item.description || item.name)}</div>
                            <div class="m-card-subtitle">${esc(item.cde_project?.name || item.bill_name || 'Project BOQ')}</div>
                        </div>
                        <span class="m-pill active">${esc(item.unit || 'QTY')}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin:0.5rem 0;font-size:0.82rem;">
                        <div>Quantity: <strong>${(item.quantity || 0).toLocaleString()} ${esc(item.unit || '')}</strong></div>
                        <div>Rate: <strong>$${(item.unit_rate || 0).toLocaleString()}</strong></div>
                    </div>
                    <div style="font-size:0.95rem;font-weight:800;color:#10b981;margin-bottom:0.4rem;">
                        Total Value: $${((item.quantity || 0) * (item.unit_rate || 0)).toLocaleString()}
                    </div>
                    <div class="m-card-footer">
                        📊 Category: ${esc(item.category || item.type || 'General Work')}
                        ${item.executed_qty ? ' · Executed: ' + item.executed_qty + ' ' + (item.unit || '') : ''}
                    </div>
                </div>`).join('');
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
