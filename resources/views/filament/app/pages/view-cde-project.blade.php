<x-filament-panels::page>
@php
    $stats = $this->getStats();
    $recentTasks = $this->getRecentTasks();
    $recentDocs = $this->getRecentDocuments();
    $modules = $this->getEnabledModulesList();
    $record = $this->record;

    $statusColors = [
        'active' => '#10b981', 'planning' => '#3b82f6', 'on_hold' => '#f59e0b',
        'completed' => '#6b7280', 'cancelled' => '#ef4444',
    ];
    $statusLabels = [
        'active' => 'Active', 'planning' => 'Planning', 'on_hold' => 'On Hold',
        'completed' => 'Completed', 'cancelled' => 'Cancelled',
    ];
    $statusColor = $statusColors[$record->status] ?? '#6b7280';
    $statusLabel = $statusLabels[$record->status] ?? ucfirst($record->status);
@endphp

@push('styles')
<style>
    .ov-grid { display: grid; gap: 1rem; }
    .ov-grid-4 { grid-template-columns: repeat(4, 1fr); }
    .ov-grid-3 { grid-template-columns: repeat(3, 1fr); }
    .ov-grid-2-1 { grid-template-columns: 2fr 1fr; }

    .ov-card {
        background: white; border-radius: 0.75rem; padding: 1.25rem;
        border: 1px solid #e5e7eb;
        transition: box-shadow 200ms;
    }
    .ov-card:hover { box-shadow: 0 4px 6px -1px rgba(0,0,0,.07); }

    .dark .ov-card {
        background: rgba(30, 41, 59, 0.65);
        border-color: rgba(255, 255, 255, 0.06);
    }
    .dark .ov-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.25); }

    .ov-stat-primary {
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
        color: white; border: none;
    }
    .ov-stat-primary .ov-stat-label,
    .ov-stat-primary .ov-stat-sub { color: rgba(255,255,255,.75); }
    .ov-stat-primary .ov-stat-value { color: white; }
    .ov-stat-primary .ov-stat-icon { background: rgba(255,255,255,.15); color: white; }

    .dark .ov-stat-primary {
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 50%, #134e4a 100%);
        border-color: transparent;
    }

    .ov-stat-icon {
        display: flex; align-items: center; justify-content: center;
        width: 2.25rem; height: 2.25rem; border-radius: 0.5rem;
        background: #f0f9ff; flex-shrink: 0;
    }
    .ov-stat-icon svg { width: 1.125rem; height: 1.125rem; }

    .dark .ov-stat-icon { background: rgba(255, 255, 255, 0.08); }

    .ov-stat-label { font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; }
    .dark .ov-stat-label { color: #94a3b8; }

    .ov-stat-value { font-size: 1.75rem; font-weight: 700; color: #111827; line-height: 1.2; margin-top: 0.125rem; }
    .dark .ov-stat-value { color: #f1f5f9; }

    .ov-stat-sub { font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem; }
    .dark .ov-stat-sub { color: #94a3b8; }
    .ov-stat-sub.danger { color: #ef4444; }
    .ov-stat-sub.success { color: #10b981; }

    .ov-section-title { font-size: 0.9375rem; font-weight: 600; color: #111827; }
    .dark .ov-section-title { color: #f1f5f9; }

    .ov-section-subtitle { font-size: 0.75rem; color: #9ca3af; margin-top: 0.125rem; }
    .dark .ov-section-subtitle { color: #64748b; }

    .ov-table { width: 100%; border-collapse: collapse; margin-top: 0.75rem; }
    .ov-table th { font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; text-align: left; padding: 0.5rem 0.75rem; border-bottom: 1px solid #f3f4f6; }
    .dark .ov-table th { color: #64748b; border-bottom-color: rgba(255, 255, 255, 0.06); }

    .ov-table td { font-size: 0.8125rem; color: #374151; padding: 0.625rem 0.75rem; border-bottom: 1px solid #f9fafb; }
    .dark .ov-table td { color: #cbd5e1; border-bottom-color: rgba(255, 255, 255, 0.04); }

    .ov-table tr:last-child td { border-bottom: none; }
    .ov-table tr:hover td { background: #f9fafb; }
    .dark .ov-table tr:hover td { background: rgba(255, 255, 255, 0.03); }

    .ov-badge {
        display: inline-block; padding: 0.125rem 0.5rem; border-radius: 9999px;
        font-size: 0.6875rem; font-weight: 500;
    }
    .ov-badge-success { background: #ecfdf5; color: #059669; }
    .dark .ov-badge-success { background: rgba(16, 185, 129, 0.15); color: #34d399; }

    .ov-badge-warning { background: #fffbeb; color: #d97706; }
    .dark .ov-badge-warning { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }

    .ov-badge-danger { background: #fef2f2; color: #dc2626; }
    .dark .ov-badge-danger { background: rgba(239, 68, 68, 0.15); color: #f87171; }

    .ov-badge-info { background: #eff6ff; color: #2563eb; }
    .dark .ov-badge-info { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }

    .ov-badge-gray { background: #f3f4f6; color: #4b5563; }
    .dark .ov-badge-gray { background: rgba(255, 255, 255, 0.08); color: #94a3b8; }

    .ov-link { color: #2563eb; text-decoration: none; font-weight: 500; }
    .ov-link:hover { text-decoration: underline; }
    .dark .ov-link { color: #60a5fa; }

    .ov-activity-dot { width: 0.5rem; height: 0.5rem; border-radius: 50%; flex-shrink: 0; margin-top: 0.375rem; }

    .ov-module-item {
        display: flex; align-items: center; gap: 0.625rem; padding: 0.5rem 0.625rem;
        border-radius: 0.5rem; transition: background 150ms; cursor: default;
    }
    .ov-module-item:hover { background: #f9fafb; }
    .dark .ov-module-item:hover { background: rgba(255, 255, 255, 0.04); }

    .ov-module-icon {
        display: flex; align-items: center; justify-content: center;
        width: 1.75rem; height: 1.75rem; border-radius: 0.375rem;
        background: #eff6ff; flex-shrink: 0;
    }
    .ov-module-icon svg { width: 0.875rem; height: 0.875rem; color: #3b82f6; }
    .dark .ov-module-icon { background: rgba(59, 130, 246, 0.15); }
    .dark .ov-module-icon svg { color: #60a5fa; }

    /* Dark mode value text used in inline styles */
    .ov-detail-label { font-size: 0.8125rem; color: #6b7280; }
    .dark .ov-detail-label { color: #94a3b8; }

    .ov-detail-value { font-size: 0.8125rem; font-weight: 500; color: #111827; }
    .dark .ov-detail-value { color: #e2e8f0; }

    .ov-detail-value-bold { font-size: 0.8125rem; font-weight: 600; color: #111827; }
    .dark .ov-detail-value-bold { color: #f1f5f9; }

    .ov-detail-row {
        display: flex; justify-content: space-between; align-items: center;
        padding-bottom: 0.5rem; border-bottom: 1px solid #f3f4f6;
    }
    .ov-detail-row:last-child { border-bottom: none; padding-bottom: 0; }
    .dark .ov-detail-row { border-bottom-color: rgba(255, 255, 255, 0.06); }

    .ov-doc-title { font-size: 0.8125rem; font-weight: 500; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dark .ov-doc-title { color: #e2e8f0; }

    .ov-doc-time { font-size: 0.6875rem; color: #9ca3af; }
    .dark .ov-doc-time { color: #64748b; }

    .ov-module-name { font-size: 0.8125rem; font-weight: 500; color: #374151; }
    .dark .ov-module-name { color: #e2e8f0; }

    .ov-progress-name { font-size: 1rem; font-weight: 600; color: #111827; }
    .dark .ov-progress-name { color: #f1f5f9; }

    .ov-progress-pct { font-size: 0.8125rem; color: #6b7280; margin-top: 0.25rem; }
    .dark .ov-progress-pct { color: #94a3b8; }

    .ov-progress-track { margin-top: 0.75rem; height: 0.5rem; background: #f3f4f6; border-radius: 9999px; overflow: hidden; }
    .dark .ov-progress-track { background: rgba(255, 255, 255, 0.1); }

    .ov-progress-bar { height: 100%; background: linear-gradient(90deg, #0d9488, #14b8a6); border-radius: 9999px; transition: width 500ms; }

    .ov-progress-meta { display: flex; justify-content: space-between; margin-top: 0.5rem; }
    .ov-progress-meta span { font-size: 0.75rem; color: #9ca3af; }
    .dark .ov-progress-meta span { color: #64748b; }

    .ov-quick-stat { text-align: center; padding: 0.5rem; background: #f9fafb; border-radius: 0.5rem; }
    .dark .ov-quick-stat { background: rgba(255, 255, 255, 0.05); }

    .ov-quick-stat-value { font-size: 1.125rem; font-weight: 700; color: #111827; }
    .dark .ov-quick-stat-value { color: #f1f5f9; }

    .ov-quick-stat-label { font-size: 0.6875rem; color: #9ca3af; }
    .dark .ov-quick-stat-label { color: #64748b; }

    .ov-empty { text-align: center; padding: 2rem 0; color: #9ca3af; }
    .ov-empty p { font-size: 0.875rem; }
    .dark .ov-empty { color: #64748b; }

    .ov-icon-muted { color: #9ca3af; }
    .dark .ov-icon-muted { color: #64748b; }

    .ov-stat-icon-amber { background: #fef3c7; }
    .dark .ov-stat-icon-amber { background: rgba(245, 158, 11, 0.15); }

    .ov-stat-icon-green { background: #ecfdf5; }
    .dark .ov-stat-icon-green { background: rgba(16, 185, 129, 0.15); }

    .ov-stat-icon-red { background: #fef2f2; }
    .dark .ov-stat-icon-red { background: rgba(239, 68, 68, 0.15); }

    @media (max-width: 768px) {
        .ov-grid-4 { grid-template-columns: repeat(2, 1fr); }
        .ov-grid-3, .ov-grid-2-1 { grid-template-columns: 1fr; }
    }
</style>
@endpush

{{-- ═══ STAT CARDS ═══ --}}
<div class="ov-grid ov-grid-4">
    {{-- Active Status Card (primary) --}}
    <div class="ov-card ov-stat-primary">
        <div style="display: flex; align-items: flex-start; justify-content: space-between;">
            <div>
                <div class="ov-stat-label">Project Status</div>
                <div class="ov-stat-value">{{ $statusLabel }}</div>
                <div class="ov-stat-sub">{{ $record->client?->name ?? 'No client' }}</div>
            </div>
            <div class="ov-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
            </div>
        </div>
    </div>

    {{-- Open Tasks --}}
    <div class="ov-card">
        <div style="display: flex; align-items: flex-start; justify-content: space-between;">
            <div>
                <div class="ov-stat-label">Open Tasks</div>
                <div class="ov-stat-value">{{ $stats['tasks_open'] }}</div>
                @if($stats['tasks_overdue'] > 0)
                    <div class="ov-stat-sub danger">↓ {{ $stats['tasks_overdue'] }} overdue</div>
                @else
                    <div class="ov-stat-sub success">All on track</div>
                @endif
            </div>
            <div class="ov-stat-icon ov-stat-icon-amber">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
            </div>
        </div>
    </div>

    {{-- Documents --}}
    <div class="ov-card">
        <div style="display: flex; align-items: flex-start; justify-content: space-between;">
            <div>
                <div class="ov-stat-label">Documents</div>
                <div class="ov-stat-value">{{ $stats['documents'] }}</div>
                @if($stats['docs_this_week'] > 0)
                    <div class="ov-stat-sub success">↑ +{{ $stats['docs_this_week'] }} this week</div>
                @else
                    <div class="ov-stat-sub">{{ $stats['folders'] }} folders</div>
                @endif
            </div>
            <div class="ov-stat-icon ov-stat-icon-green">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
            </div>
        </div>
    </div>

    {{-- Open Issues --}}
    <div class="ov-card">
        <div style="display: flex; align-items: flex-start; justify-content: space-between;">
            <div>
                <div class="ov-stat-label">Open Issues</div>
                <div class="ov-stat-value">{{ $stats['incidents'] }}</div>
                @if($stats['incidents_open'] > 0)
                    <div class="ov-stat-sub danger">↑ {{ $stats['incidents_open'] }} open</div>
                @else
                    <div class="ov-stat-sub success">All resolved</div>
                @endif
            </div>
            <div class="ov-stat-icon ov-stat-icon-red">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#dc2626"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MIDDLE ROW: Tasks + Project Details ═══ --}}
<div class="ov-grid ov-grid-2-1" style="margin-top: 0.25rem;">
    {{-- Pending Tasks --}}
    <div class="ov-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="ov-section-title">Pending Tasks</div>
                <div class="ov-section-subtitle">Due soonest first</div>
            </div>
        </div>

        @if($recentTasks->isEmpty())
            <div class="ov-empty">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p>No pending tasks</p>
            </div>
        @else
            <table class="ov-table">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTasks as $task)
                        <tr>
                            <td><span class="ov-link">{{ Str::limit($task->title, 35) }}</span></td>
                            <td>{{ $task->assignee?->name ?? '—' }}</td>
                            <td>
                                @if($task->due_date)
                                    @if($task->due_date->isPast())
                                        <span style="color: #dc2626; font-weight: 500;">{{ $task->due_date->format('M d') }}</span>
                                    @elseif($task->due_date->isToday())
                                        <span style="color: #d97706; font-weight: 500;">Today</span>
                                    @elseif($task->due_date->isTomorrow())
                                        <span class="ov-detail-value" style="font-weight: 500;">Tomorrow</span>
                                    @else
                                        {{ $task->due_date->format('M d') }}
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @php
                                    $taskBadge = match($task->status) {
                                        'in_progress' => 'ov-badge-info',
                                        'review' => 'ov-badge-warning',
                                        'blocked' => 'ov-badge-danger',
                                        default => 'ov-badge-gray',
                                    };
                                @endphp
                                <span class="ov-badge {{ $taskBadge }}">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Project Details --}}
    <div class="ov-card">
        <div class="ov-section-title">Project Details</div>
        <div class="ov-section-subtitle">Key information</div>

        <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.75rem;">
            <div class="ov-detail-row">
                <span class="ov-detail-label">Client</span>
                <span class="ov-detail-value">{{ $record->client?->name ?? '—' }}</span>
            </div>
            <div class="ov-detail-row">
                <span class="ov-detail-label">Manager</span>
                <span class="ov-detail-value">{{ $record->manager?->name ?? '—' }}</span>
            </div>
            <div class="ov-detail-row">
                <span class="ov-detail-label">Budget</span>
                <span class="ov-detail-value-bold">{{ \App\Support\CurrencyHelper::format($record->budget ?? 0) }}</span>
            </div>
            <div class="ov-detail-row">
                <span class="ov-detail-label">Start</span>
                <span class="ov-detail-value">{{ $record->start_date ? $record->start_date->format('M d, Y') : '—' }}</span>
            </div>
            <div class="ov-detail-row">
                <span class="ov-detail-label">End</span>
                <span class="ov-detail-value">{{ $record->end_date ? $record->end_date->format('M d, Y') : '—' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══ BOTTOM ROW: Recent Docs + Modules + Status ═══ --}}
<div class="ov-grid ov-grid-3" style="margin-top: 0.25rem;">
    {{-- Recent Documents --}}
    <div class="ov-card">
        <div class="ov-section-title">Recent Documents</div>
        <div class="ov-section-subtitle">Latest uploads</div>

        @if($recentDocs->isEmpty())
            <div class="ov-empty">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                </svg>
                <p>No documents yet</p>
            </div>
        @else
            <div style="margin-top: 0.75rem; display: flex; flex-direction: column; gap: 0.375rem;">
                @foreach($recentDocs as $doc)
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0;">
                        <div class="ov-activity-dot" style="background: #3b82f6;"></div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="ov-doc-title">{{ $doc->title ?? $doc->name ?? 'Document' }}</div>
                            <div class="ov-doc-time">{{ $doc->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Enabled Modules --}}
    <div class="ov-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="ov-section-title">Enabled Modules</div>
                <div class="ov-section-subtitle">Quick access</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ov-icon-muted" style="width: 1.25rem; height: 1.25rem;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.401.604-.401.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.959.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z" />
            </svg>
        </div>

        @if(empty($modules))
            <div class="ov-empty">
                No modules enabled yet.
            </div>
        @else
            <div style="margin-top: 0.625rem; display: flex; flex-direction: column; gap: 0.125rem;">
                @foreach($modules as $mod)
                    <div class="ov-module-item">
                        <div class="ov-module-icon">
                            <x-filament::icon :icon="$mod['icon']" />
                        </div>
                        <span class="ov-module-name">{{ $mod['name'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Project Progress --}}
    <div class="ov-card">
        <div class="ov-section-title">Project Status</div>
        <div class="ov-section-subtitle">Current health</div>

        <div style="margin-top: 1.25rem;">
            <div class="ov-progress-name">{{ $record->name }}</div>

            {{-- Progress calc --}}
            @php
                $totalTasks = $stats['tasks_total'];
                $doneTasks = $totalTasks - $stats['tasks_open'];
                $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
            @endphp
            <div class="ov-progress-pct">{{ $progress }}% complete</div>

            {{-- Progress bar --}}
            <div class="ov-progress-track">
                <div class="ov-progress-bar" style="width: {{ $progress }}%;"></div>
            </div>

            <div class="ov-progress-meta">
                <span>{{ $doneTasks }} / {{ $totalTasks }} tasks done</span>
                <span class="ov-badge {{ $record->status === 'active' ? 'ov-badge-success' : ($record->status === 'planning' ? 'ov-badge-info' : 'ov-badge-gray') }}">
                    {{ $statusLabel }}
                </span>
            </div>

            {{-- Quick stats --}}
            <div style="margin-top: 1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <div class="ov-quick-stat">
                    <div class="ov-quick-stat-value">{{ $stats['rfis'] }}</div>
                    <div class="ov-quick-stat-label">RFIs</div>
                </div>
                <div class="ov-quick-stat">
                    <div class="ov-quick-stat-value">{{ $stats['folders'] }}</div>
                    <div class="ov-quick-stat-label">Folders</div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-filament-panels::page>
