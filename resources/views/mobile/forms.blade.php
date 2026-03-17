@extends('mobile.layout')
@section('title', 'Offline Forms — InfraHub')

@section('content')
    <div class="m-page-title">Field Forms</div>
    <div class="m-page-subtitle">Submit data — works offline too</div>

    {{-- Form Selector --}}
    <div style="display:flex;gap:0.5rem;margin-bottom:1.25rem;overflow-x:auto;">
        <button class="m-btn m-btn-primary" onclick="showForm('diary')" id="btn-diary"
            style="flex:1;font-size:0.78rem;padding:0.6rem;">📋 Diary</button>
        <button class="m-btn m-btn-outline" onclick="showForm('attendance')" id="btn-attendance"
            style="flex:1;font-size:0.78rem;padding:0.6rem;">👷 Attendance</button>
        <button class="m-btn m-btn-outline" onclick="showForm('safety')" id="btn-safety"
            style="flex:1;font-size:0.78rem;padding:0.6rem;">⚠️ Safety</button>
    </div>

    {{-- Site Diary Form --}}
    <div id="form-diary" class="form-panel">
        <form onsubmit="return submitDiary(event)">
            <div class="m-form-group">
                <label class="m-label">Project *</label>
                <select class="m-select proj-select" name="cde_project_id" required></select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Date</label>
                    <input type="date" class="m-input" name="diary_date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="m-form-group">
                    <label class="m-label">Weather</label>
                    <select class="m-select" name="weather">
                        <option value="">Select</option>
                        <option value="sunny">☀️ Sunny</option>
                        <option value="cloudy">☁️ Cloudy</option>
                        <option value="rainy">🌧️ Rainy</option>
                        <option value="windy">💨 Windy</option>
                    </select>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Own Workers</label>
                    <input type="number" class="m-input" name="workers_on_site" min="0" value="0">
                </div>
                <div class="m-form-group">
                    <label class="m-label">Sub Workers</label>
                    <input type="number" class="m-input" name="subcontractor_workers" min="0" value="0">
                </div>
            </div>
            <div class="m-form-group">
                <label class="m-label">Work Performed</label>
                <textarea class="m-textarea" name="work_performed" rows="3" placeholder="Today's activities..."></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-primary">📋 Save Site Diary</button>
        </form>
    </div>

    {{-- Attendance Form --}}
    <div id="form-attendance" class="form-panel" style="display:none;">
        <form onsubmit="return submitAttendance(event)">
            <div class="m-form-group">
                <label class="m-label">Project</label>
                <select class="m-select proj-select" name="cde_project_id"></select>
            </div>
            <div class="m-form-group">
                <label class="m-label">Date</label>
                <input type="date" class="m-input" name="attendance_date" value="{{ date('Y-m-d') }}" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Clock In</label>
                    <input type="time" class="m-input" name="clock_in" value="07:00">
                </div>
                <div class="m-form-group">
                    <label class="m-label">Clock Out</label>
                    <input type="time" class="m-input" name="clock_out" value="17:00">
                </div>
            </div>
            <div class="m-form-group">
                <label class="m-label">Status</label>
                <select class="m-select" name="status" required>
                    <option value="present">✅ Present</option>
                    <option value="late">⏰ Late</option>
                    <option value="absent">❌ Absent</option>
                    <option value="half_day">½ Half Day</option>
                </select>
            </div>
            <div class="m-form-group">
                <label class="m-label">Notes</label>
                <textarea class="m-textarea" name="notes" rows="2" placeholder="Any notes..."></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-success">👷 Save Attendance</button>
        </form>
    </div>

    {{-- Safety Incident Form --}}
    <div id="form-safety" class="form-panel" style="display:none;">
        <form onsubmit="return submitSafety(event)">
            <div class="m-form-group">
                <label class="m-label">Title *</label>
                <input type="text" class="m-input" name="title" required placeholder="Brief description of incident">
            </div>
            <div class="m-form-group">
                <label class="m-label">Project</label>
                <select class="m-select proj-select" name="cde_project_id"></select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Type</label>
                    <select class="m-select" name="type">
                        <option value="near_miss">Near Miss</option>
                        <option value="first_aid">First Aid</option>
                        <option value="injury">Injury</option>
                        <option value="property_damage">Property Damage</option>
                    </select>
                </div>
                <div class="m-form-group">
                    <label class="m-label">Severity</label>
                    <select class="m-select" name="severity">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
            </div>
            <div class="m-form-group">
                <label class="m-label">Location</label>
                <input type="text" class="m-input" name="location" placeholder="e.g. Block C, Level 2">
            </div>
            <div class="m-form-group">
                <label class="m-label">Description</label>
                <textarea class="m-textarea" name="description" rows="3" placeholder="What happened..."></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-danger">⚠️ Report Safety Incident</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            loadProjectDropdowns();
            // Jump to specific form via hash
            const hash = location.hash.replace('#', '');
            if (hash) showForm(hash);
        });

        function showForm(name) {
            document.querySelectorAll('.form-panel').forEach(f => f.style.display = 'none');
            document.querySelectorAll('[id^="btn-"]').forEach(b => { b.className = 'm-btn m-btn-outline'; b.style.flex = '1'; b.style.fontSize = '0.78rem'; b.style.padding = '0.6rem'; });
            const panel = document.getElementById('form-' + name);
            const btn = document.getElementById('btn-' + name);
            if (panel) panel.style.display = 'block';
            if (btn) btn.className = 'm-btn m-btn-primary';
        }

        async function loadProjectDropdowns() {
            let projects = [];
            try {
                const cached = localStorage.getItem('m_projects');
                if (cached) projects = JSON.parse(cached);
            } catch { }

            if (!projects.length) {
                try {
                    const data = await API.get('/projects?per_page=100');
                    if (data?.data) { projects = data.data; localStorage.setItem('m_projects', JSON.stringify(projects)); }
                } catch { }
            }

            document.querySelectorAll('.proj-select').forEach(sel => {
                sel.innerHTML = '<option value="">— Select Project —</option>' +
                    projects.map(p => `<option value="${p.id}">${esc(p.name)}</option>`).join('');
            });
        }

        function formData(form) {
            const fd = new FormData(form);
            const obj = {};
            for (const [k, v] of fd.entries()) { if (v) obj[k] = v; }
            return obj;
        }

        async function submitDiary(e) {
            e.preventDefault();
            const data = formData(e.target);
            if (!data.cde_project_id) { toast('Select a project', 'error'); return false; }

            try {
                if (navigator.onLine) {
                    await API.post('/offline-sync/site-diaries', data);
                    toast('Site diary saved! ✓');
                    e.target.reset();
                } else {
                    if (typeof window.saveFormOffline === 'function') await window.saveFormOffline('site-diaries', data);
                    toast('Saved offline — will sync when online', 'info');
                    e.target.reset();
                }
            } catch { toast('Error saving', 'error'); }
            return false;
        }

        async function submitAttendance(e) {
            e.preventDefault();
            const data = formData(e.target);
            try {
                if (navigator.onLine) {
                    await API.post('/offline-sync/attendance', data);
                    toast('Attendance recorded! ✓');
                    e.target.reset();
                } else {
                    if (typeof window.saveFormOffline === 'function') await window.saveFormOffline('attendance', data);
                    toast('Saved offline', 'info');
                    e.target.reset();
                }
            } catch { toast('Error saving', 'error'); }
            return false;
        }

        async function submitSafety(e) {
            e.preventDefault();
            const data = formData(e.target);
            if (!data.title) { toast('Enter a title', 'error'); return false; }
            try {
                if (navigator.onLine) {
                    await API.post('/offline-sync/safety-incidents', data);
                    toast('Safety incident reported! ✓');
                    e.target.reset();
                } else {
                    if (typeof window.saveFormOffline === 'function') await window.saveFormOffline('safety-incidents', data);
                    toast('Saved offline', 'info');
                    e.target.reset();
                }
            } catch { toast('Error saving', 'error'); }
            return false;
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    </script>
@endpush