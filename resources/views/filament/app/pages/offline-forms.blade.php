<x-filament-panels::page>
    {{-- Inline styles for offline functionality --}}
    <style>
        .offline-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.5rem;
        }

        .offline-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.12);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.2s;
        }

        .offline-card:hover {
            border-color: rgba(99, 102, 241, 0.3);
            transform: translateY(-2px);
        }

        .offline-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .offline-card p {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .offline-card .icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .icon-diary {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }

        .icon-attendance {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
        }

        .icon-safety {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }

        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border-radius: 10px;
            border: 1px solid rgba(99, 102, 241, 0.15);
            background: rgba(15, 23, 42, 0.6);
            color: #e2e8f0;
            font-size: 0.85rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.82rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            width: 100%;
            justify-content: center;
        }

        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
        }

        .btn-save:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-save svg {
            width: 16px;
            height: 16px;
        }

        .queue-panel {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.12);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .queue-panel h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .queue-stats {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .queue-stat {
            text-align: center;
        }

        .queue-stat .count {
            font-size: 1.5rem;
            font-weight: 800;
            color: #818cf8;
        }

        .queue-stat .label {
            font-size: 0.7rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .queue-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.75rem;
        }

        .btn-sync {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.2);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-sync:hover {
            background: rgba(34, 197, 94, 0.25);
        }

        .btn-sync:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot.online {
            background: #4ade80;
        }

        .status-dot.offline {
            background: #ef4444;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: 0.3
            }
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #64748b;
            font-size: 0.85rem;
        }
    </style>

    {{-- Queue Status Panel --}}
    <div class="queue-panel" id="queue-panel">
        <h3>
            <span class="status-dot" id="connection-dot"></span>
            <span id="connection-label">Checking connection...</span>
        </h3>
        <div class="queue-stats">
            <div class="queue-stat">
                <div class="count" id="q-generic">0</div>
                <div class="label">Form Submissions</div>
            </div>
            <div class="queue-stat">
                <div class="count" id="q-diaries">0</div>
                <div class="label">Site Diaries</div>
            </div>
            <div class="queue-stat">
                <div class="count" id="q-attendance">0</div>
                <div class="label">Attendance</div>
            </div>
            <div class="queue-stat">
                <div class="count" id="q-safety">0</div>
                <div class="label">Safety Reports</div>
            </div>
            <div class="queue-stat">
                <div class="count" id="q-total" style="color: #c084fc;">0</div>
                <div class="label">Total Pending</div>
            </div>
        </div>
        <div class="queue-actions">
            <button class="btn-sync" id="btn-manual-sync" onclick="manualSync()" disabled>
                ↻ Sync Now
            </button>
            <button class="btn-sync" onclick="clearAllQueues()" style="background: rgba(239,68,68,0.15); color: #f87171; border-color: rgba(239,68,68,0.2);">
                🗑 Clear Queue
            </button>
        </div>
        <div style="margin-top: 0.75rem; font-size: 0.72rem; color: #64748b;">
            💡 <strong>Tip:</strong> Press <kbd style="background:rgba(99,102,241,0.15); padding:0.1rem 0.4rem; border-radius:4px; font-size:0.7rem;">Ctrl+Shift+S</kbd> on any Create/Edit page to save it offline
        </div>
    </div>

    {{-- Pending Items List --}}
    <div class="queue-panel" id="pending-list-panel" style="display:none; margin-bottom: 1.5rem;">
        <h3>📋 Pending Offline Items</h3>
        <div id="pending-items-list"></div>
    </div>

    {{-- Form Cards --}}
    <div class="offline-grid">
        {{-- Site Diary Form --}}
        <div class="offline-card">
            <div class="icon-wrap icon-diary">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <h3>📋 Daily Site Diary</h3>
            <p>Record weather, workforce, equipment, and work performed on site.</p>
            <form id="form-diary" onsubmit="return saveDiary(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label>Project</label>
                        <select name="cde_project_id" required id="diary-project">
                            <option value="">Loading projects...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="diary_date" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Weather</label>
                        <select name="weather">
                            <option value="">Select</option>
                            <option value="sunny">☀️ Sunny</option>
                            <option value="cloudy">☁️ Cloudy</option>
                            <option value="rainy">🌧️ Rainy</option>
                            <option value="windy">💨 Windy</option>
                            <option value="stormy">⛈️ Stormy</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Temperature (°C)</label>
                        <input type="number" name="temperature" step="0.1" placeholder="e.g. 28">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Own Workers</label>
                        <input type="number" name="workers_on_site" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label>Sub Workers</label>
                        <input type="number" name="subcontractor_workers" min="0" value="0">
                    </div>
                </div>
                <div class="form-group">
                    <label>Equipment on Site</label>
                    <input type="number" name="equipment_on_site" min="0" value="0">
                </div>
                <div class="form-group">
                    <label>Work Performed</label>
                    <textarea name="work_performed" rows="3" placeholder="Describe today's activities..."></textarea>
                </div>
                <div class="form-group">
                    <label>Work Planned Tomorrow</label>
                    <textarea name="work_planned_tomorrow" rows="2"
                        placeholder="Tomorrow's planned activities..."></textarea>
                </div>
                <button type="submit" class="btn-save" id="btn-diary">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                    Save Site Diary
                </button>
            </form>
        </div>

        {{-- Crew Attendance Form --}}
        <div class="offline-card">
            <div class="icon-wrap icon-attendance">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <h3>👷 Crew Attendance</h3>
            <p>Record worker attendance, clock times, and status.</p>
            <form id="form-attendance" onsubmit="return saveAttendance(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label>Worker</label>
                        <select name="user_id" required id="att-worker">
                            <option value="">Loading workers...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="attendance_date" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Project (optional)</label>
                    <select name="cde_project_id" id="att-project">
                        <option value="">Loading projects...</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Clock In</label>
                        <input type="time" name="clock_in" value="07:00">
                    </div>
                    <div class="form-group">
                        <label>Clock Out</label>
                        <input type="time" name="clock_out" value="17:00">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="present">✅ Present</option>
                            <option value="late">⏰ Late</option>
                            <option value="absent">❌ Absent</option>
                            <option value="half_day">½ Half Day</option>
                            <option value="leave">🏖️ On Leave</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Site Location</label>
                        <input type="text" name="site_location" placeholder="e.g. Block A">
                    </div>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2" placeholder="Any notes..."></textarea>
                </div>
                <button type="submit" class="btn-save" id="btn-attendance">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                    Save Attendance
                </button>
            </form>
        </div>

        {{-- Safety Incident Form --}}
        <div class="offline-card">
            <div class="icon-wrap icon-safety">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h3>⚠️ Safety Incident</h3>
            <p>Report safety incidents, near-misses, and hazards immediately.</p>
            <form id="form-safety" onsubmit="return saveSafety(event)">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" required placeholder="Brief description of the incident">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Project</label>
                        <select name="cde_project_id" id="safety-project">
                            <option value="">Loading projects...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date & Time</label>
                        <input type="datetime-local" name="incident_date" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type">
                            <option value="near_miss">Near Miss</option>
                            <option value="first_aid">First Aid</option>
                            <option value="injury">Injury</option>
                            <option value="property_damage">Property Damage</option>
                            <option value="environmental">Environmental</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Severity</label>
                        <select name="severity">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="e.g. Block C, Level 2">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"
                        placeholder="Detailed description of what happened..."></textarea>
                </div>
                <button type="submit" class="btn-save" id="btn-safety"
                    style="background: linear-gradient(135deg, #dc2626, #ef4444);">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    Report Safety Incident
                </button>
            </form>
        </div>
    </div>

    {{-- JavaScript for form handling --}}
    <script>
        // ── Load dropdowns (projects + workers) ─────────────────
        const CACHE_KEY_PROJECTS = 'infrahub_projects_cache';
        const CACHE_KEY_WORKERS = 'infrahub_workers_cache';

        async function loadDropdowns() {
            // Load projects
            let projects = [];
            try {
                const resp = await fetch('/api/v1/projects', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (resp.ok) {
                    const json = await resp.json();
                    projects = json.data || json || [];
                    localStorage.setItem(CACHE_KEY_PROJECTS, JSON.stringify(projects));
                }
            } catch {
                // Offline fallback: use cached data
                const cached = localStorage.getItem(CACHE_KEY_PROJECTS);
                if (cached) projects = JSON.parse(cached);
            }

            // Populate project dropdowns
            ['diary-project', 'att-project', 'safety-project'].forEach(id => {
                const sel = document.getElementById(id);
                if (!sel) return;
                sel.innerHTML = '<option value="">— Select Project —</option>';
                projects.forEach(p => {
                    sel.innerHTML += `<option value="${p.id}">${p.name || p.code || 'Project ' + p.id}</option>`;
                });
            });

            // Load workers
            let workers = [];
            try {
                const resp = await fetch('/api/v1/offline-sync/workers', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (resp.ok) {
                    workers = await resp.json();
                    localStorage.setItem(CACHE_KEY_WORKERS, JSON.stringify(workers));
                }
            } catch {
                const cached = localStorage.getItem(CACHE_KEY_WORKERS);
                if (cached) workers = JSON.parse(cached);
            }

            const workerSel = document.getElementById('att-worker');
            if (workerSel) {
                workerSel.innerHTML = '<option value="">— Select Worker —</option>';
                workers.forEach(w => {
                    workerSel.innerHTML += `<option value="${w.id}">${w.name}</option>`;
                });
            }
        }

        // ── Form data extractor ─────────────────────────────────
        function formToObj(form) {
            const fd = new FormData(form);
            const obj = {};
            for (const [k, v] of fd.entries()) {
                if (v !== '' && v !== null) obj[k] = v;
            }
            return obj;
        }

        // ── Save handlers ───────────────────────────────────────
        async function saveDiary(e) {
            e.preventDefault();
            const data = formToObj(e.target);
            if (!data.cde_project_id) { alert('Please select a project'); return false; }

            if (navigator.onLine) {
                // Try direct API call first
                try {
                    const resp = await fetch('/api/v1/offline-sync/site-diaries', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                        credentials: 'same-origin',
                        body: JSON.stringify(data),
                    });
                    if (resp.ok) {
                        showFormToast('Site diary saved! ✓', 'success');
                        e.target.reset();
                        return false;
                    }
                } catch { /* Fall through to offline save */ }
            }

            // Save offline
            await window.saveFormOffline('site-diaries', data);
            e.target.reset();
            refreshQueueCounts();
            return false;
        }

        async function saveAttendance(e) {
            e.preventDefault();
            const data = formToObj(e.target);
            if (!data.user_id) { alert('Please select a worker'); return false; }

            if (navigator.onLine) {
                try {
                    const resp = await fetch('/api/v1/offline-sync/attendance', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                        credentials: 'same-origin',
                        body: JSON.stringify(data),
                    });
                    if (resp.ok) {
                        showFormToast('Attendance recorded! ✓', 'success');
                        e.target.reset();
                        return false;
                    }
                } catch { /* Fall through to offline save */ }
            }

            await window.saveFormOffline('attendance', data);
            e.target.reset();
            refreshQueueCounts();
            return false;
        }

        async function saveSafety(e) {
            e.preventDefault();
            const data = formToObj(e.target);
            if (!data.title) { alert('Please enter a title'); return false; }

            if (navigator.onLine) {
                try {
                    const resp = await fetch('/api/v1/offline-sync/safety-incidents', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                        credentials: 'same-origin',
                        body: JSON.stringify(data),
                    });
                    if (resp.ok) {
                        showFormToast('Safety incident reported! ✓', 'success');
                        e.target.reset();
                        return false;
                    }
                } catch { /* Fall through to offline save */ }
            }

            await window.saveFormOffline('safety-incidents', data);
            e.target.reset();
            refreshQueueCounts();
            return false;
        }

        // ── Queue counts ────────────────────────────────────────
        async function refreshQueueCounts() {
            if (typeof InfraDB === 'undefined') return;
            try {
                const g = await InfraDB.count('form-queue');
                const d = await InfraDB.count('site-diaries');
                const a = await InfraDB.count('attendance');
                const s = await InfraDB.count('safety-incidents');
                const total = g + d + a + s;

                document.getElementById('q-generic').textContent = g;
                document.getElementById('q-diaries').textContent = d;
                document.getElementById('q-attendance').textContent = a;
                document.getElementById('q-safety').textContent = s;
                document.getElementById('q-total').textContent = total;

                const syncBtn = document.getElementById('btn-manual-sync');
                if (syncBtn) syncBtn.disabled = (total === 0) || !navigator.onLine;

                // Render pending items list
                await renderPendingItems();
            } catch (e) { console.warn('Queue count error:', e); }
        }

        async function renderPendingItems() {
            const panel = document.getElementById('pending-list-panel');
            const list = document.getElementById('pending-items-list');
            if (!panel || !list || typeof InfraDB === 'undefined') return;

            let html = '';
            // Generic form queue items
            try {
                const items = await InfraDB.getAll('form-queue');
                items.forEach(item => {
                    const time = new Date(item._createdAt).toLocaleString();
                    const fieldCount = Object.keys(item.data || {}).length;
                    html += `<div style="display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0;border-bottom:1px solid rgba(255,255,255,0.04);">
                        <div style="width:8px;height:8px;border-radius:50%;background:#818cf8;flex-shrink:0;"></div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.82rem;font-weight:600;color:#e2e8f0;">${item._label || item._resource}</div>
                            <div style="font-size:0.7rem;color:#64748b;">${time} · ${fieldCount} fields · ${item._action}</div>
                        </div>
                        <button onclick="removeQueueItem('form-queue','${item._offlineId}')" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.75rem;padding:0.25rem;">✕</button>
                    </div>`;
                });
            } catch(e) {}

            // Legacy store items
            for (const store of ['site-diaries', 'attendance', 'safety-incidents']) {
                try {
                    const items = await InfraDB.getAll(store);
                    items.forEach(item => {
                        const time = new Date(item._createdAt).toLocaleString();
                        html += `<div style="display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0;border-bottom:1px solid rgba(255,255,255,0.04);">
                            <div style="width:8px;height:8px;border-radius:50%;background:#4ade80;flex-shrink:0;"></div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:0.82rem;font-weight:600;color:#e2e8f0;">${store.replace(/-/g, ' ')}</div>
                                <div style="font-size:0.7rem;color:#64748b;">${time}</div>
                            </div>
                            <button onclick="removeQueueItem('${store}','${item._offlineId}')" style="background:none;border:none;color:#f87171;cursor:pointer;font-size:0.75rem;padding:0.25rem;">✕</button>
                        </div>`;
                    });
                } catch(e) {}
            }

            list.innerHTML = html || '<div style="text-align:center;padding:1rem;color:#64748b;font-size:0.82rem;">No pending items ✓</div>';
            panel.style.display = html ? 'block' : 'none';
        }

        async function removeQueueItem(store, id) {
            if (typeof InfraDB === 'undefined') return;
            await InfraDB.remove(store, id);
            refreshQueueCounts();
        }

        async function clearAllQueues() {
            if (!confirm('Clear all pending offline data? This cannot be undone.')) return;
            if (typeof InfraDB === 'undefined') return;
            for (const store of InfraDB.STORES) {
                await InfraDB.clear(store);
            }
            refreshQueueCounts();
        }

        // ── Connection status ───────────────────────────────────
        function updateConnectionUI() {
            const dot = document.getElementById('connection-dot');
            const label = document.getElementById('connection-label');
            const syncBtn = document.getElementById('btn-manual-sync');
            if (navigator.onLine) {
                dot.className = 'status-dot online';
                label.textContent = 'Online — data will sync automatically';
                if (syncBtn) syncBtn.disabled = false;
            } else {
                dot.className = 'status-dot offline';
                label.textContent = 'Offline — data is saved locally';
                if (syncBtn) syncBtn.disabled = true;
            }
        }

        // ── Manual sync ─────────────────────────────────────────
        async function manualSync() {
            if (!navigator.onLine) { alert('You are currently offline'); return; }
            const btn = document.getElementById('btn-manual-sync');
            if (btn) { btn.disabled = true; btn.textContent = 'Syncing...'; }

            if (typeof window.infrahubSync === 'function') {
                await window.infrahubSync();
            }

            if (btn) { btn.textContent = '↻ Sync Now'; btn.disabled = false; }
            refreshQueueCounts();
        }

        // ── Toast helper ────────────────────────────────────────
        function showFormToast(msg, type) {
            const toast = document.createElement('div');
            toast.style.cssText = `position:fixed;top:4rem;right:1rem;z-index:99999;padding:0.75rem 1.25rem;border-radius:12px;font-family:'Inter',sans-serif;font-size:0.82rem;font-weight:600;animation:fadeIn 0.3s ease;`;
            toast.style.background = type === 'success' ? 'rgba(6,78,59,0.95)' : 'rgba(30,58,138,0.95)';
            toast.style.color = type === 'success' ? '#a7f3d0' : '#bfdbfe';
            toast.style.border = type === 'success' ? '1px solid rgba(52,211,153,0.2)' : '1px solid rgba(96,165,250,0.2)';
            toast.textContent = msg;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
        }

        // ── Listen for SW sync completion ───────────────────────
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', (event) => {
                if (event.data?.type === 'SYNC_COMPLETE') {
                    refreshQueueCounts();
                    showFormToast('Background sync complete ✓', 'success');
                }
            });
        }

        // ── Boot ────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            loadDropdowns();
            refreshQueueCounts();
            updateConnectionUI();
            window.addEventListener('online', () => { updateConnectionUI(); refreshQueueCounts(); });
            window.addEventListener('offline', updateConnectionUI);
            setInterval(refreshQueueCounts, 15000);
        });
    </script>
</x-filament-panels::page>