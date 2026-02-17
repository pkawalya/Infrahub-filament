<x-filament-panels::page>

    @push('styles')
        <style>
            /* Stat Cards */
            .cde-stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
                margin-bottom: 1.25rem;
            }

            .cde-stat-card {
                position: relative;
                border-radius: 1rem;
                padding: 1.25rem 1.5rem;
                overflow: hidden;
                transition: transform 250ms cubic-bezier(.4, 0, .2, 1), box-shadow 250ms cubic-bezier(.4, 0, .2, 1);
            }

            .cde-stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px -5px rgba(0, 0, 0, .1), 0 4px 10px -5px rgba(0, 0, 0, .05);
            }

            .cde-stat-card.primary {
                background: linear-gradient(135deg, #0f766e 0%, #14b8a6 50%, #2dd4bf 100%);
                color: white;
            }

            .cde-stat-card.secondary {
                background: white;
                border: 1px solid #e5e7eb;
            }

            .dark .cde-stat-card.secondary {
                background: rgba(31, 41, 55, .7);
                border-color: rgba(255, 255, 255, .08);
            }

            .cde-stat-card::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -30%;
                width: 140px;
                height: 140px;
                border-radius: 50%;
                opacity: 0.08;
            }

            .cde-stat-card.primary::before {
                background: white;
            }

            .cde-stat-card.secondary::before {
                background: currentColor;
            }

            .cde-stat-label {
                font-size: 0.6875rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                margin-bottom: 0.25rem;
            }

            .cde-stat-card.primary .cde-stat-label {
                color: rgba(255, 255, 255, .7);
            }

            .cde-stat-card.secondary .cde-stat-label {
                color: #6b7280;
            }

            .cde-stat-value {
                font-size: 2rem;
                font-weight: 800;
                line-height: 1.1;
            }

            .cde-stat-card.secondary .cde-stat-value {
                color: #111827;
            }

            .dark .cde-stat-card.secondary .cde-stat-value {
                color: #f3f4f6;
            }

            .cde-stat-sub {
                font-size: 0.75rem;
                margin-top: 0.25rem;
                font-weight: 500;
            }

            .cde-stat-icon-wrap {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 0.75rem;
                flex-shrink: 0;
                transition: transform 300ms;
            }

            .cde-stat-card:hover .cde-stat-icon-wrap {
                transform: scale(1.1);
            }

            .cde-stat-card.primary .cde-stat-icon-wrap {
                background: rgba(255, 255, 255, .15);
                backdrop-filter: blur(8px);
            }

            /* Breadcrumb */
            .cde-breadcrumb {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.875rem 1.25rem;
                border-radius: 0.75rem;
                background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
                border: 1px solid #e0f2fe;
                margin-bottom: 1.25rem;
            }

            .dark .cde-breadcrumb {
                background: linear-gradient(135deg, rgba(14, 116, 144, .08) 0%, rgba(31, 41, 55, .5) 100%);
                border-color: rgba(14, 116, 144, .15);
            }

            .cde-bc-icon {
                width: 1.5rem;
                height: 1.5rem;
                color: #0891b2;
            }

            .cde-bc-link {
                font-size: 0.8125rem;
                font-weight: 600;
                color: #0891b2;
                text-decoration: none;
                padding: 0.25rem 0.5rem;
                border-radius: 0.375rem;
                transition: all 150ms;
                cursor: pointer;
                border: none;
                background: none;
            }

            .cde-bc-link:hover {
                background: rgba(8, 145, 178, .08);
                color: #0e7490;
            }

            .cde-bc-sep {
                color: #94a3b8;
                width: 1rem;
                height: 1rem;
            }

            .cde-bc-current {
                font-size: 0.8125rem;
                font-weight: 700;
                color: #1e293b;
                padding: 0.25rem 0.5rem;
                background: white;
                border-radius: 0.375rem;
                border: 1px solid #e2e8f0;
            }

            .dark .cde-bc-current {
                color: #f1f5f9;
                background: rgba(255, 255, 255, .05);
                border-color: rgba(255, 255, 255, .1);
            }

            /* Folders Section */
            .cde-folders-section {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 1rem;
                padding: 1.25rem 1.5rem;
                margin-bottom: 1.25rem;
            }

            .dark .cde-folders-section {
                background: rgba(31, 41, 55, .7);
                border-color: rgba(255, 255, 255, .08);
            }

            .cde-section-title {
                font-size: 0.8125rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #374151;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 1rem;
            }

            .dark .cde-section-title {
                color: #e5e7eb;
            }

            .cde-section-title-icon {
                width: 1.125rem;
                height: 1.125rem;
                color: #6366f1;
            }

            .cde-section-title-badge {
                font-size: 0.625rem;
                font-weight: 700;
                background: #eef2ff;
                color: #6366f1;
                padding: 0.125rem 0.5rem;
                border-radius: 9999px;
                margin-left: 0.25rem;
            }

            .dark .cde-section-title-badge {
                background: rgba(99, 102, 241, .15);
            }

            .cde-folders-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 0.75rem;
            }

            .cde-folder-card {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.625rem;
                padding: 1.125rem 0.75rem 1rem;
                border-radius: 0.875rem;
                border: 1.5px solid #e5e7eb;
                background: #fafbfc;
                cursor: pointer;
                transition: all 200ms cubic-bezier(.4, 0, .2, 1);
                text-align: center;
            }

            .dark .cde-folder-card {
                background: rgba(255, 255, 255, .03);
                border-color: rgba(255, 255, 255, .08);
            }

            .cde-folder-card:hover {
                border-color: #818cf8;
                background: #eef2ff;
                transform: translateY(-3px);
                box-shadow: 0 8px 20px -4px rgba(99, 102, 241, .15);
            }

            .dark .cde-folder-card:hover {
                border-color: rgba(129, 140, 248, .4);
                background: rgba(99, 102, 241, .08);
            }

            .cde-folder-icon-wrap {
                width: 3rem;
                height: 3rem;
                border-radius: 0.75rem;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
                transition: all 200ms;
            }

            .dark .cde-folder-icon-wrap {
                background: linear-gradient(135deg, rgba(59, 130, 246, .15) 0%, rgba(99, 102, 241, .15) 100%);
            }

            .cde-folder-card:hover .cde-folder-icon-wrap {
                background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
                transform: scale(1.08);
            }

            .cde-folder-icon {
                width: 1.5rem;
                height: 1.5rem;
                color: #3b82f6;
                transition: color 200ms;
            }

            .cde-folder-card:hover .cde-folder-icon {
                color: white;
            }

            .cde-folder-name {
                font-size: 0.75rem;
                font-weight: 600;
                color: #374151;
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                transition: color 200ms;
            }

            .dark .cde-folder-name {
                color: #d1d5db;
            }

            .cde-folder-card:hover .cde-folder-name {
                color: #4338ca;
            }

            .dark .cde-folder-card:hover .cde-folder-name {
                color: #a5b4fc;
            }

            .cde-folder-meta {
                display: flex;
                align-items: center;
                gap: 0.375rem;
            }

            .cde-folder-badge {
                font-size: 0.5625rem;
                font-weight: 700;
                padding: 0.125rem 0.375rem;
                border-radius: 0.25rem;
                background: #f3f4f6;
                color: #6b7280;
                font-family: ui-monospace, monospace;
                letter-spacing: 0.02em;
            }

            .dark .cde-folder-badge {
                background: rgba(255, 255, 255, .06);
                color: #9ca3af;
            }

            .cde-folder-docs {
                font-size: 0.625rem;
                color: #9ca3af;
                font-weight: 500;
            }

            /* Documents Table Section */
            .cde-docs-section {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 1rem;
                overflow: hidden;
            }

            .dark .cde-docs-section {
                background: rgba(31, 41, 55, .7);
                border-color: rgba(255, 255, 255, .08);
            }

            .cde-docs-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1rem 1.5rem;
                border-bottom: 1px solid #f3f4f6;
            }

            .dark .cde-docs-header {
                border-color: rgba(255, 255, 255, .06);
            }

            .cde-docs-title {
                font-size: 0.8125rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #374151;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .dark .cde-docs-title {
                color: #e5e7eb;
            }

            .cde-docs-title-icon {
                width: 1.125rem;
                height: 1.125rem;
                color: #0ea5e9;
            }
        </style>
    @endpush

    {{-- ── Stats Cards ─────────────────────────────────────────────────── --}}
    @php $stats = $this->getStats(); @endphp
    <div class="cde-stats-grid">
        @foreach($stats as $i => $stat)
            <div class="cde-stat-card {{ $i === 0 ? 'primary' : 'secondary' }}">
                <div
                    style="display: flex; align-items: flex-start; justify-content: space-between; position: relative; z-index: 1;">
                    <div>
                        <div class="cde-stat-label">{{ $stat['label'] }}</div>
                        <div class="cde-stat-value">{{ $stat['value'] }}</div>
                        @if(!empty($stat['sub']))
                            @php
                                $subColor = match ($stat['sub_type'] ?? 'neutral') {
                                    'success' => $i === 0 ? 'rgba(255,255,255,.75)' : '#10b981',
                                    'danger' => $i === 0 ? '#fca5a5' : '#ef4444',
                                    'info' => $i === 0 ? 'rgba(255,255,255,.75)' : '#0ea5e9',
                                    default => $i === 0 ? 'rgba(255,255,255,.6)' : '#6b7280',
                                };
                            @endphp
                            <div class="cde-stat-sub" style="color: {{ $subColor }}">{{ $stat['sub'] }}</div>
                        @endif
                    </div>
                    @if(!empty($stat['icon_svg']))
                        <div class="cde-stat-icon-wrap"
                            style="{{ $i !== 0 ? 'background: ' . ($stat['icon_bg'] ?? '#f0f9ff') : '' }}">
                            {!! $stat['icon_svg'] !!}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Breadcrumb / Location Bar ────────────────────────────────────── --}}
    <div class="cde-breadcrumb">
        <svg class="cde-bc-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
        </svg>
        <button wire:click="navigateToFolder(null)" class="cde-bc-link">Root</button>
        @if($this->currentFolderId)
            @php
                $breadcrumbFolders = [];
                $f = \App\Models\CdeFolder::find($this->currentFolderId);
                while ($f) {
                    array_unshift($breadcrumbFolders, $f);
                    $f = $f->parent;
                }
            @endphp
            @foreach($breadcrumbFolders as $bf)
                <svg class="cde-bc-sep" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                        clip-rule="evenodd" />
                </svg>
                @if($loop->last)
                    <span class="cde-bc-current">{{ $bf->name }}</span>
                @else
                    <button wire:click="navigateToFolder({{ $bf->id }})" class="cde-bc-link">{{ $bf->name }}</button>
                @endif
            @endforeach
        @endif
    </div>

    {{-- ── Subfolders Grid ──────────────────────────────────────────────── --}}
    @php $subfolders = $this->getSubfolders(); @endphp
    @if($subfolders->isNotEmpty())
        <div class="cde-folders-section">
            <div class="cde-section-title">
                <svg class="cde-section-title-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                </svg>
                Folders
                <span class="cde-section-title-badge">{{ $subfolders->count() }}</span>
            </div>
            <div class="cde-folders-grid">
                @foreach($subfolders as $sf)
                    <button wire:click="navigateToFolder({{ $sf->id }})" class="cde-folder-card">
                        <div class="cde-folder-icon-wrap">
                            <svg class="cde-folder-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z" />
                            </svg>
                        </div>
                        <span class="cde-folder-name">{{ $sf->name }}</span>
                        <div class="cde-folder-meta">
                            @if($sf->suitability_code)
                                <span class="cde-folder-badge">{{ $sf->suitability_code }}</span>
                            @endif
                            <span class="cde-folder-docs">{{ $sf->documents()->count() }} docs</span>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Documents Table ───────────────────────────────────────────────── --}}
    <div class="cde-docs-section">
        <div class="cde-docs-header">
            <div class="cde-docs-title">
                <svg class="cde-docs-title-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Documents in {{ $this->folderPath }}
            </div>
        </div>
        {{ $this->table }}
    </div>

    {{-- ── Transmittals Section ─────────────────────────────────────────── --}}
    @php $transmittals = $this->getTransmittals(); @endphp
    @if($transmittals->isNotEmpty())
        <div style="margin-top: 1.25rem;">
            <x-filament::section icon="heroicon-o-paper-airplane" icon-color="success" collapsible>
                <x-slot name="heading">Transmittals</x-slot>
                <x-slot name="description">Document packages sent to external parties.</x-slot>

                <div style="display: grid; gap: 10px;">
                    @foreach($transmittals as $tr)
                        @php
                            $statusStyle = match ($tr->status) {
                                'sent' => 'background:rgba(59,130,246,0.1);color:#3b82f6;',
                                'acknowledged' => 'background:rgba(16,185,129,0.1);color:#10b981;',
                                default => 'background:rgba(107,114,128,0.1);color:#6b7280;',
                            };
                            $purposeLabel = \App\Models\Transmittal::$purposes[$tr->purpose] ?? $tr->purpose;
                        @endphp
                        <div
                            style="display:flex; align-items:center; gap:16px; padding:14px 18px; border-radius:10px; background:var(--gray-50, #f9fafb); border:1px solid var(--gray-200, #e5e7eb);">
                            <div
                                style="flex-shrink:0; width:40px; height:40px; border-radius:10px; background:rgba(139,92,246,0.08); display:flex; align-items:center; justify-content:center;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="#8b5cf6" style="width:20px;height:20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                </svg>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:600; font-size:14px;">{{ $tr->transmittal_number }} — {{ $tr->subject }}
                                </div>
                                <div style="font-size:12px; color:var(--gray-400,#9ca3af); margin-top:3px;">
                                    To: {{ $tr->to_organization }}{{ $tr->to_contact ? " ({$tr->to_contact})" : '' }}
                                    · {{ $purposeLabel }}
                                    · {{ $tr->items->count() }} doc(s)
                                    @if($tr->sender) · By: {{ $tr->sender->name }} @endif
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <span
                                    style="padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; text-transform:uppercase; {{ $statusStyle }}">
                                    {{ \App\Models\Transmittal::$statuses[$tr->status] ?? $tr->status }}
                                </span>
                                <div style="font-size:11px; color:var(--gray-400,#9ca3af); margin-top:4px;">
                                    {{ $tr->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        </div>
    @endif

</x-filament-panels::page>