<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    @push('styles')
        <style>
            .field-weekly {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 8px;
                margin-bottom: 16px;
            }

            .field-weekly-item {
                padding: 14px 16px;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                background: white;
                text-align: center;
            }

            .dark .field-weekly-item {
                background: rgba(30, 41, 59, 0.5);
                border-color: rgba(255, 255, 255, 0.08);
            }

            .field-weekly-num {
                font-size: 22px;
                font-weight: 800;
                letter-spacing: -0.02em;
                line-height: 1.2;
            }

            .field-weekly-label {
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #64748b;
                margin-top: 2px;
            }

            .dark .field-weekly-label {
                color: #94a3b8;
            }

            .field-timeline {
                display: grid;
                gap: 6px;
                margin-bottom: 16px;
            }

            .field-log-row {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 14px;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                background: white;
                transition: all .12s;
            }

            .dark .field-log-row {
                background: rgba(30, 41, 59, 0.5);
                border-color: rgba(255, 255, 255, 0.08);
            }

            .field-log-row:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
            }

            .dark .field-log-row:hover {
                background: rgba(30, 41, 59, 0.8);
            }

            .field-log-date {
                font-size: 12px;
                font-weight: 700;
                color: #1e293b;
                min-width: 90px;
            }

            .dark .field-log-date {
                color: #e2e8f0;
            }

            .field-log-weather {
                font-size: 16px;
                min-width: 28px;
                text-align: center;
            }

            .field-log-info {
                flex: 1;
                min-width: 0;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 12px;
                color: #64748b;
            }

            .dark .field-log-info {
                color: #94a3b8;
            }

            .field-log-badge {
                padding: 2px 8px;
                border-radius: 99px;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
            }

            .field-section-title {
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #475569;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .dark .field-section-title {
                color: #cbd5e1;
            }
        </style>
    @endpush

    {{-- Weekly Summary Strip --}}
    @php $week = $this->getWeeklySummary(); @endphp
    <div class="field-section-title">
        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
        </svg>
        This Week · {{ now()->startOfWeek()->format('M d') }} – {{ now()->endOfWeek()->format('M d') }}
    </div>
    <div class="field-weekly">
        <div class="field-weekly-item">
            <div class="field-weekly-num" style="color:#4f46e5;">{{ $week['total_logs'] }}</div>
            <div class="field-weekly-label">Logs This Week</div>
        </div>
        <div class="field-weekly-item">
            <div class="field-weekly-num" style="color:#0891b2;">{{ $week['total_hours'] }}h</div>
            <div class="field-weekly-label">Hours Tracked</div>
        </div>
        <div class="field-weekly-item">
            <div class="field-weekly-num" style="color:#059669;">{{ $week['avg_workers'] }}</div>
            <div class="field-weekly-label">Avg Workers/Day</div>
        </div>
        <div class="field-weekly-item">
            <div class="field-weekly-num" style="color:#16a34a;">{{ $week['approved'] }}</div>
            <div class="field-weekly-label">Approved</div>
        </div>
        <div class="field-weekly-item">
            @if($week['pending'] > 0)
                <div class="field-weekly-num" style="color:#d97706;">{{ $week['pending'] }}</div>
            @else
                <div class="field-weekly-num" style="color:#10b981;">0</div>
            @endif
            <div class="field-weekly-label">Pending Review</div>
        </div>
    </div>

    {{-- Recent Logs Timeline --}}
    @php $recentLogs = $this->getRecentLogs(); @endphp
    @if($recentLogs->isNotEmpty())
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">Recent Site Logs</x-slot>
            <x-slot name="description">Last 7 entries</x-slot>

            <div class="field-timeline">
                @foreach($recentLogs as $log)
                    @php
                        $weatherEmoji = match ($log->weather) {
                            'sunny' => '☀️', 'partly_cloudy' => '⛅', 'cloudy' => '☁️',
                            'rainy' => '🌧️', 'stormy' => '⛈️', 'windy' => '💨', 'foggy' => '🌫️',
                            default => '—'
                        };
                        $statusStyle = match ($log->status) {
                            'approved' => 'background:rgba(16,185,129,0.1);color:#10b981;',
                            'submitted' => 'background:rgba(59,130,246,0.1);color:#3b82f6;',
                            'rejected' => 'background:rgba(239,68,68,0.1);color:#ef4444;',
                            default => 'background:rgba(107,114,128,0.1);color:#6b7280;',
                        };
                    @endphp
                    <div class="field-log-row">
                        <div class="field-log-date">{{ $log->log_date->format('D, M d') }}</div>
                        <div class="field-log-weather">{{ $weatherEmoji }}</div>
                        <div class="field-log-info">
                            <span>👷 {{ $log->workers_on_site ?? 0 }}</span>
                            <span>📋 {{ $log->taskEntries->count() }} tasks</span>
                            <span>⏱ {{ $log->taskEntries->sum('hours_worked') }}h</span>
                            @if($log->creator)
                                <span>· {{ $log->creator->name }}</span>
                            @endif
                        </div>
                        <span class="field-log-badge" style="{{ $statusStyle }}">
                            {{ \App\Models\DailySiteLog::$statuses[$log->status] ?? $log->status }}
                        </span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    {{-- Main Table --}}
    {{ $this->table }}
</x-filament-panels::page>