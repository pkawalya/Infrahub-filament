@extends('mobile.layout')
@section('title', 'Daily Site Diaries — InfraHub Mobile')

@section('content')
    <div class="m-page-title" style="display:flex;align-items:center;gap:0.5rem;">
        <x-mobile.icon name="diaries" size="24" class="text-blue-400" /> Daily Site Diaries
    </div>
    <div class="m-page-subtitle">Field operational records & weather logs</div>

    <div class="m-category-tabs">
        <button type="button" class="m-category-tab active" onclick="switchView('list')" id="tab-list">Diaries Log</button>
        <button type="button" class="m-category-tab" onclick="switchView('new')" id="tab-new">+ New Diary</button>
    </div>

    {{-- Diaries List View --}}
    <div id="view-list">
        <div id="diaries-container">
            <div class="m-card"><div class="m-skeleton" style="height:70px;"></div></div>
            <div class="m-card"><div class="m-skeleton" style="height:70px;"></div></div>
        </div>
    </div>

    {{-- New Diary Form View --}}
    <div id="view-new" style="display:none;">
        <form id="diary-form" class="m-card" style="display:flex;flex-direction:column;gap:0.8rem;" onsubmit="handleDiarySubmit(event)">
            <div class="m-form-group">
                <label class="m-label">Project *</label>
                <select id="diary-project" class="m-select" required>
                    <option value="">Select Project...</option>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Date *</label>
                    <input type="date" id="diary-date" class="m-input" required>
                </div>
                <div class="m-form-group">
                    <label class="m-label">Weather</label>
                    <select id="diary-weather" class="m-select">
                        <option value="Sunny">Clear / Sunny</option>
                        <option value="Overcast">Overcast / Cloudy</option>
                        <option value="Rainy">Rain / Storm</option>
                        <option value="High Winds">High Winds</option>
                    </select>
                </div>
            </div>
            <div class="m-form-group">
                <label class="m-label">Total Crew Count</label>
                <input type="number" id="diary-crew" placeholder="e.g. 24" class="m-input">
            </div>
            <div class="m-form-group">
                <label class="m-label">Work Accomplished & Notes *</label>
                <textarea id="diary-notes" class="m-textarea" rows="4" placeholder="Describe progress, delays, equipment used..." required></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-primary">
                Submit Site Diary
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let diariesList = [];
        let projectsList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            document.getElementById('diary-date').value = new Date().toISOString().slice(0, 10);

            try {
                const pRes = await MobileAPI.get('/projects?per_page=50');
                if (pRes?.data) {
                    projectsList = pRes.data;
                    const sel = document.getElementById('diary-project');
                    projectsList.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.name;
                        sel.appendChild(opt);
                    });
                }
            } catch {}

            fetchDiaries();
        });

        async function fetchDiaries() {
            try {
                const res = await MobileAPI.get('/daily-site-diaries?per_page=30');
                if (res?.data) {
                    diariesList = res.data;
                    localStorage.setItem('m_diaries', JSON.stringify(diariesList));
                }
            } catch {
                const cached = localStorage.getItem('m_diaries');
                if (cached) diariesList = JSON.parse(cached);
            }
            renderDiaries();
        }

        function switchView(view) {
            document.getElementById('view-list').style.display = view === 'list' ? 'block' : 'none';
            document.getElementById('view-new').style.display = view === 'new' ? 'block' : 'none';
            document.querySelectorAll('.m-category-tabs button').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + view).classList.add('active');
        }

        function renderDiaries() {
            const container = document.getElementById('diaries-container');
            if (!diariesList || diariesList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg></div>
                        <div class="m-empty-title">No Site Diaries Recorded</div>
                    </div>`;
                return;
            }

            container.innerHTML = diariesList.map(d => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">Date: ${esc(d.date || '')}</div>
                            <div class="m-card-subtitle">${esc(d.cde_project?.name || d.project_name || 'Site Record')}</div>
                        </div>
                        <span class="m-pill active"><span class="m-pill-dot"></span><span class="m-pill-text">${esc(d.weather_conditions || d.weather || 'Normal')}</span></span>
                    </div>
                    <div style="font-size:0.82rem;color:var(--text-muted);margin:0.5rem 0;line-height:1.4;">
                        ${esc(d.work_summary || d.notes || 'No summary provided')}
                    </div>
                    <div class="m-card-footer">
                        Crew: ${d.total_workers || d.crew_count || 0} workers
                        ${d.supervisor?.name ? ' · Supervisor: ' + esc(d.supervisor.name) : ''}
                    </div>
                </div>`).join('');
        }

        async function handleDiarySubmit(e) {
            e.preventDefault();
            const payload = {
                cde_project_id: document.getElementById('diary-project').value,
                date: document.getElementById('diary-date').value,
                weather_conditions: document.getElementById('diary-weather').value,
                total_workers: document.getElementById('diary-crew').value || 0,
                work_summary: document.getElementById('diary-notes').value,
            };

            if (!navigator.onLine) {
                if (typeof window.saveFormOffline === 'function') {
                    await window.saveFormOffline('site-diaries', payload);
                    switchView('list');
                    return;
                }
            }

            try {
                const res = await MobileAPI.post('/daily-site-diaries', payload);
                if (res) {
                    MobileUI.toast('Site Diary saved ✓');
                    switchView('list');
                    fetchDiaries();
                } else {
                    MobileUI.toast('Failed to save diary', 'error');
                }
            } catch {
                if (typeof window.saveFormOffline === 'function') {
                    await window.saveFormOffline('site-diaries', payload);
                    switchView('list');
                }
            }
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
