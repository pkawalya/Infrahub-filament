@extends('mobile.layout')
@section('title', 'Crew Attendance — InfraHub Mobile')

@section('content')
    <div class="m-page-title" style="display:flex;align-items:center;gap:0.5rem;">
        <x-mobile.icon name="attendance" size="24" class="text-emerald-400" /> Crew Attendance
    </div>
    <div class="m-page-subtitle">Field staff roster & clock-in tracking</div>

    <div class="m-category-tabs">
        <button type="button" class="m-category-tab active" onclick="switchView('log')" id="tab-log">Today's Log</button>
        <button type="button" class="m-category-tab" onclick="switchView('clock')" id="tab-clock">Clock-in Worker</button>
    </div>

    {{-- Attendance Log View --}}
    <div id="view-log">
        <div id="attendance-container">
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
            <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
        </div>
    </div>

    {{-- Clock-in View --}}
    <div id="view-clock" style="display:none;">
        <form id="attendance-form" class="m-card" style="display:flex;flex-direction:column;gap:0.8rem;" onsubmit="handleAttendanceSubmit(event)">
            <div class="m-form-group">
                <label class="m-label">Worker Name / ID *</label>
                <input type="text" id="att-worker-name" placeholder="Worker Full Name or ID..." class="m-input" required>
            </div>
            <div class="m-form-group">
                <label class="m-label">Trade / Role *</label>
                <select id="att-trade" class="m-select" required>
                    <option value="General Mason">General Mason</option>
                    <option value="Steel Fixer">Steel Fixer</option>
                    <option value="Carpenter">Carpenter</option>
                    <option value="Electrician">Electrician</option>
                    <option value="Plumber">Plumber</option>
                    <option value="Heavy Operator">Heavy Machinery Operator</option>
                    <option value="Safety Officer">Safety Inspector</option>
                    <option value="Site Engineer">Site Engineer</option>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="m-form-group">
                    <label class="m-label">Shift</label>
                    <select id="att-shift" class="m-select">
                        <option value="Day">Day Shift</option>
                        <option value="Night">Night Shift</option>
                        <option value="Overtime">Overtime</option>
                    </select>
                </div>
                <div class="m-form-group">
                    <label class="m-label">Hours Worked</label>
                    <input type="number" id="att-hours" value="8" step="0.5" class="m-input" required>
                </div>
            </div>
            <div class="m-form-group">
                <button type="button" onclick="captureGPS()" class="m-btn m-btn-outline" id="gps-btn">
                    Capture Geolocation GPS
                </button>
                <input type="hidden" id="att-lat">
                <input type="hidden" id="att-lng">
            </div>
            <button type="submit" class="m-btn m-btn-primary">
                Clock-In Crew Member
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let attendanceLogs = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchAttendance();
        });

        async function fetchAttendance() {
            try {
                const res = await MobileAPI.get('/crew-attendances?per_page=30');
                if (res?.data) {
                    attendanceLogs = res.data;
                    localStorage.setItem('m_attendance', JSON.stringify(attendanceLogs));
                }
            } catch {
                const cached = localStorage.getItem('m_attendance');
                if (cached) attendanceLogs = JSON.parse(cached);
            }
            renderAttendance();
        }

        function switchView(v) {
            document.getElementById('view-log').style.display = v === 'log' ? 'block' : 'none';
            document.getElementById('view-clock').style.display = v === 'clock' ? 'block' : 'none';
            document.querySelectorAll('.m-category-tabs button').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + v).classList.add('active');
        }

        function captureGPS() {
            const btn = document.getElementById('gps-btn');
            if ('geolocation' in navigator) {
                btn.textContent = 'Locating GPS...';
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        document.getElementById('att-lat').value = pos.coords.latitude;
                        document.getElementById('att-lng').value = pos.coords.longitude;
                        btn.textContent = `GPS Verified (${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)}) ✓`;
                        btn.style.color = '#10b981';
                        btn.style.borderColor = 'rgba(16,185,129,0.3)';
                    },
                    () => {
                        btn.textContent = 'GPS location position estimated';
                    }
                );
            }
        }

        function renderAttendance() {
            const container = document.getElementById('attendance-container');
            if (!attendanceLogs || attendanceLogs.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg></div>
                        <div class="m-empty-title">No Attendance Recorded Today</div>
                    </div>`;
                return;
            }

            container.innerHTML = attendanceLogs.map(a => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">${esc(a.worker_name || a.worker?.name || 'Site Crew Member')}</div>
                            <div class="m-card-subtitle">${esc(a.trade || a.role || 'Construction Trade')}</div>
                        </div>
                        <span class="m-pill active"><span class="m-pill-dot"></span><span class="m-pill-text">${esc(a.shift_type || a.shift || 'Day')} (${a.hours_worked || 8}h)</span></span>
                    </div>
                    <div class="m-card-footer" style="margin-top:0.4rem;">
                        Date: ${esc(a.date || new Date().toISOString().slice(0,10))}
                        ${a.latitude ? ` · GPS Verified` : ''}
                    </div>
                </div>`).join('');
        }

        async function handleAttendanceSubmit(e) {
            e.preventDefault();
            const payload = {
                worker_name: document.getElementById('att-worker-name').value,
                trade: document.getElementById('att-trade').value,
                shift_type: document.getElementById('att-shift').value,
                hours_worked: parseFloat(document.getElementById('att-hours').value),
                date: new Date().toISOString().slice(0, 10),
                latitude: document.getElementById('att-lat').value || null,
                longitude: document.getElementById('att-lng').value || null,
            };

            if (!navigator.onLine && typeof window.saveFormOffline === 'function') {
                await window.saveFormOffline('attendance', payload);
                switchView('log');
                return;
            }

            try {
                const res = await MobileAPI.post('/crew-attendances', payload);
                if (res) {
                    MobileUI.toast('Worker Clocked-in ✓');
                    switchView('log');
                    fetchAttendance();
                }
            } catch {
                if (typeof window.saveFormOffline === 'function') {
                    await window.saveFormOffline('attendance', payload);
                    switchView('log');
                }
            }
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
