<x-filament-panels::page>

    @push('styles')
        <style>
            .rpt-toolbar {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                padding: 10px 16px;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                margin-bottom: 16px;
            }

            .dark .rpt-toolbar {
                background: rgba(255, 255, 255, 0.04);
                border-color: rgba(255, 255, 255, 0.08);
            }

            .rpt-toolbar label {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: #6b7280;
            }

            .rpt-toolbar input,
            .rpt-toolbar select {
                font-size: 12px;
                padding: 5px 10px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                background: white;
                color: #111827;
            }

            .dark .rpt-toolbar input,
            .dark .rpt-toolbar select {
                background: rgba(255, 255, 255, 0.06);
                border-color: rgba(255, 255, 255, 0.12);
                color: #f3f4f6;
            }

            .rpt-export-btn {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 5px 12px;
                font-size: 11px;
                font-weight: 700;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                background: #f9fafb;
                color: #374151;
                cursor: pointer;
                transition: all .2s;
            }

            .rpt-export-btn:hover {
                background: #6366f1;
                color: white;
                border-color: #6366f1;
            }

            .dark .rpt-export-btn {
                background: rgba(255, 255, 255, 0.06);
                border-color: rgba(255, 255, 255, 0.12);
                color: #e5e7eb;
            }

            .rpt-tab {
                padding: 6px 14px;
                font-size: 11px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                border-radius: 6px;
                transition: all .2s;
                background: transparent;
                color: #6b7280;
                white-space: nowrap;
            }

            .rpt-tab:hover {
                background: #f3f4f6;
                color: #374151;
            }

            .rpt-tab.active {
                background: #6366f1;
                color: white;
            }

            .dark .rpt-tab:hover {
                background: rgba(255, 255, 255, 0.06);
                color: #e5e7eb;
            }

            .rpt-card {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                padding: 1.25rem;
                margin-bottom: 1rem;
            }

            .dark .rpt-card {
                background: rgba(255, 255, 255, 0.04);
                border-color: rgba(255, 255, 255, 0.08);
            }

            .rpt-card-title {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #6b7280;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .dark .rpt-card-title {
                color: #9ca3af;
            }

            .rpt-card-title svg {
                width: 14px;
                height: 14px;
            }

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

            .rpt-grid-4 {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
                margin-bottom: 1rem;
            }

            @media (max-width:768px) {

                .rpt-grid-2,
                .rpt-grid-3,
                .rpt-grid-4 {
                    grid-template-columns: 1fr;
                }
            }

            .rpt-bar-track {
                height: 22px;
                background: #f3f4f6;
                border-radius: 4px;
                overflow: hidden;
            }

            .dark .rpt-bar-track {
                background: rgba(255, 255, 255, 0.05);
            }

            .rpt-bar-fill {
                height: 100%;
                border-radius: 4px;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                padding-right: 6px;
                font-size: 10px;
                font-weight: 700;
                color: white;
                transition: width .5s;
                min-width: 0;
            }

            .rpt-mini-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            .rpt-mini-table th {
                text-align: left;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #9ca3af;
                padding: 6px 10px;
                border-bottom: 1px solid #e5e7eb;
                font-weight: 700;
            }

            .dark .rpt-mini-table th {
                border-bottom-color: rgba(255, 255, 255, 0.06);
            }

            .rpt-mini-table td {
                padding: 6px 10px;
                border-bottom: 1px solid #f3f4f6;
                color: #374151;
            }

            .dark .rpt-mini-table td {
                border-bottom-color: rgba(255, 255, 255, 0.04);
                color: #e5e7eb;
            }

            .rpt-kpi {
                text-align: center;
                padding: 14px 12px;
                border-radius: 8px;
                border: 1px solid #e5e7eb;
            }

            .dark .rpt-kpi {
                border-color: rgba(255, 255, 255, 0.08);
            }

            .rpt-kpi-value {
                font-size: 1.25rem;
                font-weight: 800;
                color: #111827;
            }

            .dark .rpt-kpi-value {
                color: #f3f4f6;
            }

            .rpt-kpi-label {
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: #9ca3af;
                margin-top: 2px;
            }

            .rpt-badge {
                display: inline-block;
                font-size: 10px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 99px;
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }

            .rpt-rag-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                display: inline-block;
            }
        </style>
    @endpush

    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    {{-- ── Report Selector & Date Range Toolbar ── --}}
    <div class="rpt-toolbar">
        <div style="display:flex;align-items:center;gap:6px;flex:1;overflow-x:auto;">
            @foreach(['summary' => 'Summary', 'financial' => 'Financial', 'tasks' => 'Tasks', 'documents' => 'Documents', 'contracts' => 'Contracts', 'safety' => 'Safety', 'rfis' => 'RFIs'] as $key => $label)
                <button wire:click="$set('activeReport', '{{ $key }}')"
                    class="rpt-tab {{ $this->activeReport === $key ? 'active' : '' }}">{{ $label }}</button>
            @endforeach
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <label>From</label>
            <input type="date" wire:model.live="dateFrom" value="{{ $this->dateFrom }}">
            <label>To</label>
            <input type="date" wire:model.live="dateTo" value="{{ $this->dateTo }}">
        </div>
        <button class="rpt-export-btn" wire:click="exportReport('{{ $this->activeReport }}')" title="Export CSV">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Export CSV
        </button>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- SUMMARY REPORT --}}
    {{-- ════════════════════════════════════════════ --}}
    @if($this->activeReport === 'summary')
        @php $rpt = $this->getProjectSummaryReport(); @endphp

        {{-- RAG Health --}}
        <div class="rpt-grid-4">
            @foreach($rpt['health'] as $h)
                @php $ragC = ['green' => ['#dcfce7', '#16a34a'], 'amber' => ['#fef3c7', '#d97706'], 'red' => ['#fef2f2', '#dc2626']][$h['status']] ?? ['#f1f5f9', '#64748b']; @endphp
                <div style="padding:10px 14px;border-radius:8px;background:{{ $ragC[0] }};border:1px solid {{ $ragC[1] }}22;">
                    <div style="display:flex;align-items:center;gap:5px;margin-bottom:3px;">
                        <span class="rpt-rag-dot" style="background:{{ $ragC[1] }};"></span>
                        <span
                            style="font-size:11px;font-weight:700;color:{{ $ragC[1] }};text-transform:uppercase;">{{ $h['dimension'] }}</span>
                    </div>
                    <div style="font-size:11px;color:#64748b;">{{ $h['detail'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- Task Breakdown + Progress Ring --}}
        <div class="rpt-grid-2">
            <div class="rpt-card">
                <div class="rpt-card-title"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                    </svg> Task Status</div>
                @if($rpt['total_tasks'] === 0)
                    <div style="text-align:center;padding:2rem;color:#9ca3af;font-size:12px;">No tasks yet</div>
                @else
                    @php $barColors = ['todo' => '#94a3b8', 'in_progress' => '#3b82f6', 'review' => '#f59e0b', 'done' => '#10b981', 'blocked' => '#ef4444'];
                    $barLabels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done', 'blocked' => 'Blocked']; @endphp
                    @foreach($rpt['task_breakdown'] as $s => $c)
                        @php $pct = $rpt['total_tasks'] > 0 ? round(($c / $rpt['total_tasks']) * 100) : 0; @endphp
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                            <span
                                style="width:80px;font-size:11px;font-weight:600;color:#6b7280;text-align:right;">{{ $barLabels[$s] ?? ucfirst($s) }}</span>
                            <div class="rpt-bar-track" style="flex:1;">
                                <div class="rpt-bar-fill"
                                    style="width:{{ max($pct, ($c > 0 ? 8 : 0)) }}%;background:{{ $barColors[$s] ?? '#6b7280' }};">
                                    @if($pct > 15){{ $pct }}%@endif</div>
                            </div>
                            <span style="width:30px;font-size:12px;font-weight:700;color:#374151;text-align:center;">{{ $c }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="rpt-card" style="text-align:center;">
                <div class="rpt-card-title" style="justify-content:center;"><svg fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                    </svg> Overall Completion</div>
                @php $donePct = $rpt['total_tasks'] > 0 ? round(($rpt['task_breakdown']['done'] / $rpt['total_tasks']) * 100) : 0;
                    $circ = 314.159;
                    $off = $circ - ($donePct / 100) * $circ;
                $pColor = $donePct >= 75 ? '#10b981' : ($donePct >= 40 ? '#f59e0b' : '#3b82f6'); @endphp
                <div style="position:relative;width:120px;height:120px;margin:0 auto 1rem;">
                    <svg viewBox="0 0 120 120" width="120" height="120" style="transform:rotate(-90deg);">
                        <circle fill="none" stroke="#e5e7eb" stroke-width="10" cx="60" cy="60" r="50" />
                        <circle fill="none" stroke="{{ $pColor }}" stroke-width="10" stroke-linecap="round" cx="60" cy="60"
                            r="50" stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $off }}" />
                    </svg>
                    <div
                        style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                        <span style="font-size:1.75rem;font-weight:800;color:#111827;">{{ $donePct }}%</span>
                        <span style="font-size:10px;color:#6b7280;text-transform:uppercase;">Complete</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Module Summary + Milestones --}}
        <div class="rpt-grid-2">
            <div class="rpt-card">
                <div class="rpt-card-title"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z" />
                    </svg> Module Health</div>
                <table class="rpt-mini-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Module</th>
                            <th>Total</th>
                            <th>Open</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rpt['module_summary'] as $m)
                            <tr>
                                <td>{{ $m['icon'] }}</td>
                                <td style="font-weight:600;">{{ $m['module'] }}</td>
                                <td style="font-weight:700;">{{ $m['total'] }}</td>
                                <td style="font-weight:600;color:{{ ($m['open'] ?? 0) > 0 ? '#f59e0b' : '#10b981' }};">
                                    {{ $m['open'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="rpt-card">
                <div class="rpt-card-title"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" />
                    </svg> Milestones</div>
                @forelse($rpt['milestones'] as $ms)
                    @php $dotC = match ($ms['status']) { 'completed' => '#10b981', 'in_progress' => '#3b82f6', 'delayed' => '#ef4444', default => '#9ca3af'}; @endphp
                    <div style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #f3f4f6;">
                        <span class="rpt-rag-dot" style="background:{{ $dotC }};"></span>
                        <span style="flex:1;font-size:12px;font-weight:600;">{{ $ms['title'] }}</span>
                        <span class="rpt-badge"
                            style="background:{{ $dotC }}15;color:{{ $dotC }};">{{ str_replace('_', ' ', $ms['status']) }}</span>
                        <span style="font-size:11px;color:#6b7280;">{{ $ms['target'] ?? '—' }}@if($ms['is_late']) <span
                        style="color:#ef4444;">⚠ Late</span>@endif</span>
                    </div>
                @empty
                    <div style="text-align:center;padding:1.5rem;color:#9ca3af;font-size:12px;">No milestones</div>
                @endforelse
            </div>
        </div>

        {{-- Monthly Trend --}}
        @php $maxT = max(1, max(array_column($rpt['trend'], 'tasks_completed')), max(array_column($rpt['trend'], 'logs'))); @endphp
        <div class="rpt-grid-2">
            <div class="rpt-card">
                <div class="rpt-card-title">Tasks Completed · 6 Months</div>
                <div style="display:flex;align-items:flex-end;gap:4px;height:60px;">
                    @foreach($rpt['trend'] as $t)
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                            <div style="width:100%;background:#6366f1;border-radius:3px 3px 0 0;height:{{ max(2, ($t['tasks_completed'] / $maxT) * 50) }}px;"
                                title="{{ $t['tasks_completed'] }}"></div>
                            <div style="font-size:8px;color:#94a3b8;margin-top:2px;">{{ $t['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="rpt-card">
                <div class="rpt-card-title">Daily Logs · 6 Months</div>
                <div style="display:flex;align-items:flex-end;gap:4px;height:60px;">
                    @foreach($rpt['trend'] as $t)
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                            <div style="width:100%;background:#0891b2;border-radius:3px 3px 0 0;height:{{ max(2, ($t['logs'] / $maxT) * 50) }}px;"
                                title="{{ $t['logs'] }}"></div>
                            <div style="font-size:8px;color:#94a3b8;margin-top:2px;">{{ $t['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════ --}}
    {{-- FINANCIAL REPORT --}}
    {{-- ════════════════════════════════════════════ --}}
    @if($this->activeReport === 'financial')
        @php $fin = $this->getFinancialReport(); @endphp
        <div class="rpt-grid-4">
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:#6366f1;">
                    {{ \App\Support\CurrencyHelper::formatCompact($fin['invoiced']) }}</div>
                <div class="rpt-kpi-label">Invoiced</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:#059669;">
                    {{ \App\Support\CurrencyHelper::formatCompact($fin['received']) }}</div>
                <div class="rpt-kpi-label">Received</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:#ef4444;">
                    {{ \App\Support\CurrencyHelper::formatCompact($fin['expenses']) }}</div>
                <div class="rpt-kpi-label">Expenses</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:#d97706;">
                    {{ \App\Support\CurrencyHelper::formatCompact($fin['outstanding']) }}</div>
                <div class="rpt-kpi-label">Outstanding ({{ $fin['overdue'] }} overdue)</div>
            </div>
        </div>

        {{-- Cash Flow Chart --}}
        <div class="rpt-card">
            <div class="rpt-card-title">Monthly Cash Flow</div>
            @php $maxCF = max(1, max(array_column($fin['cashFlow'], 'invoiced')), max(array_column($fin['cashFlow'], 'received')), max(array_column($fin['cashFlow'], 'expenses'))); @endphp
            <div style="display:flex;gap:6px;align-items:flex-end;height:100px;">
                @foreach($fin['cashFlow'] as $cf)
                    <div style="flex:1;display:flex;gap:2px;align-items:flex-end;">
                        <div style="flex:1;background:#6366f1;border-radius:2px 2px 0 0;height:{{ max(2, ($cf['invoiced'] / $maxCF) * 80) }}px;"
                            title="Invoiced: {{ \App\Support\CurrencyHelper::format($cf['invoiced'], 0) }}"></div>
                        <div style="flex:1;background:#10b981;border-radius:2px 2px 0 0;height:{{ max(2, ($cf['received'] / $maxCF) * 80) }}px;"
                            title="Received: {{ \App\Support\CurrencyHelper::format($cf['received'], 0) }}"></div>
                        <div style="flex:1;background:#ef4444;border-radius:2px 2px 0 0;height:{{ max(2, ($cf['expenses'] / $maxCF) * 80) }}px;"
                            title="Expenses: {{ \App\Support\CurrencyHelper::format($cf['expenses'], 0) }}"></div>
                    </div>
                @endforeach
            </div>
            <div style="display:flex;gap:6px;margin-top:4px;">@foreach($fin['cashFlow'] as $cf)<div
            style="flex:1;text-align:center;font-size:9px;color:#94a3b8;">{{ $cf['label'] }}</div>@endforeach</div>
            <div style="display:flex;gap:16px;margin-top:8px;font-size:10px;">
                <span style="display:flex;align-items:center;gap:4px;"><span
                        style="width:8px;height:8px;border-radius:2px;background:#6366f1;"></span> Invoiced</span>
                <span style="display:flex;align-items:center;gap:4px;"><span
                        style="width:8px;height:8px;border-radius:2px;background:#10b981;"></span> Received</span>
                <span style="display:flex;align-items:center;gap:4px;"><span
                        style="width:8px;height:8px;border-radius:2px;background:#ef4444;"></span> Expenses</span>
            </div>
        </div>

        {{-- Invoice Status + Expense Breakdown --}}
        <div class="rpt-grid-2">
            <div class="rpt-card">
                <div class="rpt-card-title">Invoices by Status</div>
                <table class="rpt-mini-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fin['byStatus'] as $s)
                            <tr>
                                <td><span class="rpt-badge"
                                        style="background:#6366f115;color:#6366f1;">{{ $s['status'] }}</span></td>
                                <td style="font-weight:700;">{{ $s['count'] }}</td>
                                <td style="font-weight:600;">{{ \App\Support\CurrencyHelper::format($s['total'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="rpt-card">
                <div class="rpt-card-title">Expenses by Category</div>
                @php $maxExp = max(1, max(array_column($fin['expenseBreakdown'] ?: [['total' => 0]], 'total'))); @endphp
                @forelse($fin['expenseBreakdown'] as $eb)
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                        <span
                            style="width:100px;font-size:11px;font-weight:600;color:#6b7280;text-align:right;">{{ $eb['category'] }}</span>
                        <div class="rpt-bar-track" style="flex:1;">
                            <div class="rpt-bar-fill" style="width:{{ ($eb['total'] / $maxExp) * 100 }}%;background:#ef4444;">
                                {{ $eb['total_fmt'] }}</div>
                        </div>
                        <span style="font-size:10px;color:#94a3b8;">{{ $eb['count'] }}</span>
                    </div>
                @empty
                    <div style="text-align:center;padding:1rem;color:#9ca3af;font-size:12px;">No expenses</div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════ --}}
    {{-- TASK REPORT --}}
    {{-- ════════════════════════════════════════════ --}}
    @if($this->activeReport === 'tasks')
        @php $tr = $this->getTaskReport(); @endphp
        <div class="rpt-grid-4">
            <div class="rpt-kpi">
                <div class="rpt-kpi-value">{{ $tr['createdInRange'] }}</div>
                <div class="rpt-kpi-label">Created in Period</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:#10b981;">{{ $tr['completedInRange'] }}</div>
                <div class="rpt-kpi-label">Completed in Period</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:#ef4444;">{{ count($tr['overdue']) }}</div>
                <div class="rpt-kpi-label">Overdue</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:#3b82f6;">{{ $tr['byStatus']['in_progress'] ?? 0 }}</div>
                <div class="rpt-kpi-label">In Progress</div>
            </div>
        </div>
        <div class="rpt-grid-2">
            <div class="rpt-card">
                <div class="rpt-card-title">Tasks by Status</div>
                @php $statusColors = ['todo' => '#94a3b8', 'in_progress' => '#3b82f6', 'review' => '#f59e0b', 'done' => '#10b981', 'blocked' => '#ef4444'];
                $totalT = max(1, array_sum($tr['byStatus'])); @endphp
                @foreach($tr['byStatus'] as $s => $c)
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;">
                        <span
                            style="width:80px;font-size:11px;font-weight:600;color:#6b7280;text-align:right;">{{ ucfirst(str_replace('_', ' ', $s)) }}</span>
                        <div class="rpt-bar-track" style="flex:1;">
                            <div class="rpt-bar-fill"
                                style="width:{{ max(($c > 0 ? 8 : 0), round(($c / $totalT) * 100)) }}%;background:{{ $statusColors[$s] ?? '#6b7280' }};">
                            </div>
                        </div>
                        <span style="width:30px;font-size:12px;font-weight:700;text-align:center;">{{ $c }}</span>
                    </div>
                @endforeach
            </div>
            <div class="rpt-card">
                <div class="rpt-card-title">Tasks by Assignee (Top 15)</div>
                <table class="rpt-mini-table">
                    <thead>
                        <tr>
                            <th>Assignee</th>
                            <th>Total</th>
                            <th>Done</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tr['byAssignee'] as $name => $a)
                            @php $aPct = $a['total'] > 0 ? round(($a['done'] / $a['total']) * 100) : 0; @endphp
                            <tr>
                                <td style="font-weight:600;">{{ $name }}</td>
                                <td style="font-weight:700;">{{ $a['total'] }}</td>
                                <td style="color:#10b981;font-weight:600;">{{ $a['done'] }}</td>
                                <td>{{ $aPct }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(!empty($tr['overdue']))
            <div class="rpt-card">
                <div class="rpt-card-title" style="color:#ef4444;">⚠ Overdue Tasks</div>
                <table class="rpt-mini-table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assignee</th>
                            <th>Due</th>
                            <th>Days Over</th>
                            <th>Priority</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tr['overdue'] as $ot)
                            <tr>
                                <td style="font-weight:600;">{{ Str::limit($ot['title'], 40) }}</td>
                                <td>{{ $ot['assignee'] }}</td>
                                <td>{{ $ot['due'] }}</td>
                                <td style="color:#ef4444;font-weight:700;">{{ $ot['days_overdue'] }}d</td>
                                <td><span class="rpt-badge"
                                        style="background:{{ $ot['priority'] === 'high' || $ot['priority'] === 'urgent' ? '#fef2f2' : '#f1f5f9' }};color:{{ $ot['priority'] === 'high' || $ot['priority'] === 'urgent' ? '#dc2626' : '#64748b' }};">{{ $ot['priority'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    {{-- ════════════════════════════════════════════ --}}
    {{-- DOCUMENT REPORT --}}
    {{-- ════════════════════════════════════════════ --}}
    @if($this->activeReport === 'documents')
        @php $dr = $this->getDocumentReport(); @endphp
        <div class="rpt-grid-3">
            <div class="rpt-card">
                <div class="rpt-card-title">By Status</div>@foreach($dr['by_status'] as $s => $c)<div
                    style="display:flex;justify-content:space-between;padding:4px 0;font-size:12px;border-bottom:1px solid #f3f4f6;">
                    <span style="font-weight:600;">{{ ucfirst($s) }}</span><span style="font-weight:700;">{{ $c }}</span>
                </div>@endforeach
            </div>
            <div class="rpt-card">
                <div class="rpt-card-title">By Discipline</div>@foreach($dr['by_discipline'] as $d => $c)<div
                    style="display:flex;justify-content:space-between;padding:4px 0;font-size:12px;border-bottom:1px solid #f3f4f6;">
                    <span style="font-weight:600;">{{ $d ?: '—' }}</span><span style="font-weight:700;">{{ $c }}</span>
                </div>@endforeach
            </div>
            <div class="rpt-card">
                <div class="rpt-card-title">By Suitability</div>@foreach($dr['by_suitability'] as $s => $c)<div
                    style="display:flex;justify-content:space-between;padding:4px 0;font-size:12px;border-bottom:1px solid #f3f4f6;">
                    <span style="font-weight:600;">{{ $s ?: '—' }}</span><span style="font-weight:700;">{{ $c }}</span>
                </div>@endforeach
            </div>
        </div>
        <div class="rpt-card">
            <div class="rpt-card-title">Recent Uploads ({{ $dr['total'] }} in period)</div>
            <table class="rpt-mini-table">
                <thead>
                    <tr>
                        <th>Doc #</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Suitability</th>
                        <th>Rev</th>
                        <th>By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dr['recent_uploads'] as $d)
                        <tr>
                            <td style="font-family:monospace;font-size:11px;">{{ $d['doc_number'] }}</td>
                            <td style="font-weight:600;">{{ Str::limit($d['title'], 35) }}</td>
                            <td><span class="rpt-badge" style="background:#6366f115;color:#6366f1;">{{ $d['status'] }}</span>
                            </td>
                            <td>{{ $d['suitability'] ?? '—' }}</td>
                            <td>{{ $d['revision'] }}</td>
                            <td>{{ $d['uploaded_by'] }}</td>
                            <td style="font-size:11px;">{{ $d['date'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ════════════════════════════════════════════ --}}
    {{-- CONTRACT REPORT --}}
    {{-- ════════════════════════════════════════════ --}}
    @if($this->activeReport === 'contracts')
        @php $cr = $this->getContractReport(); @endphp
        <div class="rpt-grid-4">
            <div class="rpt-kpi">
                <div class="rpt-kpi-value">{{ \App\Support\CurrencyHelper::formatCompact($cr['totalOriginal']) }}</div>
                <div class="rpt-kpi-label">Original Value</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:{{ $cr['variance'] > 0 ? '#ef4444' : '#10b981' }};">
                    {{ \App\Support\CurrencyHelper::formatCompact($cr['totalRevised']) }}</div>
                <div class="rpt-kpi-label">Revised Value</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:#059669;">
                    {{ \App\Support\CurrencyHelper::formatCompact($cr['totalPaid']) }}</div>
                <div class="rpt-kpi-label">Total Paid</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:{{ $cr['variance'] > 0 ? '#ef4444' : '#10b981' }};">
                    {{ ($cr['variance'] >= 0 ? '+' : '') . \App\Support\CurrencyHelper::formatCompact($cr['variance']) }}</div>
                <div class="rpt-kpi-label">Variance</div>
            </div>
        </div>
        <div class="rpt-card">
            <div class="rpt-card-title">Contract Details</div>
            <table class="rpt-mini-table">
                <thead>
                    <tr>
                        <th>Contract #</th>
                        <th>Title</th>
                        <th>Vendor</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Original</th>
                        <th>Revised</th>
                        <th>Paid</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cr['details'] as $c)
                        <tr>
                            <td style="font-family:monospace;font-size:11px;font-weight:600;">{{ $c['number'] }}</td>
                            <td>{{ Str::limit($c['title'], 30) }}</td>
                            <td>{{ $c['vendor'] }}</td>
                            <td><span class="rpt-badge" style="background:#dbeafe;color:#2563eb;">{{ $c['type'] }}</span></td>
                            <td><span class="rpt-badge"
                                    style="background:{{ $c['status'] === 'active' ? '#dcfce7' : '#f1f5f9' }};color:{{ $c['status'] === 'active' ? '#16a34a' : '#64748b' }};">{{ $c['status'] }}</span>
                            </td>
                            <td>{{ $c['original'] }}</td>
                            <td>{{ $c['revised'] }}</td>
                            <td style="color:#059669;font-weight:600;">{{ $c['paid'] }}</td>
                            <td style="font-weight:700;">{{ $c['pct'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ════════════════════════════════════════════ --}}
    {{-- SAFETY REPORT --}}
    {{-- ════════════════════════════════════════════ --}}
    @if($this->activeReport === 'safety')
        @php $sr = $this->getSafetyReport(); @endphp
        <div class="rpt-grid-3">
            <div class="rpt-card">
                <div class="rpt-card-title">By Severity</div>
                @php $sevColors = ['critical' => '#dc2626', 'fatal' => '#991b1b', 'high' => '#ea580c', 'medium' => '#d97706', 'low' => '#65a30d', 'minor' => '#10b981']; @endphp
                @foreach($sr['bySeverity'] as $s => $c)<div
                    style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f3f4f6;"><span
                        style="font-weight:600;font-size:12px;color:{{ $sevColors[$s] ?? '#6b7280' }};">{{ ucfirst($s) }}</span><span
                style="font-weight:700;font-size:12px;">{{ $c }}</span></div>@endforeach
            </div>
            <div class="rpt-card">
                <div class="rpt-card-title">By Status</div>@foreach($sr['byStatus'] as $s => $c)<div
                    style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f3f4f6;font-size:12px;">
                    <span style="font-weight:600;">{{ ucfirst($s) }}</span><span style="font-weight:700;">{{ $c }}</span>
                </div>@endforeach
            </div>
            <div class="rpt-card">
                <div class="rpt-card-title">Monthly Trend</div>
                @php $maxSf = max(1, max(array_column($sr['trend'], 'count'))); @endphp
                <div style="display:flex;align-items:flex-end;gap:4px;height:60px;">@foreach($sr['trend'] as $t)<div
                    style="flex:1;display:flex;flex-direction:column;align-items:center;">
                    <div style="width:100%;background:#ef4444;border-radius:3px 3px 0 0;height:{{ max(2, ($t['count'] / $maxSf) * 50) }}px;"
                        title="{{ $t['count'] }}"></div>
                    <div style="font-size:8px;color:#94a3b8;margin-top:2px;">{{ $t['label'] }}</div>
                </div>@endforeach</div>
            </div>
        </div>
        @if(!empty($sr['details']))
            <div class="rpt-card">
                <div class="rpt-card-title">Incident Details</div>
                <table class="rpt-mini-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sr['details'] as $i)
                            <tr>
                                <td style="font-weight:600;">{{ Str::limit($i['title'], 35) }}</td>
                                <td>{{ $i['type'] }}</td>
                                <td style="color:{{ $sevColors[$i['severity']] ?? '#6b7280' }};font-weight:700;">
                                    {{ ucfirst($i['severity']) }}</td>
                                <td><span class="rpt-badge" style="background:#f1f5f9;color:#64748b;">{{ $i['status'] }}</span></td>
                                <td style="font-size:11px;">{{ $i['date'] }}</td>
                                <td>{{ $i['location'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    {{-- ════════════════════════════════════════════ --}}
    {{-- RFI REPORT --}}
    {{-- ════════════════════════════════════════════ --}}
    @if($this->activeReport === 'rfis')
        @php $rr = $this->getRfiReport(); @endphp
        <div class="rpt-grid-3">
            <div class="rpt-kpi">
                <div class="rpt-kpi-value">{{ $rr['total'] }}</div>
                <div class="rpt-kpi-label">Total RFIs</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:#3b82f6;">{{ $rr['avg_response_days'] }}d</div>
                <div class="rpt-kpi-label">Avg Response Time</div>
            </div>
            <div class="rpt-kpi">
                <div class="rpt-kpi-value" style="color:#f59e0b;">{{ $rr['by_status']['open'] ?? 0 }}</div>
                <div class="rpt-kpi-label">Open RFIs</div>
            </div>
        </div>
        <div class="rpt-card">
            <div class="rpt-card-title">RFI Details</div>
            <table class="rpt-mini-table">
                <thead>
                    <tr>
                        <th>RFI #</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Assignee</th>
                        <th>Created</th>
                        <th>Response (days)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rr['details'] as $rfi)
                        <tr>
                            <td style="font-family:monospace;font-size:11px;">{{ $rfi['number'] }}</td>
                            <td style="font-weight:600;">{{ Str::limit($rfi['subject'], 40) }}</td>
                            <td><span class="rpt-badge" style="background:#6366f115;color:#6366f1;">{{ $rfi['status'] }}</span>
                            </td>
                            <td>{{ $rfi['assignee'] }}</td>
                            <td style="font-size:11px;">{{ $rfi['date'] }}</td>
                            <td style="font-weight:700;">{{ $rfi['response_days'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</x-filament-panels::page>