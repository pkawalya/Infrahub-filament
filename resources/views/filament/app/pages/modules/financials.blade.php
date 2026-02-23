<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    @push('styles')
        <style>
            /* ─── Financials Tab Navigation ─── */
            .financials-tabs {
                display: flex;
                gap: 4px;
                border-bottom: 2px solid #e5e7eb;
                padding-bottom: 0;
                margin-bottom: 1.5rem;
            }

            .dark .financials-tabs {
                border-bottom-color: rgba(255, 255, 255, 0.06);
            }

            .financials-tab {
                padding: 10px 20px;
                font-size: 13px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                border-radius: 8px 8px 0 0;
                transition: all 0.2s;
                background: transparent;
                color: #6b7280;
            }

            .dark .financials-tab {
                color: #9ca3af;
            }

            .financials-tab:hover {
                opacity: 0.85;
            }

            .financials-tab.active {
                background: var(--primary-600, #2563eb);
                color: white;
            }

            .financials-tab .badge {
                font-size: 11px;
                padding: 2px 7px;
                border-radius: 99px;
                margin-left: 6px;
            }

            .financials-tab .badge-danger {
                background: rgba(239, 68, 68, 0.15);
                color: #ef4444;
            }

            .financials-tab .badge-warning {
                background: rgba(245, 158, 11, 0.15);
                color: #f59e0b;
            }

            .financials-tab .badge-success {
                background: rgba(16, 185, 129, 0.15);
                color: #10b981;
            }

            /* ─── Expense Summary Strip ─── */
            .fin-summary-strip {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 12px;
                margin-bottom: 1.5rem;
            }

            @media (max-width: 640px) {
                .fin-summary-strip {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            .fin-summary-stat {
                padding: 16px;
                border-radius: 10px;
                text-align: center;
            }

            .fin-summary-stat.default {
                background: #f9fafb;
                border: 1px solid #e5e7eb;
            }

            .dark .fin-summary-stat.default {
                background: rgba(255, 255, 255, 0.02);
                border-color: rgba(255, 255, 255, 0.06);
            }

            .fin-summary-stat .stat-num {
                font-size: 24px;
                font-weight: 800;
            }

            .fin-summary-stat .stat-label {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .fin-muted {
                color: #6b7280;
            }

            .dark .fin-muted {
                color: #9ca3af;
            }
        </style>
    @endpush

    {{-- Tab Navigation --}}
    <div class="financials-tabs">
        @foreach(['invoices' => '📄 Invoices', 'receipts' => '💵 Receipts', 'expenses' => '💳 Expenses'] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="financials-tab {{ $this->activeTab === $tab ? 'active' : '' }}">
                {{ $label }}
                @if($tab === 'invoices')
                    @php $overdueCount = \App\Models\Invoice::where('cde_project_id', $this->record->id)->whereNotIn('status', ['paid', 'cancelled'])->whereNotNull('due_date')->where('due_date', '<', now())->count(); @endphp
                    @php $draftCount = \App\Models\Invoice::where('cde_project_id', $this->record->id)->where('status', 'draft')->count(); @endphp
                    @if($overdueCount > 0)
                        <span class="badge badge-danger">{{ $overdueCount }} overdue</span>
                    @elseif($draftCount > 0)
                        <span class="badge badge-warning">{{ $draftCount }}</span>
                    @endif
                @elseif($tab === 'receipts')
                    @php $receiptCount = \App\Models\InvoicePayment::where('cde_project_id', $this->record->id)->count(); @endphp
                    @if($receiptCount > 0)
                        <span class="badge badge-success">{{ $receiptCount }}</span>
                    @endif
                @elseif($tab === 'expenses')
                    @php $pendingExpenses = \App\Models\Expense::where('cde_project_id', $this->record->id)->where('status', 'pending')->count(); @endphp
                    @if($pendingExpenses > 0)
                        <span class="badge badge-warning">{{ $pendingExpenses }} pending</span>
                    @endif
                @endif
            </button>
        @endforeach
    </div>

    @if($this->activeTab === 'invoices')
        {{-- Invoice summary strip --}}
        @php
            $invBase = \App\Models\Invoice::where('cde_project_id', $this->record->id);
            $totalInv = (clone $invBase)->count();
            $paidInv = (clone $invBase)->where('status', 'paid')->count();
            $overdueInv = (clone $invBase)->whereNotIn('status', ['paid', 'cancelled'])->whereNotNull('due_date')->where('due_date', '<', now())->count();
            $outstandingAmt = (clone $invBase)->whereNotIn('status', ['paid', 'cancelled'])->selectRaw('COALESCE(SUM(total_amount - amount_paid), 0) as balance')->value('balance');
        @endphp
        <div class="fin-summary-strip">
            <div class="fin-summary-stat default">
                <div class="stat-num">{{ $totalInv }}</div>
                <div class="stat-label fin-muted">Total Invoices</div>
            </div>
            <div class="fin-summary-stat" style="background: rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.1);">
                <div class="stat-num" style="color:#10b981;">{{ $paidInv }}</div>
                <div class="stat-label" style="color:#10b981;">Fully Paid</div>
            </div>
            <div class="fin-summary-stat" style="background: rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.1);">
                <div class="stat-num" style="color:#ef4444;">{{ $overdueInv }}</div>
                <div class="stat-label" style="color:#ef4444;">Overdue</div>
            </div>
            <div class="fin-summary-stat" style="background: rgba(245,158,11,0.05); border:1px solid rgba(245,158,11,0.1);">
                <div class="stat-num" style="color:#d97706;">{{ \App\Support\CurrencyHelper::format($outstandingAmt, 0) }}
                </div>
                <div class="stat-label" style="color:#d97706;">Outstanding</div>
            </div>
        </div>
        {{ $this->table }}

    @elseif($this->activeTab === 'receipts')
        {{ $this->table }}

    @elseif($this->activeTab === 'expenses')
        {{-- Expense summary strip --}}
        @php
            $expBase = \App\Models\Expense::where('cde_project_id', $this->record->id);
            $totalExp = (clone $expBase)->count();
            $pendingExp = (clone $expBase)->where('status', 'pending')->count();
            $paidExp = (clone $expBase)->where('status', 'paid')->count();
            $rejectedExp = (clone $expBase)->where('status', 'rejected')->count();
        @endphp
        <div class="fin-summary-strip">
            <div class="fin-summary-stat default">
                <div class="stat-num">{{ $totalExp }}</div>
                <div class="stat-label fin-muted">Total Expenses</div>
            </div>
            <div class="fin-summary-stat" style="background: rgba(245,158,11,0.05); border:1px solid rgba(245,158,11,0.1);">
                <div class="stat-num" style="color:#f59e0b;">{{ $pendingExp }}</div>
                <div class="stat-label" style="color:#f59e0b;">Pending</div>
            </div>
            <div class="fin-summary-stat" style="background: rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.1);">
                <div class="stat-num" style="color:#10b981;">{{ $paidExp }}</div>
                <div class="stat-label" style="color:#10b981;">Paid</div>
            </div>
            <div class="fin-summary-stat" style="background: rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.1);">
                <div class="stat-num" style="color:#ef4444;">{{ $rejectedExp }}</div>
                <div class="stat-label" style="color:#ef4444;">Rejected</div>
            </div>
        </div>
        {{ $this->table }}
    @endif
</x-filament-panels::page>