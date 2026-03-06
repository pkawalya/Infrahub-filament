<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    @push('styles')
        <style>
            /* ─── SHEQ Safety Dashboard ─── */
            .sheq-dashboard {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 16px;
            }

            @media(max-width:768px) {
                .sheq-dashboard {
                    grid-template-columns: 1fr;
                }
            }

            .sheq-card {
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 16px;
                position: relative;
                overflow: hidden;
            }

            .dark .sheq-card {
                background: rgba(30, 41, 59, 0.5);
                border-color: rgba(255, 255, 255, 0.08);
            }

            .sheq-card-title {
                font-size: 10px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #64748b;
                display: flex;
                align-items: center;
                gap: 5px;
                margin-bottom: 10px;
            }

            .dark .sheq-card-title {
                color: #94a3b8;
            }

            .sheq-card-title svg {
                width: 13px;
                height: 13px;
            }

            /* KPI Hero Strip */
            .sheq-kpi-strip {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
                gap: 6px;
                margin-bottom: 16px;
            }

            .sheq-kpi {
                padding: 14px 12px;
                border-radius: 10px;
                text-align: center;
                border: 1px solid #e2e8f0;
                background: white;
                position: relative;
            }

            .dark .sheq-kpi {
                background: rgba(30, 41, 59, 0.5);
                border-color: rgba(255, 255, 255, 0.08);
            }

            .sheq-kpi-num {
                font-size: 26px;
                font-weight: 800;
                line-height: 1.1;
                letter-spacing: -0.02em;
            }

            .sheq-kpi-label {
                font-size: 9px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #64748b;
                margin-top: 3px;
            }

            .dark .sheq-kpi-label {
                color: #94a3b8;
            }

            .sheq-kpi-sub {
                font-size: 9px;
                color: #94a3b8;
                margin-top: 1px;
            }

            /* Severity Heatmap */
            .sev-bar-row {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 5px;
            }

            .sev-bar-label {
                width: 65px;
                font-size: 11px;
                font-weight: 600;
                color: #475569;
                text-align: right;
            }

            .dark .sev-bar-label {
                color: #94a3b8;
            }

            .sev-bar-track {
                flex: 1;
                height: 20px;
                background: #f1f5f9;
                border-radius: 4px;
                overflow: hidden;
                position: relative;
            }

            .dark .sev-bar-track {
                background: rgba(255, 255, 255, 0.05);
            }

            .sev-bar-fill {
                height: 100%;
                border-radius: 4px;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                padding-right: 6px;
                font-size: 10px;
                font-weight: 700;
                color: white;
                transition: width .4s ease;
                min-width: 0;
            }

            .sev-bar-count {
                width: 28px;
                font-size: 12px;
                font-weight: 700;
                color: #334155;
                text-align: center;
            }

            .dark .sev-bar-count {
                color: #e2e8f0;
            }

            /* Trend Chart */
            .trend-chart {
                display: flex;
                align-items: flex-end;
                gap: 3px;
                height: 80px;
            }

            .trend-bar-wrap {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .trend-bar-group {
                display: flex;
                gap: 1px;
                align-items: flex-end;
                height: 60px;
                width: 100%;
            }

            .trend-bar {
                flex: 1;
                border-radius: 2px 2px 0 0;
                transition: height .3s;
                min-height: 2px;
            }

            .trend-label {
                font-size: 8px;
                font-weight: 600;
                color: #94a3b8;
                margin-top: 2px;
            }

            .trend-legend {
                display: flex;
                gap: 12px;
                font-size: 9px;
                color: #64748b;
                margin-top: 6px;
            }

            .trend-legend span::before {
                content: '';
                display: inline-block;
                width: 8px;
                height: 8px;
                border-radius: 2px;
                margin-right: 4px;
                vertical-align: middle;
            }

            .trend-legend .all::before {
                background: #6366f1;
            }

            .trend-legend .crit::before {
                background: #ef4444;
            }

            /* Alert Strips */
            .sheq-alert {
                display: flex;
                align-items: flex-start;
                gap: 8px;
                padding: 10px 14px;
                border-radius: 8px;
                margin-bottom: 10px;
                font-size: 12px;
                line-height: 1.4;
            }

            .sheq-alert svg {
                width: 16px;
                height: 16px;
                flex-shrink: 0;
                margin-top: 1px;
            }

            .sheq-alert.danger {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #991b1b;
            }

            .dark .sheq-alert.danger {
                background: rgba(220, 38, 38, 0.08);
                border-color: rgba(220, 38, 38, 0.15);
                color: #f87171;
            }

            .sheq-alert.warning {
                background: #fffbeb;
                border: 1px solid #fde68a;
                color: #92400e;
            }

            .dark .sheq-alert.warning {
                background: rgba(217, 119, 6, 0.08);
                border-color: rgba(217, 119, 6, 0.15);
                color: #fbbf24;
            }

            .sheq-alert.info {
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                color: #1e40af;
            }

            .dark .sheq-alert.info {
                background: rgba(37, 99, 235, 0.08);
                border-color: rgba(37, 99, 235, 0.15);
                color: #60a5fa;
            }

            /* Upcoming Inspections */
            .insp-row {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 0;
                border-bottom: 1px solid #f1f5f9;
                font-size: 12px;
            }

            .dark .insp-row {
                border-bottom-color: rgba(255, 255, 255, 0.04);
            }

            .insp-row:last-child {
                border-bottom: none;
            }

            .insp-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                flex-shrink: 0;
            }

            .insp-title {
                flex: 1;
                font-weight: 600;
                color: #334155;
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .dark .insp-title {
                color: #e2e8f0;
            }

            .insp-meta {
                font-size: 11px;
                color: #64748b;
                white-space: nowrap;
            }

            .dark .insp-meta {
                color: #94a3b8;
            }

            /* Tab Navigation */
            .sheq-tabs {
                display: flex;
                gap: 4px;
                border-bottom: 2px solid #e5e7eb;
                margin-bottom: 1rem;
                overflow-x: auto;
            }

            .dark .sheq-tabs {
                border-bottom-color: rgba(255, 255, 255, 0.06);
            }

            .sheq-tab {
                padding: 10px 20px;
                font-size: 13px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                border-radius: 8px 8px 0 0;
                transition: all 0.2s;
                background: transparent;
                color: #6b7280;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                white-space: nowrap;
            }

            .dark .sheq-tab {
                color: #9ca3af;
            }

            .sheq-tab:hover {
                opacity: 0.85;
            }

            .sheq-tab.active {
                background: var(--primary-600, #2563eb);
                color: white;
            }

            .sheq-tab .tab-icon {
                width: 1rem;
                height: 1rem;
                flex-shrink: 0;
            }

            .sheq-tab.active .tab-icon {
                color: white;
            }

            .sheq-tab .badge {
                font-size: 11px;
                padding: 2px 7px;
                border-radius: 99px;
                margin-left: 4px;
            }

            .sheq-tab .badge-danger {
                background: rgba(239, 68, 68, 0.15);
                color: #ef4444;
            }

            .sheq-tab.active .badge-danger {
                background: rgba(255, 255, 255, 0.2);
                color: white;
            }

            .sheq-tab .badge-warning {
                background: rgba(245, 158, 11, 0.15);
                color: #f59e0b;
            }

            .sheq-tab.active .badge-warning {
                background: rgba(255, 255, 255, 0.2);
                color: white;
            }

            .sheq-tab .badge-info {
                background: rgba(99, 102, 241, 0.15);
                color: #6366f1;
            }

            .sheq-tab.active .badge-info {
                background: rgba(255, 255, 255, 0.2);
                color: white;
            }

            /* Snag/Social Summary */
            .sheq-snag-summary {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
                margin-bottom: 1rem;
            }

            @media(max-width:640px) {
                .sheq-snag-summary {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            .sheq-snag-stat {
                padding: 14px;
                border-radius: 10px;
                text-align: center;
            }

            .sheq-snag-stat.default {
                background: #f9fafb;
                border: 1px solid #e5e7eb;
            }

            .dark .sheq-snag-stat.default {
                background: rgba(255, 255, 255, 0.02);
                border-color: rgba(255, 255, 255, 0.06);
            }

            .sheq-snag-stat .stat-num {
                font-size: 22px;
                font-weight: 800;
            }

            .sheq-snag-stat .stat-label {
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: 600;
            }

            .sheq-muted {
                color: #6b7280;
            }

            .dark .sheq-muted {
                color: #9ca3af;
            }
        </style>
    @endpush

    {{-- ═══ SAFETY DASHBOARD ═══ --}}
    @php
        $kpi = $this->getSafetyKPIs();
        $critAlerts = $this->getCriticalAlerts();
        $overdueSnagList = $this->getOverdueSnags();
    @endphp

    {{-- Critical Incident Alert Banner --}}
    @if($critAlerts->isNotEmpty())
        <div class="sheq-alert danger">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <div>
                <strong>🚨 {{ $critAlerts->count() }} Critical/Fatal Incident{{ $critAlerts->count() > 1 ? 's' : '' }}
                    Require Immediate Attention</strong><br>
                @foreach($critAlerts->take(3) as $alert)
                    <span style="opacity:0.9;">{{ $alert->incident_number }}:
                        {{ \Illuminate\Support\Str::limit($alert->title, 50) }}
                        ({{ self::$severities[$alert->severity] ?? $alert->severity }}){{ !$loop->last ? ' · ' : '' }}</span>
                @endforeach
                @if($critAlerts->count() > 3)
                    <span style="opacity:0.7;">… and {{ $critAlerts->count() - 3 }} more</span>
                @endif
            </div>
        </div>
    @endif

    {{-- Overdue Inspections Alert --}}
    @if($kpi['overdue_inspections'] > 0)
        <div class="sheq-alert warning">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><strong>{{ $kpi['overdue_inspections'] }} overdue
                    inspection{{ $kpi['overdue_inspections'] > 1 ? 's' : '' }}</strong> — scheduled date has passed without
                completion.</span>
        </div>
    @endif

    {{-- Overdue Snags Alert --}}
    @if($overdueSnagList->isNotEmpty())
        <div class="sheq-alert warning">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 12.75c1.148 0 2.278.08 3.383.237 1.037.146 1.866.966 1.866 2.013 0 3.728-2.35 6.75-5.25 6.75S6.75 18.728 6.75 15c0-1.046.83-1.867 1.866-2.013A24.204 24.204 0 0112 12.75z" />
            </svg>
            <span><strong>{{ $overdueSnagList->count() }} overdue
                    snag{{ $overdueSnagList->count() > 1 ? 's' : '' }}:</strong>
                {{ $overdueSnagList->take(4)->pluck('title')->map(fn($t) => \Illuminate\Support\Str::limit($t, 30))->join(', ') }}{{ $overdueSnagList->count() > 4 ? '…' : '' }}
            </span>
        </div>
    @endif

    {{-- KPI Hero Strip --}}
    <div class="sheq-kpi-strip">
        <div class="sheq-kpi">
            @php
                $safeDayColor = $kpi['safe_days'] !== null
                    ? ($kpi['safe_days'] > 30 ? '#10b981' : ($kpi['safe_days'] > 7 ? '#d97706' : '#ef4444'))
                    : '#94a3b8';
            @endphp
            <div class="sheq-kpi-num" style="color:{{ $safeDayColor }};">
                {{ $kpi['safe_days'] !== null ? $kpi['safe_days'] : '∞' }}
            </div>
            <div class="sheq-kpi-label">Days Since Incident</div>
            @if($kpi['safe_days'] !== null && $kpi['safe_days'] > 30)
                <div class="sheq-kpi-sub">🏆 Great streak!</div>
            @endif
        </div>

        <div class="sheq-kpi">
            <div class="sheq-kpi-num"
                style="color:{{ $kpi['resolution_rate'] >= 80 ? '#10b981' : ($kpi['resolution_rate'] >= 50 ? '#d97706' : '#ef4444') }};">
                {{ $kpi['resolution_rate'] }}%
            </div>
            <div class="sheq-kpi-label">Resolution Rate</div>
            <div class="sheq-kpi-sub">incidents closed</div>
        </div>

        <div class="sheq-kpi">
            <div class="sheq-kpi-num" style="color:#6366f1;">
                {{ $kpi['avg_resolution_days'] ?? '—' }}
            </div>
            <div class="sheq-kpi-label">Avg Resolution</div>
            <div class="sheq-kpi-sub">days to close</div>
        </div>

        <div class="sheq-kpi">
            <div class="sheq-kpi-num" style="color:#0891b2;">{{ $kpi['total_inspections'] }}</div>
            <div class="sheq-kpi-label">Inspections</div>
            <div class="sheq-kpi-sub">{{ $kpi['inspection_completion_rate'] }}% done</div>
        </div>

        <div class="sheq-kpi">
            <div class="sheq-kpi-num"
                style="color:{{ ($kpi['avg_inspection_score'] ?? 0) >= 80 ? '#10b981' : '#d97706' }};">
                {{ $kpi['avg_inspection_score'] ?? '—' }}
            </div>
            <div class="sheq-kpi-label">Avg Score</div>
            <div class="sheq-kpi-sub">inspection quality</div>
        </div>

        <div class="sheq-kpi">
            @if($kpi['open_snags'] > 0)
                <div class="sheq-kpi-num" style="color:{{ $kpi['overdue_snags'] > 0 ? '#ef4444' : '#d97706' }};">
                    {{ $kpi['open_snags'] }}</div>
            @else
                <div class="sheq-kpi-num" style="color:#10b981;">0</div>
            @endif
            <div class="sheq-kpi-label">Open Snags</div>
            @if($kpi['overdue_snags'] > 0)
                <div class="sheq-kpi-sub" style="color:#ef4444;">{{ $kpi['overdue_snags'] }} overdue</div>
            @else
                <div class="sheq-kpi-sub">all on track</div>
            @endif
        </div>
    </div>

    {{-- Dashboard Row: Severity + Trend + Upcoming Inspections --}}
    @php
        $trend = $this->getIncidentTrend();
        $maxTrend = max(1, max(array_column($trend, 'count') ?: [1]));
        $sevOrder = ['fatal', 'critical', 'major', 'moderate', 'minor'];
        $sevColors = ['fatal' => '#7f1d1d', 'critical' => '#dc2626', 'major' => '#ea580c', 'moderate' => '#d97706', 'minor' => '#6b7280'];
        $sevTotal = array_sum($kpi['severities']);
        $upcomingInsps = $this->getUpcomingInspections();
    @endphp

    <div class="sheq-dashboard">
        {{-- Severity Distribution --}}
        <div class="sheq-card">
            <div class="sheq-card-title">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625z" />
                </svg>
                Incident Severity Distribution
            </div>
            @if($sevTotal > 0)
                @foreach($sevOrder as $sev)
                    @php $cnt = $kpi['severities'][$sev] ?? 0; @endphp
                    @if($cnt > 0)
                        <div class="sev-bar-row">
                            <div class="sev-bar-label">{{ ucfirst($sev) }}</div>
                            <div class="sev-bar-track">
                                <div class="sev-bar-fill"
                                    style="width:{{ ($cnt / $sevTotal) * 100 }}%;background:{{ $sevColors[$sev] ?? '#6b7280' }};">
                                    @if(($cnt / $sevTotal) > 0.15)
                                        {{ $cnt }}
                                    @endif
                                </div>
                            </div>
                            <div class="sev-bar-count">{{ $cnt }}</div>
                        </div>
                    @endif
                @endforeach
            @else
                <div style="text-align:center;padding:20px 0;color:#94a3b8;font-size:13px;">
                    <svg style="width:32px;height:32px;margin:0 auto 6px;opacity:0.4;" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    No incidents recorded — excellent safety record!
                </div>
            @endif
        </div>

        {{-- Incident Trend (6 months) --}}
        <div class="sheq-card">
            <div class="sheq-card-title">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                </svg>
                Incident Trend · 6 Months
            </div>
            <div class="trend-chart">
                @foreach($trend as $t)
                    <div class="trend-bar-wrap">
                        <div class="trend-bar-group">
                            <div class="trend-bar"
                                style="background:#6366f1;height:{{ max(2, ($t['count'] / $maxTrend) * 55) }}px;"
                                title="All: {{ $t['count'] }}"></div>
                            @if($t['critical'] > 0)
                                <div class="trend-bar"
                                    style="background:#ef4444;height:{{ max(2, ($t['critical'] / $maxTrend) * 55) }}px;"
                                    title="Critical: {{ $t['critical'] }}"></div>
                            @endif
                        </div>
                        <div class="trend-label">{{ $t['label'] }}</div>
                    </div>
                @endforeach
            </div>
            <div class="trend-legend">
                <span class="all">All Incidents</span>
                <span class="crit">Critical/Fatal</span>
            </div>
        </div>
    </div>

    {{-- Upcoming Inspections Tracker --}}
    @if($upcomingInsps->isNotEmpty())
        <x-filament::section collapsible>
            <x-slot name="heading">
                <span style="display:flex;align-items:center;gap:6px;">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    Upcoming Inspections
                    @if($kpi['upcoming_inspections'] > 0)
                        <span
                            style="background:#dbeafe;color:#2563eb;font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;">{{ $kpi['upcoming_inspections'] }}
                            this week</span>
                    @endif
                </span>
            </x-slot>
            <x-slot name="description">Scheduled inspections within 7 days</x-slot>

            @foreach($upcomingInsps as $insp)
                @php
                    $isOverdue = $insp->scheduled_date && $insp->scheduled_date->isPast();
                    $dotColor = $isOverdue ? '#ef4444' : ($insp->status === 'in_progress' ? '#f59e0b' : '#10b981');
                @endphp
                <div class="insp-row">
                    <div class="insp-dot"
                        style="background:{{ $dotColor }};{{ $isOverdue ? 'box-shadow:0 0 0 3px rgba(239,68,68,0.15);' : '' }}">
                    </div>
                    <div class="insp-title">{{ $insp->title }}</div>
                    <div class="insp-meta">
                        @if($insp->inspector)
                            👤 {{ $insp->inspector->name }} ·
                        @endif
                        📅 {{ $insp->scheduled_date?->format('M d') ?? '—' }}
                        @if($isOverdue)
                            <span style="color:#ef4444;font-weight:700;"> OVERDUE</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </x-filament::section>
    @endif

    {{-- Tab Navigation --}}
    <div class="sheq-tabs">
        {{-- Incidents --}}
        <button wire:click="$set('activeTab', 'incidents')"
            class="sheq-tab {{ $this->activeTab === 'incidents' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="tab-icon">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            Incidents
            @php $incidentCount = \App\Models\SafetyIncident::where('cde_project_id', $this->record->id)->whereIn('status', ['reported', 'investigating'])->count(); @endphp
            @if($incidentCount > 0)
                <span class="badge badge-danger">{{ $incidentCount }}</span>
            @endif
        </button>

        {{-- Inspections --}}
        <button wire:click="$set('activeTab', 'inspections')"
            class="sheq-tab {{ $this->activeTab === 'inspections' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="tab-icon">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" />
            </svg>
            Inspections
            @if($kpi['overdue_inspections'] > 0)
                <span class="badge badge-danger">{{ $kpi['overdue_inspections'] }}</span>
            @endif
        </button>

        {{-- Snags --}}
        <button wire:click="$set('activeTab', 'snags')"
            class="sheq-tab {{ $this->activeTab === 'snags' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="tab-icon">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 12.75c1.148 0 2.278.08 3.383.237 1.037.146 1.866.966 1.866 2.013 0 3.728-2.35 6.75-5.25 6.75S6.75 18.728 6.75 15c0-1.046.83-1.867 1.866-2.013A24.204 24.204 0 0112 12.75zm0 0c2.883 0 5.647.508 8.207 1.44a23.91 23.91 0 01-1.152-6.135c-.117-1.502-.553-2.914-1.238-4.211a3.002 3.002 0 00-2.19-1.59A21.047 21.047 0 0012 2.25a21.047 21.047 0 00-3.626.315 3.002 3.002 0 00-2.19 1.59 12.297 12.297 0 00-1.238 4.211 23.91 23.91 0 01-1.152 6.134A24.088 24.088 0 0112 12.75z" />
            </svg>
            Snags / Defects
            @php $snagCount = \App\Models\SnagItem::where('cde_project_id', $this->record->id)->whereIn('status', ['open', 'in_progress'])->count(); @endphp
            @if($snagCount > 0)
                <span class="badge badge-warning">{{ $snagCount }}</span>
            @endif
        </button>

        {{-- Social --}}
        <button wire:click="$set('activeTab', 'social')"
            class="sheq-tab {{ $this->activeTab === 'social' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="tab-icon">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
            </svg>
            Social
            @php $socialCount = \App\Models\SocialRecord::where('cde_project_id', $this->record->id)->whereIn('status', ['open', 'in_progress'])->count(); @endphp
            @if($socialCount > 0)
                <span class="badge badge-info">{{ $socialCount }}</span>
            @endif
        </button>
    </div>

    @if($this->activeTab === 'incidents')
        {{ $this->table }}

    @elseif($this->activeTab === 'inspections')
        {{-- Inspection scoring summary --}}
        @php
            $inspBase = \App\Models\SafetyInspection::where('cde_project_id', $this->record->id);
            $totalInsp = (clone $inspBase)->count();
            $completedInsp = (clone $inspBase)->where('status', 'completed')->count();
            $scheduledInsp = (clone $inspBase)->where('status', 'scheduled')->count();
            $avgScoreVal = (clone $inspBase)->where('status', 'completed')->whereNotNull('score')->avg('score');
        @endphp
        <div class="sheq-snag-summary">
            <div class="sheq-snag-stat default">
                <div class="stat-num">{{ $totalInsp }}</div>
                <div class="stat-label sheq-muted">Total</div>
            </div>
            <div class="sheq-snag-stat" style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.1);">
                <div class="stat-num" style="color:#10b981;">{{ $completedInsp }}</div>
                <div class="stat-label" style="color:#10b981;">Completed</div>
            </div>
            <div class="sheq-snag-stat" style="background:rgba(59,130,246,0.05);border:1px solid rgba(59,130,246,0.1);">
                <div class="stat-num" style="color:#3b82f6;">{{ $scheduledInsp }}</div>
                <div class="stat-label" style="color:#3b82f6;">Scheduled</div>
            </div>
            <div class="sheq-snag-stat" style="background:rgba(99,102,241,0.05);border:1px solid rgba(99,102,241,0.1);">
                <div class="stat-num" style="color:#6366f1;">{{ $avgScoreVal ? round($avgScoreVal) . '/100' : '—' }}</div>
                <div class="stat-label" style="color:#6366f1;">Avg Score</div>
            </div>
        </div>
        {{ $this->table }}

    @elseif($this->activeTab === 'snags')
        {{-- Snag summary strip --}}
        @php
            $snagStats = \App\Models\SnagItem::where('cde_project_id', $this->record->id);
            $totalSnags = (clone $snagStats)->count();
            $openSnags = (clone $snagStats)->whereIn('status', ['open', 'in_progress'])->count();
            $resolvedSnags = (clone $snagStats)->whereIn('status', ['resolved', 'verified', 'closed'])->count();
            $criticalSnags = (clone $snagStats)->where('severity', 'critical')->count();
            $overdueSnagCount = (clone $snagStats)->whereIn('status', ['open', 'in_progress'])
                ->whereNotNull('due_date')->where('due_date', '<', now())->count();
        @endphp

        <div class="sheq-snag-summary" style="grid-template-columns:repeat(5,1fr);">
            <div class="sheq-snag-stat default">
                <div class="stat-num">{{ $totalSnags }}</div>
                <div class="stat-label sheq-muted">Total Snags</div>
            </div>
            <div class="sheq-snag-stat" style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.1);">
                <div class="stat-num" style="color:#ef4444;">{{ $openSnags }}</div>
                <div class="stat-label" style="color:#ef4444;">Open</div>
            </div>
            <div class="sheq-snag-stat" style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.1);">
                <div class="stat-num" style="color:#10b981;">{{ $resolvedSnags }}</div>
                <div class="stat-label" style="color:#10b981;">Resolved</div>
            </div>
            <div class="sheq-snag-stat" style="background:rgba(245,158,11,0.05);border:1px solid rgba(245,158,11,0.1);">
                <div class="stat-num" style="color:#f59e0b;">{{ $criticalSnags }}</div>
                <div class="stat-label" style="color:#f59e0b;">Critical</div>
            </div>
            <div class="sheq-snag-stat"
                style="background:{{ $overdueSnagCount > 0 ? 'rgba(220,38,38,0.05)' : 'rgba(16,185,129,0.05)' }};border:1px solid {{ $overdueSnagCount > 0 ? 'rgba(220,38,38,0.1)' : 'rgba(16,185,129,0.1)' }};">
                <div class="stat-num" style="color:{{ $overdueSnagCount > 0 ? '#dc2626' : '#10b981' }};">
                    {{ $overdueSnagCount }}</div>
                <div class="stat-label" style="color:{{ $overdueSnagCount > 0 ? '#dc2626' : '#10b981' }};">Overdue</div>
            </div>
        </div>

        {{ $this->table }}

    @elseif($this->activeTab === 'social')
        {{-- Social summary strip --}}
        @php
            $socialStats = \App\Models\SocialRecord::where('cde_project_id', $this->record->id);
            $totalSocial = (clone $socialStats)->count();
            $openSocial = (clone $socialStats)->whereIn('status', ['open', 'in_progress'])->count();
            $resolvedSocial = (clone $socialStats)->whereIn('status', ['resolved', 'closed'])->count();
            $grievances = (clone $socialStats)->where('category', 'grievance')->whereIn('status', ['open', 'in_progress'])->count();
        @endphp

        <div class="sheq-snag-summary">
            <div class="sheq-snag-stat default">
                <div class="stat-num">{{ $totalSocial }}</div>
                <div class="stat-label sheq-muted">Total Records</div>
            </div>
            <div class="sheq-snag-stat" style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.1);">
                <div class="stat-num" style="color:#ef4444;">{{ $openSocial }}</div>
                <div class="stat-label" style="color:#ef4444;">Open</div>
            </div>
            <div class="sheq-snag-stat" style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.1);">
                <div class="stat-num" style="color:#10b981;">{{ $resolvedSocial }}</div>
                <div class="stat-label" style="color:#10b981;">Resolved</div>
            </div>
            <div class="sheq-snag-stat" style="background:rgba(139,92,246,0.05);border:1px solid rgba(139,92,246,0.1);">
                <div class="stat-num" style="color:#8b5cf6;">{{ $grievances }}</div>
                <div class="stat-label" style="color:#8b5cf6;">Open Grievances</div>
            </div>
        </div>

        {{ $this->table }}
    @endif
</x-filament-panels::page>