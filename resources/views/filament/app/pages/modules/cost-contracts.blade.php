<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    @push('styles')
        <style>
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