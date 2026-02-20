<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    @push('styles')
        <style>
            /* ─── SHEQ Tab Navigation ─── */
            .sheq-tabs {
                display: flex;
                gap: 4px;
                border-bottom: 2px solid #e5e7eb;
                padding-bottom: 0;
                margin-bottom: 1.5rem;
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

            .sheq-tab .badge {
                font-size: 11px;
                padding: 2px 7px;
                border-radius: 99px;
                margin-left: 6px;
            }

            .sheq-tab .badge-danger {
                background: rgba(239, 68, 68, 0.15);
                color: #ef4444;
            }

            .sheq-tab .badge-warning {
                background: rgba(245, 158, 11, 0.15);
                color: #f59e0b;
            }

            /* ─── Snag Summary ─── */
            .sheq-snag-summary {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 12px;
                margin-bottom: 1.5rem;
            }

            @media (max-width: 640px) {
                .sheq-snag-summary {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            .sheq-snag-stat {
                padding: 16px;
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
                font-size: 24px;
                font-weight: 800;
            }

            .sheq-snag-stat .stat-label {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .sheq-muted {
                color: #6b7280;
            }

            .dark .sheq-muted {
                color: #9ca3af;
            }
        </style>
    @endpush

    {{-- Tab Navigation --}}
    <div class="sheq-tabs">
        @foreach(['incidents' => '🛡️ Incidents', 'inspections' => '📋 Inspections', 'snags' => '🐛 Snag / Defect List'] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="sheq-tab {{ $this->activeTab === $tab ? 'active' : '' }}">
                {{ $label }}
                @if($tab === 'incidents')
                    @php $incidentCount = \App\Models\SafetyIncident::where('cde_project_id', $this->record->id)->whereIn('status', ['reported', 'investigating'])->count(); @endphp
                    @if($incidentCount > 0)
                        <span class="badge badge-danger">{{ $incidentCount }}</span>
                    @endif
                @elseif($tab === 'snags')
                    @php $snagCount = \App\Models\SnagItem::where('cde_project_id', $this->record->id)->whereIn('status', ['open', 'in_progress'])->count(); @endphp
                    @if($snagCount > 0)
                        <span class="badge badge-warning">{{ $snagCount }}</span>
                    @endif
                @endif
            </button>
        @endforeach
    </div>

    @if($this->activeTab === 'incidents')
        {{ $this->table }}

    @elseif($this->activeTab === 'inspections')
        {{ $this->table }}

    @elseif($this->activeTab === 'snags')
        {{-- Snag summary strip --}}
        @php
            $snagStats = \App\Models\SnagItem::where('cde_project_id', $this->record->id);
            $totalSnags = (clone $snagStats)->count();
            $openSnags = (clone $snagStats)->whereIn('status', ['open', 'in_progress'])->count();
            $resolvedSnags = (clone $snagStats)->whereIn('status', ['resolved', 'verified', 'closed'])->count();
            $criticalSnags = (clone $snagStats)->where('severity', 'critical')->count();
        @endphp

        <div class="sheq-snag-summary">
            <div class="sheq-snag-stat default">
                <div class="stat-num">{{ $totalSnags }}</div>
                <div class="stat-label sheq-muted">Total Snags</div>
            </div>
            <div class="sheq-snag-stat" style="background: rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.1);">
                <div class="stat-num" style="color:#ef4444;">{{ $openSnags }}</div>
                <div class="stat-label" style="color:#ef4444;">Open</div>
            </div>
            <div class="sheq-snag-stat" style="background: rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.1);">
                <div class="stat-num" style="color:#10b981;">{{ $resolvedSnags }}</div>
                <div class="stat-label" style="color:#10b981;">Resolved</div>
            </div>
            <div class="sheq-snag-stat" style="background: rgba(245,158,11,0.05); border:1px solid rgba(245,158,11,0.1);">
                <div class="stat-num" style="color:#f59e0b;">{{ $criticalSnags }}</div>
                <div class="stat-label" style="color:#f59e0b;">Critical</div>
            </div>
        </div>

        {{ $this->table }}
    @endif
</x-filament-panels::page>