@extends('mobile.layout')
@section('title', 'Safety Incidents & Inspections — InfraHub Mobile')

@section('content')
    <div class="m-page-title" style="display:flex;align-items:center;gap:0.5rem;">
        <x-mobile.icon name="safety" size="24" class="text-red-400" /> Safety Incidents
    </div>
    <div class="m-page-subtitle">Site hazard logs, HSE triage & audits</div>

    <div class="m-category-tabs">
        <button type="button" class="m-category-tab active" onclick="switchView('incidents')" id="tab-incidents">Incident Log</button>
        <button type="button" class="m-category-tab" onclick="switchView('report')" id="tab-report">+ Report Hazard</button>
    </div>

    {{-- Incidents List View --}}
    <div id="view-incidents">
        <div id="safety-container">
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
        </div>
    </div>

    {{-- Report Incident View --}}
    <div id="view-report" style="display:none;">
        <form id="safety-form" class="m-card" style="display:flex;flex-direction:column;gap:0.8rem;" onsubmit="handleSafetySubmit(event)">
            <div class="m-form-group">
                <label class="m-label">Incident Title / Hazard *</label>
                <input type="text" id="safe-title" placeholder="Brief description of hazard..." class="m-input" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Severity *</label>
                    <select id="safe-severity" class="m-select" required>
                        <option value="Low">Low (Near Miss)</option>
                        <option value="Medium" selected>Medium (Property/Minor)</option>
                        <option value="High">High (Medical/Injury)</option>
                        <option value="Critical">Critical (Stop Work)</option>
                    </select>
                </div>
                <div class="m-form-group">
                    <label class="m-label">Incident Type</label>
                    <select id="safe-type" class="m-select">
                        <option value="Near Miss">Near Miss</option>
                        <option value="Injury">Injury</option>
                        <option value="Property Damage">Property Damage</option>
                        <option value="Environmental">Environmental</option>
                        <option value="PPE Violation">PPE Violation</option>
                    </select>
                </div>
            </div>
            <div class="m-form-group">
                <label class="m-label">Incident Details & Immediate Action *</label>
                <textarea id="safe-description" class="m-textarea" rows="4" placeholder="Describe exact location, causes, involved personnel & immediate mitigation..." required></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-danger">
                File Safety Hazard Report
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let safetyIncidents = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchSafetyIncidents();
        });

        async function fetchSafetyIncidents() {
            try {
                const res = await MobileAPI.get('/safety-incidents?per_page=30');
                if (res?.data) {
                    safetyIncidents = res.data;
                    localStorage.setItem('m_safety', JSON.stringify(safetyIncidents));
                }
            } catch {
                const cached = localStorage.getItem('m_safety');
                if (cached) safetyIncidents = JSON.parse(cached);
            }
            renderSafetyIncidents();
        }

        function switchView(v) {
            document.getElementById('view-incidents').style.display = v === 'incidents' ? 'block' : 'none';
            document.getElementById('view-report').style.display = v === 'report' ? 'block' : 'none';
            document.querySelectorAll('.m-category-tabs button').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + v).classList.add('active');
        }

        function renderSafetyIncidents() {
            const container = document.getElementById('safety-container');
            if (!safetyIncidents || safetyIncidents.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></div>
                        <div class="m-empty-title">Zero Active Incidents Reported</div>
                    </div>`;
                return;
            }

            container.innerHTML = safetyIncidents.map(i => {
                const sev = (i.severity || 'low').toLowerCase();
                const badgeStatus = sev === 'critical' || sev === 'high' ? 'overdue' : 'active';
                return `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">${esc(i.title || i.incident_type || 'Safety Log')}</div>
                            <div class="m-card-subtitle">${esc(i.incident_type || 'HSE Incident')}</div>
                        </div>
                        <span class="m-pill ${badgeStatus}"><span class="m-pill-dot"></span><span class="m-pill-text">${esc(i.severity || 'Low')}</span></span>
                    </div>
                    <div style="font-size:0.82rem;color:var(--text-muted);margin:0.5rem 0;line-height:1.4;">
                        ${esc(i.description || i.details || '')}
                    </div>
                    <div class="m-card-footer">
                        Date: ${esc(i.created_at ? i.created_at.slice(0, 10) : new Date().toISOString().slice(0,10))}
                        ${i.reporter?.name ? ' · Reporter: ' + esc(i.reporter.name) : ''}
                    </div>
                </div>`;
            }).join('');
        }

        async function handleSafetySubmit(e) {
            e.preventDefault();
            const payload = {
                title: document.getElementById('safe-title').value,
                severity: document.getElementById('safe-severity').value,
                incident_type: document.getElementById('safe-type').value,
                description: document.getElementById('safe-description').value,
                date: new Date().toISOString().slice(0, 10),
            };

            if (!navigator.onLine && typeof window.saveFormOffline === 'function') {
                await window.saveFormOffline('safety-incidents', payload);
                switchView('incidents');
                return;
            }

            try {
                const res = await MobileAPI.post('/safety-incidents', payload);
                if (res) {
                    MobileUI.toast('Safety Hazard Logged ✓');
                    switchView('incidents');
                    fetchSafetyIncidents();
                }
            } catch {
                if (typeof window.saveFormOffline === 'function') {
                    await window.saveFormOffline('safety-incidents', payload);
                    switchView('incidents');
                }
            }
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
