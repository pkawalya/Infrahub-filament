<x-filament-panels::page>

    @push('styles')
        <style>
            /* ─── Reporting Dashboard Styles ─── */
            .rpt-grid-2 {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .rpt-grid-3 {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            @media (max-width: 768px) {

                .rpt-grid-2,
                .rpt-grid-3 {
                    grid-template-columns: 1fr;
                }
            }

            /* Section Cards */
            .rpt-card {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 1rem;
                padding: 1.5rem;
                transition: box-shadow 200ms, transform 200ms;
            }

            .rpt-card:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07);
                transform: translateY(-1px);
            }

            .dark .rpt-card {
                background: rgba(255, 255, 255, 0.04);
                border-color: rgba(255, 255, 255, 0.08);
            }

            .rpt-card-title {
                font-size: 0.8125rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #374151;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .dark .rpt-card-title {
                color: #d1d5db;
            }

            .rpt-card-title svg {
                width: 1.125rem;
                height: 1.125rem;
            }

            /* Task Status Bar Chart */
            .rpt-bar-chart {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .rpt-bar-row {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .rpt-bar-label {
                width: 90px;
                font-size: 12px;
                font-weight: 600;
                color: #6b7280;
                text-align: right;
            }

            .dark .rpt-bar-label {
                color: #9ca3af;
            }

            .rpt-bar-track {
                flex: 1;
                height: 28px;
                background: #f3f4f6;
                border-radius: 6px;
                overflow: hidden;
                position: relative;
            }

            .dark .rpt-bar-track {
                background: rgba(255, 255, 255, 0.05);
            }

            .rpt-bar-fill {
                height: 100%;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                padding-right: 10px;
                font-size: 12px;
                font-weight: 700;
                color: white;
                transition: width 600ms cubic-bezier(.4, 0, .2, 1);
                min-width: 0;
            }

            .rpt-bar-count {
                width: 40px;
                font-size: 13px;
                font-weight: 700;
                color: #374151;
                text-align: center;
            }

            .dark .rpt-bar-count {
                color: #e5e7eb;
            }

            /* Module Summary Table */
            .rpt-mod-table {
                width: 100%;
                font-size: 13px;
                border-collapse: collapse;
            }

            .rpt-mod-table th {
                text-align: left;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #9ca3af;
                padding: 8px 12px;
                border-bottom: 1px solid #e5e7eb;
            }

            .dark .rpt-mod-table th {
                border-bottom-color: rgba(255, 255, 255, 0.06);
            }

            .rpt-mod-table td {
                padding: 10px 12px;
                border-bottom: 1px solid #f3f4f6;
            }

            .dark .rpt-mod-table td {
                border-bottom-color: rgba(255, 255, 255, 0.04);
            }

            .rpt-mod-name {
                font-weight: 600;
                color: #111827;
            }

            .dark .rpt-mod-name {
                color: #f3f4f6;
            }

            .rpt-mod-count {
                font-weight: 700;
                color: #374151;
            }

            .dark .rpt-mod-count {
                color: #e5e7eb;
            }

            .rpt-mod-open {
                font-weight: 600;
                font-size: 12px;
            }

            .rpt-mod-icon {
                font-size: 16px;
            }

            /* Milestone Timeline */
            .rpt-milestone {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .dark .rpt-milestone {
                border-bottom-color: rgba(255, 255, 255, 0.04);
            }

            .rpt-milestone:last-child {
                border-bottom: none;
            }

            .rpt-ms-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                flex-shrink: 0;
            }

            .rpt-ms-title {
                font-size: 13px;
                font-weight: 600;
                flex: 1;
                color: #374151;
            }

            .dark .rpt-ms-title {
                color: #e5e7eb;
            }

            .rpt-ms-date {
                font-size: 12px;
                color: #6b7280;
                white-space: nowrap;
            }

            .dark .rpt-ms-date {
                color: #9ca3af;
            }

            .rpt-ms-badge {
                font-size: 10px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                padding: 2px 8px;
                border-radius: 99px;
                white-space: nowrap;
            }

            /* Progress Ring */
            .rpt-progress-ring {
                position: relative;
                width: 120px;
                height: 120px;
                margin: 0 auto 1rem;
            }

            .rpt-progress-ring svg {
                transform: rotate(-90deg);
            }

            .rpt-progress-ring .ring-bg {
                fill: none;
                stroke: #e5e7eb;
                stroke-width: 10;
            }

            .dark .rpt-progress-ring .ring-bg {
                stroke: rgba(255, 255, 255, 0.08);
            }

            .rpt-progress-ring .ring-fill {
                fill: none;
                stroke-width: 10;
                stroke-linecap: round;
                transition: stroke-dashoffset 800ms cubic-bezier(.4, 0, .2, 1);
            }

            .rpt-progress-center {
                position: absolute;
                inset: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .rpt-progress-num {
                font-size: 1.75rem;
                font-weight: 800;
                color: #111827;
            }

            .dark .rpt-progress-num {
                color: #f3f4f6;
            }

            .rpt-progress-label {
                font-size: 11px;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .dark .rpt-progress-label {
                color: #9ca3af;
            }
        </style>
    @endpush

    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    @php
        $taskBreakdown = $this->getTaskBreakdown();
        $totalTasks = array_sum($taskBreakdown);
        $donePercent = $totalTasks > 0 ? round(($taskBreakdown['done'] / $totalTasks) * 100) : 0;
        $milestones = $this->getMilestoneOverview();
        $moduleSummary = $this->getModuleSummary();

        $barColors = [
            'todo' => '#94a3b8',
            'in_progress' => '#3b82f6',
            'review' => '#f59e0b',
            'done' => '#10b981',
            'blocked' => '#ef4444',
        ];
        $barLabels = [
            'todo' => 'To Do',
            'in_progress' => 'In Progress',
            'review' => 'Review',
            'done' => 'Done',
            'blocked' => 'Blocked',
        ];
        $circumference = 2 * 3.14159 * 50;
        $offset = $circumference - ($donePercent / 100) * $circumference;
        $progressColor = $donePercent >= 75 ? '#10b981' : ($donePercent >= 40 ? '#f59e0b' : '#3b82f6');
    @endphp

    <div class="rpt-grid-2">
        {{-- Task Status Breakdown --}}
        <div class="rpt-card">
            <div class="rpt-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                </svg>
                Task Status Breakdown
            </div>
            @if($totalTasks === 0)
                <div style="text-align:center; padding:2rem; color:#6b7280; font-size:13px;">
                    No tasks created yet.
                </div>
            @else
                <div class="rpt-bar-chart">
                    @foreach($taskBreakdown as $status => $count)
                        @php $pct = $totalTasks > 0 ? round(($count / $totalTasks) * 100) : 0; @endphp
                        <div class="rpt-bar-row">
                            <div class="rpt-bar-label">{{ $barLabels[$status] ?? ucfirst($status) }}</div>
                            <div class="rpt-bar-track">
                                <div class="rpt-bar-fill"
                                    style="width: {{ max($pct, ($count > 0 ? 8 : 0)) }}%; background: {{ $barColors[$status] ?? '#6b7280' }};">
                                    @if($pct > 15){{ $pct }}%@endif
                                </div>
                            </div>
                            <div class="rpt-bar-count">{{ $count }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Overall Progress Ring --}}
        <div class="rpt-card" style="text-align:center;">
            <div class="rpt-card-title" style="justify-content:center;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                </svg>
                Overall Completion
            </div>
            <div class="rpt-progress-ring">
                <svg viewBox="0 0 120 120" width="120" height="120">
                    <circle class="ring-bg" cx="60" cy="60" r="50" />
                    <circle class="ring-fill" cx="60" cy="60" r="50" stroke="{{ $progressColor }}"
                        stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" />
                </svg>
                <div class="rpt-progress-center">
                    <div class="rpt-progress-num">{{ $donePercent }}%</div>
                    <div class="rpt-progress-label">Complete</div>
                </div>
            </div>
            <div style="font-size:13px; color:#6b7280;" class="dark:text-gray-400">
                {{ $taskBreakdown['done'] }} of {{ $totalTasks }} tasks completed
            </div>
        </div>
    </div>

    <div class="rpt-grid-2">
        {{-- Module Summary --}}
        <div class="rpt-card">
            <div class="rpt-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                Module Health Summary
            </div>
            <table class="rpt-mod-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Module</th>
                        <th>Total</th>
                        <th>Active / Open</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($moduleSummary as $mod)
                        <tr>
                            <td class="rpt-mod-icon">{{ $mod['icon'] }}</td>
                            <td class="rpt-mod-name">{{ $mod['module'] }}</td>
                            <td class="rpt-mod-count">{{ $mod['total'] }}</td>
                            <td>
                                @if($mod['open'] !== null)
                                    <span class="rpt-mod-open" style="color: {{ $mod['open'] > 0 ? '#f59e0b' : '#10b981' }};">
                                        {{ $mod['open'] }}
                                    </span>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Milestone Timeline --}}
        <div class="rpt-card">
            <div class="rpt-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" />
                </svg>
                Milestones
            </div>
            @if(empty($milestones))
                <div style="text-align:center; padding:2rem; color:#6b7280; font-size:13px;">
                    No milestones defined yet.
                </div>
            @else
                <div>
                    @foreach($milestones as $ms)
                        @php
                            $dotColor = match ($ms['status']) {
                                'completed' => '#10b981',
                                'in_progress' => '#3b82f6',
                                'delayed' => '#ef4444',
                                default => '#9ca3af',
                            };
                            $badgeStyle = match ($ms['status']) {
                                'completed' => 'background:rgba(16,185,129,0.1);color:#10b981;',
                                'in_progress' => 'background:rgba(59,130,246,0.1);color:#3b82f6;',
                                'delayed' => 'background:rgba(239,68,68,0.1);color:#ef4444;',
                                default => 'background:rgba(107,114,128,0.1);color:#6b7280;',
                            };
                        @endphp
                        <div class="rpt-milestone">
                            <div class="rpt-ms-dot" style="background: {{ $dotColor }};"></div>
                            <div class="rpt-ms-title">{{ $ms['title'] }}</div>
                            <span class="rpt-ms-badge"
                                style="{{ $badgeStyle }}">{{ str_replace('_', ' ', $ms['status']) }}</span>
                            <div class="rpt-ms-date">
                                {{ $ms['target'] ?? '—' }}
                                @if($ms['is_late'])
                                    <span style="color:#ef4444; font-weight:600;"> ⚠ Late</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Activity Log / Audit Trail --}}
    <x-filament::section icon="heroicon-o-clock" icon-color="gray" collapsible>
        <x-slot name="heading">Activity Log</x-slot>
        <x-slot name="description">Recent actions across all project modules</x-slot>

        @php $logs = $this->getActivityLogs(); @endphp

        @if($logs->isEmpty())
            <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-medium">No activity recorded yet</p>
                <p class="text-xs mt-1">Activity will appear here as team members work on this project.</p>
            </div>
        @else
            <div class="relative">
                {{-- Timeline line --}}
                <div class="absolute left-5 top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700"></div>

                <div class="space-y-1">
                    @foreach($logs as $log)
                        @php
                            $actionColors = [
                                'created' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'updated' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                                'deleted' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                                'status_changed' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
                                'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'submitted' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
                                'commented' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                            ];
                            $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400';
                            $actionIcons = [
                                'created' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />',
                                'updated' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />',
                                'deleted' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />',
                                'status_changed' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />',
                                'approved' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                                'rejected' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                            ];
                            $iconPath = $actionIcons[$log->action] ?? '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        @endphp
                        <div class="relative pl-12 py-2.5 group">
                            {{-- Dot on timeline --}}
                            <div
                                class="absolute left-3 top-3.5 w-4 h-4 rounded-full {{ $colorClass }} flex items-center justify-center ring-2 ring-white dark:ring-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    {!! $iconPath !!}
                                </svg>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        <span class="font-medium">{{ $log->user?->name ?? 'System' }}</span>
                                        <span class="text-gray-500 dark:text-gray-400">{{ $log->description }}</span>
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ $log->created_at->diffForHumans() }}
                                        @if($log->ip_address)
                                            · {{ $log->ip_address }}
                                        @endif
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-semibold uppercase tracking-wider {{ $colorClass }} shrink-0">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-filament::section>

</x-filament-panels::page>