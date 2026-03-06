<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    @push('styles')
        <style>
            .cc-waterfall {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                gap: 4px;
                margin-bottom: 16px;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 16px;
            }

            .dark .cc-waterfall {
                background: rgba(30, 41, 59, 0.5);
                border-color: rgba(255, 255, 255, 0.08);
            }

            .cc-wf-item {
                text-align: center;
                padding: 10px 6px;
                position: relative;
            }

            .cc-wf-item+.cc-wf-item::before {
                content: '→';
                position: absolute;
                left: -6px;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                font-size: 14px;
            }

            .cc-wf-label {
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #64748b;
                margin-bottom: 4px;
            }

            .dark .cc-wf-label {
                color: #94a3b8;
            }

            .cc-wf-val {
                font-size: 16px;
                font-weight: 800;
                letter-spacing: -0.02em;
            }

            .cc-progress-wrap {
                margin-bottom: 16px;
                padding: 14px 18px;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
            }

            .dark .cc-progress-wrap {
                background: rgba(30, 41, 59, 0.5);
                border-color: rgba(255, 255, 255, 0.08);
            }

            .cc-progress-bar {
                height: 10px;
                border-radius: 99px;
                background: #e2e8f0;
                overflow: hidden;
            }

            .dark .cc-progress-bar {
                background: rgba(255, 255, 255, 0.08);
            }

            .cc-progress-fill {
                height: 100%;
                border-radius: 99px;
                transition: width .4s ease;
            }

            .cc-progress-meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 11px;
                color: #64748b;
                margin-top: 6px;
            }

            .dark .cc-progress-meta {
                color: #94a3b8;
            }

            .cert-alert-strip {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 6px 14px;
                border-radius: 6px;
                margin-bottom: 0.75rem;
                font-size: 11px;
            }

            .cert-alert-strip.warn {
                background: #fffbeb;
                border: 1px solid #fde68a;
                color: #92400e;
            }

            .cert-alert-strip.danger {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #991b1b;
            }

            .dark .cert-alert-strip.warn {
                background: rgba(217, 119, 6, .08);
                border-color: rgba(217, 119, 6, .15);
                color: #fbbf24;
            }

            .dark .cert-alert-strip.danger {
                background: rgba(220, 38, 38, .08);
                border-color: rgba(220, 38, 38, .15);
                color: #f87171;
            }
        </style>
    @endpush

    {{-- Payment Waterfall --}}
    @php
        $cs = $this->getContractSummary();
        $fmt = fn($v) => \App\Support\CurrencyHelper::formatCompact($v);
    @endphp
    @if($cs['total'] > 0)
        <div class="cc-waterfall">
            <div class="cc-wf-item">
                <div class="cc-wf-label">Original</div>
                <div class="cc-wf-val" style="color:#6366f1;">{{ $fmt($cs['original']) }}</div>
            </div>
            <div class="cc-wf-item">
                <div class="cc-wf-label">Revised</div>
                <div class="cc-wf-val" style="color:#d97706;">{{ $fmt($cs['revised']) }}</div>
            </div>
            <div class="cc-wf-item">
                <div class="cc-wf-label">Paid</div>
                <div class="cc-wf-val" style="color:#059669;">{{ $fmt($cs['paid']) }}</div>
            </div>
            <div class="cc-wf-item">
                <div class="cc-wf-label">Retainage</div>
                <div class="cc-wf-val" style="color:#9333ea;">{{ $fmt($cs['retainage']) }}</div>
            </div>
            <div class="cc-wf-item">
                <div class="cc-wf-label">Balance</div>
                <div class="cc-wf-val" style="color:#ef4444;">{{ $fmt($cs['balance']) }}</div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="cc-progress-wrap">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                <span style="font-size:12px;font-weight:700;color:#1e293b;">Payment Progress</span>
                <span style="font-size:18px;font-weight:800;color:#059669;">{{ $cs['paid_percent'] }}%</span>
            </div>
            <div class="cc-progress-bar">
                <div class="cc-progress-fill"
                    style="width:{{ min(100, $cs['paid_percent']) }}%;background:linear-gradient(90deg,#10b981,#059669);">
                </div>
            </div>
            <div class="cc-progress-meta">
                <span>{{ $cs['active'] }} active · {{ $cs['completed'] }} completed</span>
                <span>{{ $cs['total'] }} contracts</span>
            </div>
        </div>
    @endif

    {{-- Certificate Expiry Alerts --}}
    @php
        $certs = \App\Models\Certificate::where('cde_project_id', $this->record->id)->get();
        $expired = $certs->filter(fn($c) => $c->isExpired());
        $expiring = $certs->filter(fn($c) => $c->isExpiringSoon() && !$c->isExpired());
    @endphp
    @if($expired->isNotEmpty())
        <div class="cert-alert-strip danger">
            <span>⚠️</span>
            <span><strong>{{ $expired->count() }} certificate{{ $expired->count() > 1 ? 's' : '' }} expired:</strong>
                {{ $expired->pluck('name')->join(', ') }}
            </span>
        </div>
    @endif
    @if($expiring->isNotEmpty())
        <div class="cert-alert-strip warn">
            <span>⏰</span>
            <span><strong>{{ $expiring->count() }} certificate{{ $expiring->count() > 1 ? 's' : '' }} expiring
                    soon:</strong>
                {{ $expiring->map(fn($c) => $c->name . ' (' . $c->daysUntilExpiry() . 'd)')->join(', ') }}
            </span>
        </div>
    @endif

    {{ $this->table }}
</x-filament-panels::page>