<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    @push('styles')
        <style>
            .rfi-pipeline {
                display: flex;
                gap: 2px;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 4px;
                margin-bottom: 14px;
                overflow-x: auto;
            }

            .dark .rfi-pipeline {
                background: rgba(30, 41, 59, 0.5);
                border-color: rgba(255, 255, 255, 0.08);
            }

            .rfi-pipe-item {
                flex: 1;
                min-width: 70px;
                text-align: center;
                padding: 8px 4px;
                border-radius: 6px;
            }

            .rfi-pipe-count {
                font-size: 18px;
                font-weight: 800;
                line-height: 1.2;
            }

            .rfi-pipe-label {
                font-size: 9px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-top: 2px;
                opacity: 0.7;
            }

            .rfi-tab-wrap {
                display: flex;
                gap: 4px;
                margin-bottom: 14px;
            }

            .rfi-tab-btn {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 18px;
                font-size: 13px;
                font-weight: 600;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                background: white;
                color: #475569;
                cursor: pointer;
                transition: all .15s;
            }

            .dark .rfi-tab-btn {
                background: rgba(30, 41, 59, 0.5);
                border-color: rgba(255, 255, 255, 0.08);
                color: #94a3b8;
            }

            .rfi-tab-btn:hover {
                background: #f8fafc;
            }

            .rfi-tab-btn.active {
                background: #4f46e5;
                color: white;
                border-color: #4f46e5;
                box-shadow: 0 2px 4px rgba(79, 70, 229, 0.25);
            }

            .rfi-tab-btn svg {
                width: 16px;
                height: 16px;
            }

            .rfi-tab-badge {
                font-size: 10px;
                font-weight: 700;
                padding: 1px 7px;
                border-radius: 99px;
            }

            .rfi-overdue-alert {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 14px;
                border-radius: 6px;
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #991b1b;
                font-size: 12px;
                margin-bottom: 12px;
            }

            .dark .rfi-overdue-alert {
                background: rgba(220, 38, 38, 0.08);
                border-color: rgba(220, 38, 38, 0.15);
                color: #f87171;
            }
        </style>
    @endpush

    {{-- Pipeline Bar --}}
    @php
        $pipeline = $this->activeTab === 'rfis' ? $this->getRfiPipeline() : $this->getSubmittalPipeline();
    @endphp
    <div class="rfi-pipeline">
        @foreach($pipeline as $pipe)
            <div class="rfi-pipe-item" style="background:{{ $pipe['bg'] }};color:{{ $pipe['color'] }};">
                <div class="rfi-pipe-count">{{ $pipe['count'] }}</div>
                <div class="rfi-pipe-label">{{ $pipe['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Overdue RFI Alert --}}
    @if($this->activeTab === 'rfis')
        @php
            $overdueRfis = $this->record->rfis()
                ->whereIn('status', ['open', 'under_review'])
                ->where('due_date', '<', now())->get();
        @endphp
        @if($overdueRfis->isNotEmpty())
            <div class="rfi-overdue-alert">
                <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <strong>{{ $overdueRfis->count() }} overdue RFI{{ $overdueRfis->count() > 1 ? 's' : '' }}:</strong>
                {{ $overdueRfis->pluck('rfi_number')->join(', ') }}
            </div>
        @endif
    @endif

    {{-- Tab Switcher --}}
    <div class="rfi-tab-wrap">
        <button wire:click="$set('activeTab', 'rfis')"
            class="rfi-tab-btn {{ $this->activeTab === 'rfis' ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
            </svg>
            RFIs
            @php $rfiCount = $this->record->rfis()->count() @endphp
            @if($rfiCount > 0)
                <span class="rfi-tab-badge"
                    style="{{ $this->activeTab === 'rfis' ? 'background:rgba(255,255,255,0.2);color:white;' : 'background:#eef2ff;color:#4f46e5;' }}">{{ $rfiCount }}</span>
            @endif
        </button>
        <button wire:click="$set('activeTab', 'submittals')"
            class="rfi-tab-btn {{ $this->activeTab === 'submittals' ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Submittals
            @php $subCount = $this->record->submittals()->count() @endphp
            @if($subCount > 0)
                <span class="rfi-tab-badge"
                    style="{{ $this->activeTab === 'submittals' ? 'background:rgba(255,255,255,0.2);color:white;' : 'background:#f0f9ff;color:#0284c7;' }}">{{ $subCount }}</span>
            @endif
        </button>
    </div>

    {{ $this->table }}
</x-filament-panels::page>