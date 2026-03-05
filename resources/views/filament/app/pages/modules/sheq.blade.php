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
            <div class="sheq-snag-stat" style="background: rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.1);">
                <div class="stat-num" style="color:#ef4444;">{{ $openSocial }}</div>
                <div class="stat-label" style="color:#ef4444;">Open</div>
            </div>
            <div class="sheq-snag-stat" style="background: rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.1);">
                <div class="stat-num" style="color:#10b981;">{{ $resolvedSocial }}</div>
                <div class="stat-label" style="color:#10b981;">Resolved</div>
            </div>
            <div class="sheq-snag-stat" style="background: rgba(139,92,246,0.05); border:1px solid rgba(139,92,246,0.1);">
                <div class="stat-num" style="color:#8b5cf6;">{{ $grievances }}</div>
                <div class="stat-label" style="color:#8b5cf6;">Open Grievances</div>
            </div>
        </div>

        {{ $this->table }}
    @endif
</x-filament-panels::page>