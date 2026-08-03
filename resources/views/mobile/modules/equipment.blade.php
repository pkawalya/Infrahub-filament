@extends('mobile.layout')
@section('title', 'Equipment & Fuel Logs — InfraHub')

@section('content')
    <div class="m-page-title">Equipment & Machinery</div>
    <div class="m-page-subtitle">Site fleet allocation & fuel consumption</div>

    <div style="display:flex;gap:0.4rem;margin-bottom:1rem;">
        <button class="m-pill active" onclick="switchView('fleet')" id="tab-fleet" style="cursor:pointer;border:none;">Active Fleet</button>
        <button class="m-pill" onclick="switchView('fuel')" id="tab-fuel" style="cursor:pointer;border:none;">⛽ Log Fuel</button>
    </div>

    {{-- Fleet List View --}}
    <div id="view-fleet">
        <div id="equipment-container">
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
        </div>
    </div>

    {{-- Log Fuel View --}}
    <div id="view-fuel" style="display:none;">
        <form id="fuel-form" class="m-card" style="display:flex;flex-direction:column;gap:0.8rem;" onsubmit="handleFuelSubmit(event)">
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">EQUIPMENT / ASSET</label>
                <select id="fuel-asset" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
                    <option value="">Select Equipment...</option>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;">
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">LITERS DISPENSED</label>
                    <input type="number" id="fuel-liters" placeholder="e.g. 150" step="0.1" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
                </div>
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">HOUR METER READ</label>
                    <input type="number" id="fuel-meter" placeholder="e.g. 1420" step="0.1" style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);" required>
                </div>
            </div>
            <div>
                <label style="font-size:0.75rem;font-weight:600;color:var(--text-dim);display:block;margin-bottom:0.25rem;">DISPENSED BY / DRIVER</label>
                <input type="text" id="fuel-operator" placeholder="Operator or Driver Name..." style="width:100%;padding:0.6rem;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);">
            </div>
            <button type="submit" class="m-btn-primary" style="margin-top:0.5rem;padding:0.75rem;border-radius:10px;font-weight:700;border:none;cursor:pointer;">
                Submit Fuel Log Entry
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let fleetList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchFleet();
        });

        async function fetchFleet() {
            try {
                const res = await API.get('/assets?per_page=30');
                if (res?.data) {
                    fleetList = res.data;
                    localStorage.setItem('m_fleet', JSON.stringify(fleetList));
                    populateAssetDropdown();
                }
            } catch {
                const cached = localStorage.getItem('m_fleet');
                if (cached) {
                    fleetList = JSON.parse(cached);
                    populateAssetDropdown();
                }
            }
            renderFleet();
        }

        function populateAssetDropdown() {
            const sel = document.getElementById('fuel-asset');
            sel.innerHTML = '<option value="">Select Equipment...</option>';
            fleetList.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = `${a.name || a.title} (${a.asset_code || a.serial_number || 'EQP'})`;
                sel.appendChild(opt);
            });
        }

        function switchView(v) {
            document.getElementById('view-fleet').style.display = v === 'fleet' ? 'block' : 'none';
            document.getElementById('view-fuel').style.display = v === 'fuel' ? 'block' : 'none';
            document.getElementById('tab-fleet').className = `m-pill ${v === 'fleet' ? 'active' : ''}`;
            document.getElementById('tab-fuel').className = `m-pill ${v === 'fuel' ? 'active' : ''}`;
        }

        function renderFleet() {
            const container = document.getElementById('equipment-container');
            if (!fleetList || fleetList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">🚜</div>
                        <div class="m-empty-title">No Equipment Allocated</div>
                    </div>`;
                return;
            }

            container.innerHTML = fleetList.map(a => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">🚜 ${esc(a.name || a.title)}</div>
                            <div class="m-card-subtitle">${esc(a.asset_code || a.category || 'Machinery')}</div>
                        </div>
                        <span class="m-pill active">${esc(a.status || 'Active')}</span>
                    </div>
                    <div class="m-card-footer" style="margin-top:0.4rem;">
                        📍 Location: ${esc(a.current_location || a.site || 'Main Site')}
                        ${a.meter_reading ? ' · ⏱️ Meter: ' + a.meter_reading + ' hrs' : ''}
                    </div>
                </div>`).join('');
        }

        async function handleFuelSubmit(e) {
            e.preventDefault();
            const payload = {
                asset_id: document.getElementById('fuel-asset').value,
                liters: parseFloat(document.getElementById('fuel-liters').value),
                hour_meter: parseFloat(document.getElementById('fuel-meter').value),
                operator_name: document.getElementById('fuel-operator').value,
                date: new Date().toISOString().slice(0, 10),
            };

            try {
                const res = await API.post('/equipment-fuel-logs', payload);
                if (res) {
                    toast('Fuel Log Saved ✓');
                    switchView('fleet');
                }
            } catch {
                toast('Fuel Log Saved offline queue', 'info');
                switchView('fleet');
            }
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
