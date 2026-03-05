<x-filament-panels::page>

    @push('styles')
        <style>
            /* ═══════════ CDE Page — Redesigned ═══════════ */

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

            /* Documents Table */
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

            /* Share Modal Overlay */
            .cde-share-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .45);
                backdrop-filter: blur(3px);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .cde-share-card {
                background: white;
                width: 100%;
                max-width: 560px;
                border-radius: 16px;
                box-shadow: 0 25px 60px -10px rgba(0, 0, 0, .25);
                overflow: hidden;
            }

            .dark .cde-share-card {
                background: #1f2937;
            }

            .cde-share-header {
                padding: 24px 28px;
                border-bottom: 1px solid #f0f0f5;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .dark .cde-share-header {
                border-color: rgba(255, 255, 255, .08);
            }

            .cde-share-body {
                padding: 24px 28px;
                max-height: 60vh;
                overflow-y: auto;
            }

            .cde-share-person {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .dark .cde-share-person {
                border-color: rgba(255, 255, 255, .06);
            }

            .cde-share-person:last-child {
                border-bottom: none;
            }

            .cde-share-avatar {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: linear-gradient(135deg, #818cf8, #6366f1);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 700;
                font-size: 14px;
                flex-shrink: 0;
            }

            .cde-share-name {
                font-size: 14px;
                font-weight: 600;
                color: #1e293b;
            }

            .dark .cde-share-name {
                color: #e5e7eb;
            }

            .cde-share-email {
                font-size: 12px;
                color: #9ca3af;
            }

            .cde-share-badge {
                padding: 3px 10px;
                border-radius: 99px;
                font-size: 11px;
                font-weight: 600;
                text-transform: capitalize;
            }

            .cde-share-badge-view {
                background: #dbeafe;
                color: #2563eb;
            }

            .cde-share-badge-download {
                background: #dcfce7;
                color: #16a34a;
            }

            .cde-share-badge-edit {
                background: #fef3c7;
                color: #d97706;
            }

            .cde-share-revoke {
                background: none;
                border: none;
                color: #ef4444;
                cursor: pointer;
                font-size: 12px;
                font-weight: 600;
                padding: 4px 8px;
                border-radius: 6px;
                transition: background .15s;
            }

            .cde-share-revoke:hover {
                background: #fee2e2;
            }

            .cde-link-row {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px 0;
                border-bottom: 1px solid #f3f4f6;
            }

            .dark .cde-link-row {
                border-color: rgba(255, 255, 255, .06);
            }

            .cde-link-token {
                font-family: ui-monospace, monospace;
                font-size: 12px;
                background: #f1f5f9;
                padding: 6px 10px;
                border-radius: 6px;
                flex: 1;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: #475569;
            }

            .dark .cde-link-token {
                background: rgba(255, 255, 255, .05);
                color: #94a3b8;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('copy-to-clipboard', ({ text }) => {
                    navigator.clipboard.writeText(text).then(() => {
                        // Handled by Filament notification
                    }).catch(() => {
                        prompt('Copy this link:', text);
                    });
                });
            });
        </script>
    @endpush

    {{-- ── Stats Cards ─────────────────────────────────────────────────── --}}
    @php
        $mappedStats = collect($this->getStats())->map(function ($stat, $i) {
            $stat['primary'] = $i === 0;
            return $stat;
        })->toArray();
    @endphp
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $mappedStats])

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

    {{-- ═══════════ SHARING PERMISSIONS MODAL ═══════════ --}}
    @if($showShareModal && $sharingDocumentId)
        @php $shareDoc = \App\Models\CdeDocument::find($sharingDocumentId); @endphp
        @if($shareDoc)
            <div class="cde-share-overlay" wire:click.self="$set('showShareModal', false)">
                <div class="cde-share-card" wire:click.stop>
                    {{-- Header --}}
                    <div class="cde-share-header">
                        <div>
                            <h3 style="margin:0;font-size:17px;font-weight:800;color:#0f172a;letter-spacing:-.02em;">
                                Sharing & Permissions
                            </h3>
                            <p style="margin:4px 0 0;font-size:13px;color:#64748b;">
                                {{ $shareDoc->document_number }} — {{ $shareDoc->title }}
                            </p>
                        </div>
                        <button wire:click="$set('showShareModal', false)"
                            style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:8px;">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="cde-share-body">
                        {{-- Quick Actions --}}
                        <div style="display:flex;gap:8px;margin-bottom:20px;">
                            <button wire:click="generateShareLink({{ $shareDoc->id }}, 'view', 7)"
                                style="flex:1;padding:10px;border-radius:8px;border:1px solid #e2e8f0;background:#f8fafc;cursor:pointer;font-size:13px;font-weight:600;color:#475569;display:flex;align-items:center;justify-content:center;gap:6px;">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                View Link
                            </button>
                            <button wire:click="generateShareLink({{ $shareDoc->id }}, 'download', 7)"
                                style="flex:1;padding:10px;border-radius:8px;border:none;background:linear-gradient(135deg,#4f46e5,#6366f1);cursor:pointer;font-size:13px;font-weight:600;color:white;display:flex;align-items:center;justify-content:center;gap:6px;">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.86-2.318a4.5 4.5 0 00-1.242-7.244l-4.5-4.5a4.5 4.5 0 00-6.364 6.364L4.343 8.04" />
                                </svg>
                                Download Link
                            </button>
                        </div>

                        {{-- Current Shares --}}
                        @php $shares = $this->getDocumentShares($shareDoc->id); @endphp

                        @if($shares->isNotEmpty())
                            <div style="margin-bottom:8px;">
                                <span
                                    style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;">
                                    People with access
                                </span>
                            </div>

                            @foreach($shares as $share)
                                @if($share->shared_with)
                                    <div class="cde-share-person">
                                        <div class="cde-share-avatar">
                                            {{ strtoupper(substr($share->sharedWith->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div style="flex:1;min-width:0;">
                                            <div class="cde-share-name">{{ $share->sharedWith->name ?? 'Unknown' }}</div>
                                            <div class="cde-share-email">{{ $share->sharedWith->email ?? '' }}</div>
                                        </div>
                                        <span class="cde-share-badge cde-share-badge-{{ $share->permission }}">
                                            {{ $share->permission }}
                                        </span>
                                        <button wire:click="revokeShare({{ $share->id }})" class="cde-share-revoke"
                                            title="Revoke">✕</button>
                                    </div>
                                @endif

                                @if($share->share_token)
                                    <div class="cde-link-row">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2"
                                            style="flex-shrink:0;">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.86-2.318a4.5 4.5 0 00-1.242-7.244l-4.5-4.5a4.5 4.5 0 00-6.364 6.364L4.343 8.04" />
                                        </svg>
                                        <span class="cde-link-token">{{ config('app.url') }}/share/doc/{{ $share->share_token }}</span>
                                        <span class="cde-share-badge cde-share-badge-{{ $share->permission }}"
                                            style="flex-shrink:0;">{{ $share->permission }}</span>
                                        @if($share->expires_at)
                                            <span style="font-size:11px;color:#9ca3af;white-space:nowrap;flex-shrink:0;">
                                                {{ $share->expires_at->diffForHumans() }}
                                            </span>
                                        @endif
                                        <button wire:click="revokeShare({{ $share->id }})" class="cde-share-revoke"
                                            title="Revoke">✕</button>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div style="text-align:center;padding:24px;color:#9ca3af;font-size:13px;">
                                <div style="font-size:24px;margin-bottom:8px;">🔒</div>
                                No active shares. Use the buttons above to create a share link.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif

</x-filament-panels::page>