@extends('mobile.layout', ['active' => 'forms'])
@section('title', 'Field Forms — InfraHub Mobile')

@section('content')
    <div class="m-page-title">Field Forms</div>
    <div class="m-page-subtitle">Submit site data — works offline too</div>

    {{-- Form Selector Tabs --}}
    <div class="m-category-tabs" style="margin-bottom:1.25rem;">
        <button type="button" class="m-category-tab active" onclick="showForm('diary', this)" id="btn-diary">
            <x-mobile.icon name="diaries" size="16" /> Site Diary
        </button>
        <button type="button" class="m-category-tab" onclick="showForm('attendance', this)" id="btn-attendance">
            <x-mobile.icon name="attendance" size="16" /> Attendance
        </button>
        <button type="button" class="m-category-tab" onclick="showForm('safety', this)" id="btn-safety">
            <x-mobile.icon name="safety" size="16" /> Safety Hazard
        </button>
    </div>

    {{-- Site Diary Form --}}
    <div id="form-diary" class="form-panel m-card">
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
                        <option value="">Select weather</option>
                        <option value="sunny">Clear / Sunny</option>
                        <option value="cloudy">Overcast / Cloudy</option>
                        <option value="rainy">Rain / Storm</option>
                        <option value="windy">High Winds</option>
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
                <textarea class="m-textarea" name="work_performed" rows="3" placeholder="Today's activities & progress summary..."></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-primary">Save Site Diary</button>
        </form>
    </div>

    {{-- Attendance Form --}}
    <div id="form-attendance" class="form-panel m-card" style="display:none;">
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
                    <option value="present">Present</option>
                    <option value="late">Late Arrival</option>
                    <option value="absent">Absent</option>
                    <option value="half_day">Half Day Shift</option>
                </select>
            </div>
            <div class="m-form-group">
                <label class="m-label">Notes</label>
                <textarea class="m-textarea" name="notes" rows="2" placeholder="Any notes..."></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-success">Save Attendance</button>
        </form>
    </div>

    {{-- Safety Incident Form --}}
    <div id="form-safety" class="form-panel m-card" style="display:none;">
        <form onsubmit="return submitSafety(event)">
            <div class="m-form-group">
                <label class="m-label">Incident Title *</label>
                <input type="text" class="m-input" name="title" required placeholder="Brief description of hazard / incident">
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
            <button type="submit" class="m-btn m-btn-danger">Report Safety Incident</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            loadProjectDropdowns();
            const hash = location.hash.replace('#', '');
            if (hash) {
                const btn = document.getElementById('btn-' + hash);
                showForm(hash, btn);
            }
        });

        function showForm(name, btn) {
            document.querySelectorAll('.form-panel').forEach(f => f.style.display = 'none');
            document.querySelectorAll('.m-category-tabs button').forEach(b => b.classList.remove('active'));
            const panel = document.getElementById('form-' + name);
            if (panel) panel.style.display = 'block';
            if (btn) btn.classList.add('active');
        }

        async function loadProjectDropdowns() {
            let projects = [];
            try {
                const cached = localStorage.getItem('m_projects');
                if (cached) projects = JSON.parse(cached);
            } catch { }

            if (!projects.length) {
                try {
                    const data = await MobileAPI.get('/projects?per_page=100');
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
            if (!data.cde_project_id) { MobileUI.toast('Select a project', 'error'); return false; }

            try {
                if (navigator.onLine) {
                    await MobileAPI.post('/offline-sync/site-diaries', data);
                    MobileUI.toast('Site diary saved! ✓');
                    e.target.reset();
                } else {
                    if (typeof window.saveFormOffline === 'function') await window.saveFormOffline('site-diaries', data);
                    MobileUI.toast('Saved offline — will sync when online', 'info');
                    e.target.reset();
                }
            } catch { MobileUI.toast('Error saving', 'error'); }
            return false;
        }

        async function submitAttendance(e) {
            e.preventDefault();
            const data = formData(e.target);
            try {
                if (navigator.onLine) {
                    await MobileAPI.post('/offline-sync/attendance', data);
                    MobileUI.toast('Attendance recorded! ✓');
                    e.target.reset();
                } else {
                    if (typeof window.saveFormOffline === 'function') await window.saveFormOffline('attendance', data);
                    MobileUI.toast('Saved offline', 'info');
                    e.target.reset();
                }
            } catch { MobileUI.toast('Error saving', 'error'); }
            return false;
        }

        async function submitSafety(e) {
            e.preventDefault();
            const data = formData(e.target);
            if (!data.title) { MobileUI.toast('Enter a title', 'error'); return false; }
            try {
                if (navigator.onLine) {
                    await MobileAPI.post('/offline-sync/safety-incidents', data);
                    MobileUI.toast('Safety incident reported! ✓');
                    e.target.reset();
                } else {
                    if (typeof window.saveFormOffline === 'function') await window.saveFormOffline('safety-incidents', data);
                    MobileUI.toast('Saved offline', 'info');
                    e.target.reset();
                }
            } catch { MobileUI.toast('Error saving', 'error'); }
            return false;
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    </script>
@endpush