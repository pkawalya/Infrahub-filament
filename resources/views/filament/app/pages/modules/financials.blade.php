<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    @push('styles')
        <style>
            /* ─── Tab Navigation ─── */
            .financials-tabs { display:flex; gap:4px; border-bottom:2px solid #e5e7eb; margin-bottom:1rem; }
            .dark .financials-tabs { border-bottom-color: rgba(255,255,255,0.06); }
            .financials-tab { padding:10px 20px; font-size:13px; font-weight:600; border:none; cursor:pointer; border-radius:8px 8px 0 0; transition:all 0.2s; background:transparent; color:#6b7280; display:flex; align-items:center; gap:6px; }
            .dark .financials-tab { color:#9ca3af; }
            .financials-tab:hover { opacity:0.85; }
            .financials-tab.active { background:var(--primary-600, #2563eb); color:white; }
            .financials-tab .badge { font-size:11px; padding:2px 7px; border-radius:99px; margin-left:2px; }
            .financials-tab .badge-danger { background:rgba(239,68,68,.15); color:#ef4444; }
            .financials-tab .badge-warning { background:rgba(245,158,11,.15); color:#f59e0b; }
            .financials-tab .badge-success { background:rgba(16,185,129,.15); color:#10b981; }

            /* ─── Summary Strip ─── */
            .fin-summary-strip { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:1rem; }
            @media(max-width:640px) { .fin-summary-strip { grid-template-columns:repeat(2,1fr); } }
            .fin-summary-stat { padding:16px; border-radius:10px; text-align:center; }
            .fin-summary-stat.default { background:#f9fafb; border:1px solid #e5e7eb; }
            .dark .fin-summary-stat.default { background:rgba(255,255,255,.02); border-color:rgba(255,255,255,.06); }
            .fin-summary-stat .stat-num { font-size:24px; font-weight:800; }
            .fin-summary-stat .stat-label { font-size:11px; text-transform:uppercase; letter-spacing:0.5px; }
            .fin-muted { color:#6b7280; }
            .dark .fin-muted { color:#9ca3af; }

            /* ─── Alert Strip ─── */
            .fin-alert { display:flex; align-items:center; gap:8px; padding:8px 14px; border-radius:6px; margin-bottom:0.75rem; font-size:12px; }
            .fin-alert.danger { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
            .dark .fin-alert.danger { background:rgba(220,38,38,.08); border-color:rgba(220,38,38,.15); color:#f87171; }

            /* ─── Cash Flow Chart ─── */
            .cashflow-chart { display:flex; align-items:flex-end; gap:3px; height:100px; padding:8px 0; }
            .cashflow-bar { flex:1; border-radius:3px 3px 0 0; transition:height 0.3s; position:relative; min-width:0; }
            .cashflow-bar:hover { opacity:0.85; }
            .cashflow-label { font-size:8px; color:#9ca3af; text-align:center; margin-top:2px; line-height:1; }
            .cashflow-legend { display:flex; gap:12px; font-size:10px; color:#6b7280; margin-top:4px; }
            .cashflow-legend span::before { content:''; display:inline-block; width:8px; height:8px; border-radius:2px; margin-right:4px; vertical-align:middle; }
            .cashflow-legend .inv::before { background:#6366f1; }
            .cashflow-legend .rec::before { background:#10b981; }
            .cashflow-legend .exp::before { background:#ef4444; }

            /* ─── Aging Buckets ─── */
            .aging-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:0.5rem; margin-bottom:1rem; }
            @media(max-width:640px) { .aging-grid { grid-template-columns:repeat(3,1fr); } }
            .aging-bucket { text-align:center; padding:8px; border-radius:8px; border:1px solid #e5e7eb; background:#f9fafb; }
            .dark .aging-bucket { background:rgba(255,255,255,.02); border-color:rgba(255,255,255,.06); }
            .aging-bucket .bucket-label { font-size:9px; font-weight:700; text-transform:uppercase; color:#6b7280; margin-bottom:2px; }
            .aging-bucket .bucket-value { font-size:0.9rem; font-weight:800; }
            .aging-bucket .bucket-count { font-size:9px; color:#9ca3af; }

            /* ─── Category Breakdown ─── */
            .cat-breakdown { display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:1rem; }
            .cat-card { border-radius:0.5rem; padding:0.5rem 0.75rem; border:1px solid #e5e7eb; background:#f9fafb; min-width:120px; flex:1; max-width:180px; }
            .dark .cat-card { background:rgba(255,255,255,.02); border-color:rgba(255,255,255,.06); }
            .cat-card .cat-label { font-weight:600; font-size:0.6rem; text-transform:uppercase; letter-spacing:0.04em; color:#6b7280; }
            .cat-card .cat-total { font-weight:700; font-size:0.85rem; color:#ef4444; }
            .cat-card .cat-count { font-size:0.55rem; color:#9ca3af; }
        </style>
    @endpush

    {{-- Overdue Invoice Alert --}}
    @php
        $overdueInvoices = \App\Models\Invoice::where('cde_project_id', $this->record->id)
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->get();
    @endphp
    @if($overdueInvoices->isNotEmpty())
        <div class="fin-alert danger">
            <x-heroicon-o-exclamation-triangle style="width:16px;height:16px;flex-shrink:0;" />
            <span>
                <strong>{{ $overdueInvoices->count() }} overdue invoice{{ $overdueInvoices->count() > 1 ? 's' : '' }}</strong>
                totalling {{ \App\Support\CurrencyHelper::format($overdueInvoices->sum('balance_due'), 0) }} —
                {{ $overdueInvoices->take(3)->pluck('invoice_number')->join(', ') }}{{ $overdueInvoices->count() > 3 ? '…' : '' }}
            </span>
        </div>
    @endif

    {{-- Budget vs Actuals + P&L --}}
    @php $bva = $this->getBudgetVsActuals(); @endphp
    @if($bva['budget'] > 0)
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
            {{-- Budget Progress --}}
            <div style="background:white;border:1px solid #e2e8f0;border-radius:10px;padding:16px;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;margin-bottom:10px;display:flex;align-items:center;gap:5px;">
                    <x-heroicon-o-calculator style="width:13px;height:13px;" />
                    Budget vs Actuals · {{ $bva['budget_fmt'] }} Budget
                </div>
                @foreach([
                    ['Invoiced', $bva['invoiced_pct'], $bva['invoiced_fmt'], '#6366f1'],
                    ['Received', $bva['received_pct'], $bva['received_fmt'], '#10b981'],
                    ['Expenses', $bva['expenses_pct'], $bva['expenses_fmt'], '#ef4444'],
                ] as [$label, $pct, $fmt, $color])
                    <div style="margin-bottom:8px;">
                        <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:2px;">
                            <span style="font-weight:600;color:#475569;">{{ $label }}</span>
                            <span style="font-weight:700;color:{{ $color }};">{{ $fmt }} <span style="font-weight:400;color:#94a3b8;">({{ $pct }}%)</span></span>
                        </div>
                        <div style="height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                            <div style="width:{{ min($pct, 100) }}%;height:100%;background:{{ $color }};border-radius:3px;transition:width .4s;"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Profit / Loss Summary --}}
            <div style="background:white;border:1px solid #e2e8f0;border-radius:10px;padding:16px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;margin-bottom:8px;">
                    Net Position
                </div>
                <div style="font-size:36px;font-weight:800;color:{{ $bva['profit_positive'] ? '#10b981' : '#ef4444' }};line-height:1.1;letter-spacing:-0.02em;">
                    {{ $bva['profit_positive'] ? '+' : '-' }}{{ $bva['profit_fmt'] }}
                </div>
                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                    {{ $bva['profit_positive'] ? '🟢 Profit' : '🔴 Loss' }} · Received {{ $bva['received_fmt'] }} − Expenses {{ $bva['expenses_fmt'] }}
                </div>
                {{-- Mini gauge --}}
                <div style="width:100%;max-width:200px;margin-top:12px;">
                    <div style="display:flex;justify-content:space-between;font-size:9px;color:#94a3b8;margin-bottom:2px;">
                        <span>Expenses</span>
                        <span>Received</span>
                    </div>
                    <div style="height:8px;background:#fef2f2;border-radius:4px;overflow:hidden;position:relative;">
                        @php $ratio = ($bva['received'] + $bva['expenses']) > 0 ? ($bva['received'] / ($bva['received'] + $bva['expenses'])) * 100 : 50; @endphp
                        <div style="width:{{ $ratio }}%;height:100%;background:#10b981;border-radius:4px;"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Invoice Pipeline Bar --}}
    @php $invPipe = $this->getInvoicePipeline(); @endphp
    <div style="display:flex;gap:2px;background:white;border:1px solid #e2e8f0;border-radius:8px;padding:4px;margin-bottom:14px;">
        @foreach($invPipe as $p)
            <div style="flex:1;min-width:60px;text-align:center;padding:6px 4px;border-radius:6px;background:{{ $p['bg'] }};color:{{ $p['color'] }};">
                <div style="font-size:16px;font-weight:800;line-height:1.2;">{{ $p['count'] }}</div>
                <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-top:1px;opacity:0.7;">{{ $p['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Tab Navigation --}}
    <div class="financials-tabs">
        @foreach([
            'invoices' => ['icon' => 'heroicon-o-document-text', 'label' => 'Invoices'],
            'receipts' => ['icon' => 'heroicon-o-banknotes', 'label' => 'Receipts'],
            'expenses' => ['icon' => 'heroicon-o-credit-card', 'label' => 'Expenses'],
        ] as $tab => $meta)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="financials-tab {{ $this->activeTab === $tab ? 'active' : '' }}">
                <x-dynamic-component :component="$meta['icon']" style="width:14px;height:14px;" />
                {{ $meta['label'] }}
                @if($tab === 'invoices')
                    @php $overdueCount = $overdueInvoices->count(); @endphp
                    @php $draftCount = \App\Models\Invoice::where('cde_project_id', $this->record->id)->where('status', 'draft')->count(); @endphp
                    @if($overdueCount > 0)
                        <span class="badge badge-danger">{{ $overdueCount }}</span>
                    @elseif($draftCount > 0)
                        <span class="badge badge-warning">{{ $draftCount }}</span>
                    @endif
                @elseif($tab === 'receipts')
                    @php $receiptCount = \App\Models\InvoicePayment::where('cde_project_id', $this->record->id)->count(); @endphp
                    @if($receiptCount > 0)
                        <span class="badge badge-success">{{ $receiptCount }}</span>
                    @endif
                @elseif($tab === 'expenses')
                    @php $pendingExp = \App\Models\Expense::where('cde_project_id', $this->record->id)->where('status', 'pending')->count(); @endphp
                    @if($pendingExp > 0)
                        <span class="badge badge-warning">{{ $pendingExp }}</span>
                    @endif
                @endif
            </button>
        @endforeach
    </div>

    @if($this->activeTab === 'invoices')
        {{-- Cash Flow Mini Chart + Aging Analysis --}}
        @php
            $analytics = $this->getFinancialAnalytics();
            $cashFlow = $analytics['cash_flow'];
            $maxVal = max(1, max(array_column($cashFlow, 'invoiced') ?: [1]), max(array_column($cashFlow, 'received') ?: [1]), max(array_column($cashFlow, 'expenses') ?: [1]));
            $aging = $this->getAgingAnalysis();
        @endphp

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
            {{-- Cash Flow Chart --}}
            <div style="border:1px solid #e5e7eb;border-radius:10px;padding:12px 16px;background:#fafbfc;">
                <div style="font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:4px;display:flex;align-items:center;gap:4px;">
                    <x-heroicon-o-chart-bar style="width:14px;height:14px;" />
                    Cash Flow (12 Months)
                </div>
                <div class="cashflow-chart">
                    @foreach($cashFlow as $m)
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:1px;">
                            <div style="display:flex;gap:1px;align-items:flex-end;height:80px;width:100%;">
                                <div class="cashflow-bar" style="background:#6366f1;height:{{ $maxVal > 0 ? max(2, ($m['invoiced'] / $maxVal) * 80) : 2 }}px;" title="Invoiced: {{ \App\Support\CurrencyHelper::format($m['invoiced'], 0) }}"></div>
                                <div class="cashflow-bar" style="background:#10b981;height:{{ $maxVal > 0 ? max(2, ($m['received'] / $maxVal) * 80) : 2 }}px;" title="Received: {{ \App\Support\CurrencyHelper::format($m['received'], 0) }}"></div>
                                <div class="cashflow-bar" style="background:#ef4444;height:{{ $maxVal > 0 ? max(2, ($m['expenses'] / $maxVal) * 80) : 2 }}px;" title="Expenses: {{ \App\Support\CurrencyHelper::format($m['expenses'], 0) }}"></div>
                            </div>
                            <div class="cashflow-label">{{ $m['label'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="cashflow-legend">
                    <span class="inv">Invoiced</span>
                    <span class="rec">Received</span>
                    <span class="exp">Expenses</span>
                </div>
            </div>

            {{-- Aging Analysis --}}
            <div style="border:1px solid #e5e7eb;border-radius:10px;padding:12px 16px;background:#fafbfc;">
                <div style="font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:8px;display:flex;align-items:center;gap:4px;">
                    <x-heroicon-o-clock style="width:14px;height:14px;" />
                    Receivables Aging · {{ $aging['total_fmt'] }} outstanding
                </div>
                <div class="aging-grid">
                    @foreach([
                        'current' => ['label' => 'Current', 'color' => '#059669'],
                        '1_30'    => ['label' => '1-30 Days', 'color' => '#3b82f6'],
                        '31_60'   => ['label' => '31-60 Days', 'color' => '#d97706'],
                        '61_90'   => ['label' => '61-90 Days', 'color' => '#ea580c'],
                        '90_plus' => ['label' => '90+ Days', 'color' => '#dc2626'],
                    ] as $key => $meta)
                        <div class="aging-bucket">
                            <div class="bucket-label">{{ $meta['label'] }}</div>
                            <div class="bucket-value" style="color:{{ $meta['color'] }};">{{ \App\Support\CurrencyHelper::formatCompact($aging['buckets'][$key]) }}</div>
                            <div class="bucket-count">{{ $aging['counts'][$key] }} inv.</div>
                        </div>
                    @endforeach
                </div>
                {{-- Aging bar --}}
                @if($aging['total'] > 0)
                    <div style="display:flex;height:6px;border-radius:3px;overflow:hidden;">
                        @foreach([
                            'current' => '#059669', '1_30' => '#3b82f6', '31_60' => '#d97706', '61_90' => '#ea580c', '90_plus' => '#dc2626'
                        ] as $key => $color)
                            @if($aging['buckets'][$key] > 0)
                                <div style="width:{{ ($aging['buckets'][$key] / $aging['total']) * 100 }}%;background:{{ $color }};"></div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Invoice summary strip --}}
        @php
            $invBase = \App\Models\Invoice::where('cde_project_id', $this->record->id);
            $totalInv = (clone $invBase)->count();
            $paidInv = (clone $invBase)->where('status', 'paid')->count();
            $overdueInv = $overdueInvoices->count();
            $outstandingAmt = (clone $invBase)->whereNotIn('status', ['paid', 'cancelled'])->selectRaw('COALESCE(SUM(total_amount - amount_paid), 0) as balance')->value('balance');
        @endphp
        <div class="fin-summary-strip">
            <div class="fin-summary-stat default">
                <div class="stat-num">{{ $totalInv }}</div>
                <div class="stat-label fin-muted">Total Invoices</div>
            </div>
            <div class="fin-summary-stat" style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.1);">
                <div class="stat-num" style="color:#10b981;">{{ $paidInv }}</div>
                <div class="stat-label" style="color:#10b981;">Fully Paid</div>
            </div>
            <div class="fin-summary-stat" style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.1);">
                <div class="stat-num" style="color:#ef4444;">{{ $overdueInv }}</div>
                <div class="stat-label" style="color:#ef4444;">Overdue</div>
            </div>
            <div class="fin-summary-stat" style="background:rgba(245,158,11,0.05);border:1px solid rgba(245,158,11,0.1);">
                <div class="stat-num" style="color:#d97706;">{{ \App\Support\CurrencyHelper::format($outstandingAmt, 0) }}</div>
                <div class="stat-label" style="color:#d97706;">Outstanding</div>
            </div>
        </div>
        {{ $this->table }}

    @elseif($this->activeTab === 'receipts')
        {{-- Receipt summary strip --}}
        @php
            $recBase = \App\Models\InvoicePayment::where('cde_project_id', $this->record->id);
            $totalRec = (clone $recBase)->count();
            $totalRecAmt = (clone $recBase)->sum('amount');
            $avgRec = $totalRec > 0 ? $totalRecAmt / $totalRec : 0;
            $lastRec = (clone $recBase)->orderByDesc('payment_date')->first();
        @endphp
        <div class="fin-summary-strip">
            <div class="fin-summary-stat default">
                <div class="stat-num">{{ $totalRec }}</div>
                <div class="stat-label fin-muted">Total Receipts</div>
            </div>
            <div class="fin-summary-stat" style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.1);">
                <div class="stat-num" style="color:#10b981;">{{ \App\Support\CurrencyHelper::formatCompact($totalRecAmt) }}</div>
                <div class="stat-label" style="color:#10b981;">Total Received</div>
            </div>
            <div class="fin-summary-stat default">
                <div class="stat-num">{{ \App\Support\CurrencyHelper::formatCompact($avgRec) }}</div>
                <div class="stat-label fin-muted">Avg Payment</div>
            </div>
            <div class="fin-summary-stat default">
                <div class="stat-num" style="font-size:16px;">{{ $lastRec?->payment_date?->format('M d, Y') ?? '—' }}</div>
                <div class="stat-label fin-muted">Last Payment</div>
            </div>
        </div>
        {{ $this->table }}

    @elseif($this->activeTab === 'expenses')
        {{-- Expense Category Breakdown --}}
        @php
            $analytics = $this->getFinancialAnalytics();
            $catBreakdown = $analytics['expense_breakdown'];
            $expBase = \App\Models\Expense::where('cde_project_id', $this->record->id);
            $totalExp = (clone $expBase)->count();
            $pendingExp = (clone $expBase)->where('status', 'pending')->count();
            $paidExp = (clone $expBase)->where('status', 'paid')->count();
            $rejectedExp = (clone $expBase)->where('status', 'rejected')->count();
        @endphp

        @if(count($catBreakdown) > 0)
            <div style="margin-bottom:1rem;">
                <div style="font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:6px;display:flex;align-items:center;gap:4px;">
                    <x-heroicon-o-chart-pie style="width:14px;height:14px;" />
                    Expense Breakdown by Category
                </div>
                <div class="cat-breakdown">
                    @foreach($catBreakdown as $cat)
                        <div class="cat-card">
                            <div class="cat-label">{{ $cat['label'] }}</div>
                            <div class="cat-total">{{ $cat['total_fmt'] }}</div>
                            <div class="cat-count">{{ $cat['count'] }} expense{{ $cat['count'] !== 1 ? 's' : '' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="fin-summary-strip">
            <div class="fin-summary-stat default">
                <div class="stat-num">{{ $totalExp }}</div>
                <div class="stat-label fin-muted">Total Expenses</div>
            </div>
            <div class="fin-summary-stat" style="background:rgba(245,158,11,0.05);border:1px solid rgba(245,158,11,0.1);">
                <div class="stat-num" style="color:#f59e0b;">{{ $pendingExp }}</div>
                <div class="stat-label" style="color:#f59e0b;">Pending</div>
            </div>
            <div class="fin-summary-stat" style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.1);">
                <div class="stat-num" style="color:#10b981;">{{ $paidExp }}</div>
                <div class="stat-label" style="color:#10b981;">Paid</div>
            </div>
            <div class="fin-summary-stat" style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.1);">
                <div class="stat-num" style="color:#ef4444;">{{ $rejectedExp }}</div>
                <div class="stat-label" style="color:#ef4444;">Rejected</div>
            </div>
        </div>
        {{ $this->table }}
    @endif
</x-filament-panels::page>