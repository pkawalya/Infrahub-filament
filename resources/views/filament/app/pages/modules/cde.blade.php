<x-filament-panels::page>

    @push('styles')
        <style>
            /* ═══════════ CDE — OneDrive / Asite Inspired ═══════════ */
            :root {
                --cde-bg: #ffffff;
                --cde-surface: #f8fafc;
                --cde-border: #e2e8f0;
                --cde-text: #1e293b;
                --cde-muted: #64748b;
                --cde-subtle: #94a3b8;
                --cde-accent: #4f46e5;
                --cde-accent-bg: #eef2ff;
                --cde-hover: #f1f5f9;
                --cde-radius: 8px;
            }
            .dark {
                --cde-bg: #111827;
                --cde-surface: rgba(30, 41, 59, 0.6);
                --cde-border: rgba(255,255,255,0.08);
                --cde-text: #f1f5f9;
                --cde-muted: #94a3b8;
                --cde-subtle: #64748b;
                --cde-accent: #818cf8;
                --cde-accent-bg: rgba(99,102,241,0.1);
                --cde-hover: rgba(255,255,255,0.04);
            }

            /* ── Status Pipeline ─────────────────────── */
            .cde-pipeline {
                display: flex;
                gap: 2px;
                background: var(--cde-surface);
                border: 1px solid var(--cde-border);
                border-radius: var(--cde-radius);
                padding: 4px;
                margin-bottom: 16px;
                overflow-x: auto;
            }
            .cde-pipe-item {
                flex: 1;
                min-width: 80px;
                text-align: center;
                padding: 8px 6px;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 600;
                transition: all .15s;
                cursor: default;
                position: relative;
            }
            .cde-pipe-count {
                display: block;
                font-size: 18px;
                font-weight: 800;
                line-height: 1.2;
                letter-spacing: -0.02em;
            }
            .cde-pipe-label {
                display: block;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-top: 2px;
                opacity: 0.7;
            }

            /* ── Command Bar (OneDrive-style) ────────── */
            .cde-cmdbar {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 8px 12px;
                background: var(--cde-bg);
                border: 1px solid var(--cde-border);
                border-radius: var(--cde-radius);
                margin-bottom: 12px;
            }
            .cde-cmdbar-sep {
                width: 1px;
                height: 24px;
                background: var(--cde-border);
                margin: 0 4px;
            }
            .cde-cmd-btn {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 6px 12px;
                font-size: 13px;
                font-weight: 500;
                color: var(--cde-text);
                background: none;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: background .12s;
                white-space: nowrap;
            }
            .cde-cmd-btn:hover {
                background: var(--cde-hover);
            }
            .cde-cmd-btn svg {
                width: 16px;
                height: 16px;
                color: var(--cde-muted);
            }
            .cde-cmd-btn.active {
                background: var(--cde-accent-bg);
                color: var(--cde-accent);
            }
            .cde-cmd-btn.active svg {
                color: var(--cde-accent);
            }
            .cde-cmd-spacer { flex: 1; }

            /* ── Breadcrumb (compact) ──────────────────── */
            .cde-path {
                display: flex;
                align-items: center;
                gap: 4px;
                padding: 6px 0;
                margin-bottom: 12px;
                font-size: 13px;
                overflow-x: auto;
            }
            .cde-path-item {
                color: var(--cde-accent);
                font-weight: 500;
                background: none;
                border: none;
                cursor: pointer;
                padding: 2px 6px;
                border-radius: 4px;
                transition: background .12s;
                white-space: nowrap;
            }
            .cde-path-item:hover {
                background: var(--cde-accent-bg);
            }
            .cde-path-current {
                color: var(--cde-text);
                font-weight: 700;
                padding: 2px 6px;
                white-space: nowrap;
            }
            .cde-path-sep {
                color: var(--cde-subtle);
                font-size: 12px;
            }

            /* ── Folder Grid ──────────────────────────── */
            .cde-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 10px;
                margin-bottom: 16px;
            }
            .cde-grid.list-mode {
                grid-template-columns: 1fr;
                gap: 2px;
            }
            .cde-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 14px;
                background: var(--cde-bg);
                border: 1px solid var(--cde-border);
                border-radius: var(--cde-radius);
                cursor: pointer;
                transition: all .12s;
                position: relative;
            }
            .cde-item:hover {
                background: var(--cde-hover);
                border-color: var(--cde-accent);
                box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            }
            .cde-grid.list-mode .cde-item {
                border-radius: 4px;
                padding: 10px 14px;
                border-color: transparent;
                border-bottom-color: var(--cde-border);
            }
            .cde-grid.list-mode .cde-item:hover {
                background: var(--cde-hover);
                border-color: transparent;
                border-bottom-color: var(--cde-border);
                box-shadow: none;
            }

            /* Folder icon */
            .cde-ficon {
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .cde-ficon svg {
                width: 32px;
                height: 32px;
            }
            .cde-ficon-yellow { color: #f59e0b; }
            .cde-ficon-blue { color: #3b82f6; }

            /* Item details */
            .cde-item-info {
                flex: 1;
                min-width: 0;
            }
            .cde-item-name {
                font-size: 13px;
                font-weight: 600;
                color: var(--cde-text);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .cde-item-meta {
                font-size: 11px;
                color: var(--cde-muted);
                margin-top: 1px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .cde-item-badge {
                font-size: 9px;
                font-weight: 700;
                padding: 1px 5px;
                border-radius: 3px;
                background: var(--cde-accent-bg);
                color: var(--cde-accent);
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }
            .cde-item-shared {
                color: var(--cde-subtle);
                display: flex;
                align-items: center;
            }
            .cde-item-shared svg {
                width: 14px;
                height: 14px;
            }

            /* ── Documents Section ────────────────────── */
            .cde-docs-wrap {
                background: var(--cde-bg);
                border: 1px solid var(--cde-border);
                border-radius: var(--cde-radius);
                overflow: hidden;
                margin-bottom: 16px;
            }
            .cde-docs-toolbar {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 16px;
                border-bottom: 1px solid var(--cde-border);
                font-size: 13px;
                font-weight: 600;
                color: var(--cde-text);
            }
            .cde-docs-toolbar svg {
                width: 16px;
                height: 16px;
                color: var(--cde-muted);
            }
            .cde-docs-toolbar .count {
                font-size: 11px;
                font-weight: 700;
                background: var(--cde-accent-bg);
                color: var(--cde-accent);
                padding: 1px 8px;
                border-radius: 99px;
            }

            /* ── Collapsible Sections ────────────────── */
            .cde-section-wrap {
                margin-bottom: 12px;
            }

            /* ── Share Overlay ────────────────────────── */
            .cde-share-overlay {
                position: fixed; inset: 0; background: rgba(0,0,0,.45); backdrop-filter: blur(3px);
                z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 20px;
            }
            .cde-share-card {
                background: var(--cde-bg); width: 100%; max-width: 560px; border-radius: 12px;
                box-shadow: 0 25px 60px -10px rgba(0,0,0,.25); overflow: hidden;
            }
            .cde-share-header {
                padding: 20px 24px; border-bottom: 1px solid var(--cde-border);
                display: flex; justify-content: space-between; align-items: center;
            }
            .cde-share-body { padding: 20px 24px; max-height: 60vh; overflow-y: auto; }
            .cde-share-person {
                display: flex; align-items: center; gap: 12px; padding: 10px 0;
                border-bottom: 1px solid var(--cde-border);
            }
            .cde-share-person:last-child { border-bottom: none; }
            .cde-share-avatar {
                width: 32px; height: 32px; border-radius: 50%;
                background: linear-gradient(135deg, #818cf8, #6366f1);
                display: flex; align-items: center; justify-content: center;
                color: white; font-weight: 700; font-size: 12px; flex-shrink: 0;
            }
            .cde-share-name { font-size: 13px; font-weight: 600; color: var(--cde-text); }
            .cde-share-email { font-size: 11px; color: var(--cde-muted); }
            .cde-share-badge {
                padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 600; text-transform: capitalize;
            }
            .cde-share-badge-view { background: #dbeafe; color: #2563eb; }
            .cde-share-badge-download { background: #dcfce7; color: #16a34a; }
            .cde-share-badge-edit { background: #fef3c7; color: #d97706; }
            .cde-share-revoke {
                background: none; border: none; color: #ef4444; cursor: pointer;
                font-size: 11px; font-weight: 600; padding: 4px 6px; border-radius: 4px;
            }
            .cde-share-revoke:hover { background: #fee2e2; }
            .cde-link-row {
                display: flex; align-items: center; gap: 8px; padding: 10px 0;
                border-bottom: 1px solid var(--cde-border);
            }
            .cde-link-token {
                font-family: ui-monospace, monospace; font-size: 11px;
                background: var(--cde-surface); padding: 4px 8px; border-radius: 4px;
                flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
                color: var(--cde-muted);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('copy-to-clipboard', ({ text }) => {
                    navigator.clipboard.writeText(text).catch(() => prompt('Copy this link:', text));
                });
            });
        </script>
    @endpush

    {{-- ── ISO 19650 Status Pipeline ──────────────────────────────────── --}}
    @php
        $pipeline = $this->getStatusPipeline();
    @endphp
    <div class="cde-pipeline">
        @foreach($pipeline as $stage)
            <div class="cde-pipe-item" style="background: {{ $stage['bg'] }}; color: {{ $stage['color'] }};">
                <span class="cde-pipe-count">{{ $stage['count'] }}</span>
                <span class="cde-pipe-label">{{ $stage['label'] }}</span>
            </div>
        @endforeach
    </div>

    {{-- ── Document Health Alerts ──────────────────────────────────────── --}}
    @php $docHealth = $this->getDocumentHealth(); @endphp
    @if($docHealth['needs_revision'] > 0 || $docHealth['stale'] > 0)
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
            @if($docHealth['needs_revision'] > 0)
                <div style="display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:6px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:12px;">
                    <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    <strong>{{ $docHealth['needs_revision'] }}</strong> doc{{ $docHealth['needs_revision'] > 1 ? 's' : '' }} need revision
                </div>
            @endif
            @if($docHealth['review_backlog'] > 0)
                <div style="display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:6px;background:#fffbeb;border:1px solid #fde68a;color:#92400e;font-size:12px;">
                    <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <strong>{{ $docHealth['review_backlog'] }}</strong> awaiting review
                </div>
            @endif
            @if($docHealth['stale'] > 0)
                <div style="display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:6px;background:#f1f5f9;border:1px solid #e2e8f0;color:#475569;font-size:12px;">
                    <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286z"/></svg>
                    <strong>{{ $docHealth['stale'] }}</strong> stale (30+ days)
                </div>
            @endif
        </div>
    @endif

    {{-- ══ Tab Switcher: Files | Required Submissions ════════════════════ --}}
    @php $subStats = $this->getSubmissionStats(); $totalSubs = array_sum(array_column($subStats, 'total')); @endphp
    <div style="display:flex;gap:4px;border-bottom:2px solid var(--cde-border);margin-bottom:14px;overflow-x:auto;">
        <button wire:click="$set('activeDocTab', 'files')"
            style="padding:10px 20px;font-size:13px;font-weight:600;border:none;cursor:pointer;border-radius:8px 8px 0 0;transition:all .2s;display:inline-flex;align-items:center;gap:6px;white-space:nowrap;{{ $this->activeDocTab === 'files' ? 'background:var(--cde-accent,#4f46e5);color:white;' : 'background:transparent;color:var(--cde-muted);' }}">
            <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
            Files & Documents
        </button>
        <button wire:click="$set('activeDocTab', 'submissions')"
            style="padding:10px 20px;font-size:13px;font-weight:600;border:none;cursor:pointer;border-radius:8px 8px 0 0;transition:all .2s;display:inline-flex;align-items:center;gap:6px;white-space:nowrap;{{ $this->activeDocTab === 'submissions' ? 'background:var(--cde-accent,#4f46e5);color:white;' : 'background:transparent;color:var(--cde-muted);' }}">
            <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75"/></svg>
            Required Submissions
            @if($totalSubs > 0)
                <span style="font-size:10px;padding:2px 7px;border-radius:99px;margin-left:2px;{{ $this->activeDocTab === 'submissions' ? 'background:rgba(255,255,255,0.2);color:white;' : 'background:rgba(99,102,241,0.12);color:#6366f1;' }}">{{ $totalSubs }}</span>
            @endif
        </button>
    </div>

    @if($this->activeDocTab === 'files')
    {{-- ══ Submission Tracking Matrix ════════════════════════════════════ --}}
    @php $sm = $this->getSubmissionMatrix(); @endphp
    @if($sm['total_docs'] > 0 && count($sm['matrix']) > 1)
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                <span style="display:flex;align-items:center;gap:6px;">
                    <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/></svg>
                    Submission Tracker by Discipline
                    <span style="background:{{ $sm['overall_completion'] >= 80 ? '#dcfce7' : ($sm['overall_completion'] >= 40 ? '#fef3c7' : '#fef2f2') }};color:{{ $sm['overall_completion'] >= 80 ? '#16a34a' : ($sm['overall_completion'] >= 40 ? '#d97706' : '#ef4444') }};font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;">
                        {{ $sm['overall_completion'] }}% Complete
                    </span>
                </span>
            </x-slot>
            <x-slot name="description">Track document submissions per discipline across status gates</x-slot>

            <div style="overflow-x:auto;">
                <table style="width:100%;font-size:12px;border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:2px solid #e2e8f0;">
                            <th style="text-align:left;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;">Discipline</th>
                            @foreach($sm['levels'] as $key => $level)
                                <th style="text-align:center;padding:8px 6px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.03em;color:{{ $level['color'] }};min-width:45px;">{{ $level['label'] }}</th>
                            @endforeach
                            <th style="text-align:center;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;min-width:100px;">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sm['matrix'] as $row)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:8px 10px;font-weight:600;color:#334155;white-space:nowrap;">
                                    {{ $row['discipline'] }}
                                    <span style="font-size:10px;font-weight:400;color:#94a3b8;margin-left:4px;">{{ $row['total'] }}</span>
                                </td>
                                @foreach(array_keys($sm['levels']) as $status)
                                    <td style="text-align:center;padding:8px 6px;">
                                        @if($row[$status] > 0)
                                            <span style="display:inline-block;min-width:22px;padding:2px 6px;border-radius:4px;font-weight:700;font-size:11px;background:{{ $sm['levels'][$status]['color'] }}15;color:{{ $sm['levels'][$status]['color'] }};">
                                                {{ $row[$status] }}
                                            </span>
                                        @else
                                            <span style="color:#e2e8f0;">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td style="padding:8px 10px;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <div style="flex:1;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                                            <div style="width:{{ $row['completion_pct'] }}%;height:100%;background:{{ $row['completion_pct'] >= 80 ? '#10b981' : ($row['completion_pct'] >= 40 ? '#d97706' : '#ef4444') }};border-radius:3px;transition:width .3s;"></div>
                                        </div>
                                        <span style="font-size:10px;font-weight:700;color:{{ $row['completion_pct'] >= 80 ? '#10b981' : ($row['completion_pct'] >= 40 ? '#d97706' : '#ef4444') }};min-width:28px;text-align:right;">{{ $row['completion_pct'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        {{-- Totals row --}}
                        <tr style="border-top:2px solid #e2e8f0;background:#f8fafc;">
                            <td style="padding:8px 10px;font-weight:700;color:#1e293b;">
                                Total <span style="font-size:10px;font-weight:400;color:#94a3b8;">{{ $sm['total_docs'] }}</span>
                            </td>
                            @foreach(array_keys($sm['levels']) as $status)
                                <td style="text-align:center;padding:8px 6px;font-weight:700;font-size:12px;color:#334155;">
                                    {{ $sm['totals'][$status] > 0 ? $sm['totals'][$status] : '—' }}
                                </td>
                            @endforeach
                            <td style="padding:8px 10px;">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <div style="flex:1;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                                        <div style="width:{{ $sm['overall_completion'] }}%;height:100%;background:#6366f1;border-radius:3px;"></div>
                                    </div>
                                    <span style="font-size:10px;font-weight:700;color:#6366f1;min-width:28px;text-align:right;">{{ $sm['overall_completion'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
    <div class="cde-cmdbar" x-data="{ viewMode: 'grid' }">
        {{-- Filament header actions render here --}}
        <div style="display:flex;align-items:center;gap:4px;">
            @foreach ($this->getCachedHeaderActions() as $action)
                {{ $action }}
            @endforeach
        </div>
        <div class="cde-cmd-spacer"></div>
        <div class="cde-cmdbar-sep"></div>
        {{-- View Toggle --}}
        <button class="cde-cmd-btn" :class="viewMode === 'grid' ? 'active' : ''" @click="viewMode = 'grid'" title="Grid view">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
        </button>
        <button class="cde-cmd-btn" :class="viewMode === 'list' ? 'active' : ''" @click="viewMode = 'list'" title="List view">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 5.25h16.5m-16.5-10.5h16.5"/></svg>
        </button>

        {{-- ── Breadcrumb Path (inside command bar) ──────── --}}
        <div class="cde-cmdbar-sep"></div>
        <div class="cde-path" style="margin:0; padding:0;">
            <button wire:click="navigateToFolder(null)" class="cde-path-item">
                <svg style="width:14px;height:14px;display:inline;vertical-align:-2px;margin-right:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Root
            </button>
            @if($this->currentFolderId)
                @php
                    $breadcrumbFolders = [];
                    $f = \App\Models\CdeFolder::find($this->currentFolderId);
                    while ($f) { array_unshift($breadcrumbFolders, $f); $f = $f->parent; }
                @endphp
                @foreach($breadcrumbFolders as $bf)
                    <span class="cde-path-sep">›</span>
                    @if($loop->last)
                        <span class="cde-path-current">{{ $bf->name }}</span>
                    @else
                        <button wire:click="navigateToFolder({{ $bf->id }})" class="cde-path-item">{{ $bf->name }}</button>
                    @endif
                @endforeach
            @endif
        </div>
    </div>

    {{-- ── Subfolders Grid ──────────────────────────────────────────── --}}
    @php $subfolders = $this->getSubfolders(); @endphp
    @if($subfolders->isNotEmpty())
        <div class="cde-grid" x-data :class="viewMode === 'list' ? 'list-mode' : ''">
            @foreach($subfolders as $sf)
                <button wire:click="navigateToFolder({{ $sf->id }})" class="cde-item">
                    <div class="cde-ficon cde-ficon-yellow">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z"/></svg>
                    </div>
                    <div class="cde-item-info">
                        <div class="cde-item-name" title="{{ $sf->name }}">{{ $sf->name }}</div>
                        <div class="cde-item-meta">
                            @if($sf->suitability_code)
                                <span class="cde-item-badge">{{ $sf->suitability_code }}</span>
                            @endif
                            <span>{{ $sf->documents()->count() }} items</span>
                        </div>
                    </div>
                    {{-- Arrow indicator --}}
                    <svg style="width:16px;height:16px;color:var(--cde-subtle);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
            @endforeach
        </div>
    @endif

    {{-- ── Documents Table ─────────────────────────────────────────── --}}
    <div class="cde-docs-wrap">
        <div class="cde-docs-toolbar">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            Documents
            @php $docCount = $this->record->documents()->where('cde_folder_id', $this->currentFolderId)->count(); @endphp
            <span class="count">{{ $docCount }}</span>
            <span style="flex:1;"></span>
            <span style="font-size:11px;font-weight:400;color:var(--cde-muted);">{{ $this->folderPath }}</span>
        </div>
        {{ $this->table }}
    </div>

    {{-- ── Transmittals ───────────────────────────────────────────── --}}
    @php $transmittals = $this->getTransmittals(); @endphp
    @if($transmittals->isNotEmpty())
        <div class="cde-section-wrap">
            <x-filament::section icon="heroicon-o-paper-airplane" icon-color="success" collapsible collapsed>
                <x-slot name="heading">Transmittals</x-slot>
                <x-slot name="description">{{ $transmittals->count() }} packages</x-slot>

                <div style="display: grid; gap: 8px;">
                    @foreach($transmittals as $tr)
                        @php
                            $statusStyle = match ($tr->status) {
                                'sent' => 'background:rgba(59,130,246,0.1);color:#3b82f6;',
                                'acknowledged' => 'background:rgba(16,185,129,0.1);color:#10b981;',
                                default => 'background:rgba(107,114,128,0.1);color:#6b7280;',
                            };
                            $purposeLabel = \App\Models\Transmittal::$purposes[$tr->purpose] ?? $tr->purpose;
                        @endphp
                        <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;border-radius:var(--cde-radius);background:var(--cde-surface);border:1px solid var(--cde-border);">
                            <div style="width:36px;height:36px;border-radius:8px;background:rgba(139,92,246,0.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#8b5cf6" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:600;font-size:13px;color:var(--cde-text);">{{ $tr->transmittal_number }} — {{ $tr->subject }}</div>
                                <div style="font-size:11px;color:var(--cde-muted);margin-top:2px;">
                                    To: {{ $tr->to_organization }} · {{ $purposeLabel }} · {{ $tr->items->count() }} doc(s)
                                    @if($tr->sender) · {{ $tr->sender->name }} @endif
                                </div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <span style="padding:2px 8px;border-radius:99px;font-size:10px;font-weight:600;text-transform:uppercase;{{ $statusStyle }}">
                                    {{ \App\Models\Transmittal::$statuses[$tr->status] ?? $tr->status }}
                                </span>
                                <div style="font-size:10px;color:var(--cde-subtle);margin-top:3px;">{{ $tr->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        </div>
    @endif

    {{-- ── RFIs ───────────────────────────────────────────────────── --}}
    @php $rfis = $this->getRfis(); @endphp
    @if($rfis->isNotEmpty())
        <div class="cde-section-wrap">
            <x-filament::section icon="heroicon-o-question-mark-circle" icon-color="warning" collapsible collapsed>
                <x-slot name="heading">RFIs</x-slot>
                <x-slot name="description">{{ $rfis->whereNotIn('status', ['closed','void'])->count() }} open · {{ $rfis->count() }} total</x-slot>

                <div style="display: grid; gap: 8px;">
                    @foreach($rfis as $rfi)
                        @php
                            $rfiSt = match ($rfi->status) {
                                'open' => 'background:rgba(59,130,246,0.1);color:#3b82f6;',
                                'under_review' => 'background:rgba(245,158,11,0.1);color:#f59e0b;',
                                'answered' => 'background:rgba(16,185,129,0.1);color:#10b981;',
                                'closed' => 'background:rgba(107,114,128,0.1);color:#6b7280;',
                                default => 'background:rgba(107,114,128,0.1);color:#6b7280;',
                            };
                            $priSt = match ($rfi->priority) {
                                'urgent' => 'background:#fef2f2;color:#dc2626;',
                                'high' => 'background:#fff7ed;color:#ea580c;',
                                'medium' => 'background:#fffbeb;color:#d97706;',
                                default => 'background:#f0fdf4;color:#16a34a;',
                            };
                        @endphp
                        <div x-data="{ open: false }" style="border-radius:var(--cde-radius);background:var(--cde-surface);border:1px solid var(--cde-border);overflow:hidden;">
                            <div @click="open = !open" style="display:flex;align-items:center;gap:14px;padding:12px 16px;cursor:pointer;">
                                <div style="width:36px;height:36px;border-radius:8px;background:rgba(245,158,11,0.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-weight:600;font-size:13px;color:var(--cde-text);">{{ $rfi->rfi_number }} — {{ $rfi->subject }}</div>
                                    <div style="font-size:11px;color:var(--cde-muted);margin-top:2px;">
                                        @if($rfi->submitter) By: {{ $rfi->submitter->name }} @endif
                                        @if($rfi->assignee) · To: {{ $rfi->assignee->name }} @endif
                                        @if($rfi->due_date) · Due: {{ $rfi->due_date->format('M d') }} @endif
                                    </div>
                                </div>
                                <span style="padding:2px 7px;border-radius:99px;font-size:9px;font-weight:700;text-transform:uppercase;{{ $priSt }}">{{ $rfi->priority }}</span>
                                <span style="padding:2px 8px;border-radius:99px;font-size:10px;font-weight:600;text-transform:uppercase;{{ $rfiSt }}">{{ \App\Models\Rfi::$statuses[$rfi->status] ?? $rfi->status }}</span>
                                <svg :class="open && 'rotate-180'" style="width:14px;height:14px;color:var(--cde-subtle);transition:transform .15s;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </div>
                            <div x-show="open" x-collapse style="padding:0 16px 14px;border-top:1px solid var(--cde-border);">
                                <div style="margin-top:12px;">
                                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--cde-subtle);margin-bottom:4px;">Question</div>
                                    <div style="font-size:13px;color:var(--cde-text);line-height:1.6;padding:8px 12px;background:var(--cde-bg);border-radius:6px;border:1px solid var(--cde-border);">{{ $rfi->question }}</div>
                                </div>
                                @if($rfi->answer)
                                    <div style="margin-top:10px;">
                                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#10b981;margin-bottom:4px;">Answer</div>
                                        <div style="font-size:13px;color:var(--cde-text);line-height:1.6;padding:8px 12px;background:#f0fdf4;border-radius:6px;border:1px solid #bbf7d0;">{{ $rfi->answer }}</div>
                                        @if($rfi->answered_at)
                                            <div style="font-size:10px;color:var(--cde-subtle);margin-top:3px;">Answered {{ $rfi->answered_at->diffForHumans() }}</div>
                                        @endif
                                    </div>
                                @endif
                                <div style="margin-top:12px;display:flex;gap:6px;">
                                    @if(in_array($rfi->status, ['open', 'under_review']))
                                        <div x-data="{ showForm: false }">
                                            <button @click="showForm = !showForm" style="padding:5px 12px;border-radius:5px;font-size:11px;font-weight:600;background:#10b981;color:white;border:none;cursor:pointer;">✎ Answer</button>
                                            <div x-show="showForm" x-collapse style="margin-top:6px;">
                                                <form wire:submit.prevent="answerRfi({{ $rfi->id }}, $event.target.answer.value)">
                                                    <textarea name="answer" rows="3" placeholder="Type your answer..." style="width:100%;padding:8px;border-radius:6px;border:1px solid var(--cde-border);font-size:12px;resize:vertical;"></textarea>
                                                    <button type="submit" style="margin-top:4px;padding:5px 14px;border-radius:5px;font-size:11px;font-weight:600;background:var(--cde-accent);color:white;border:none;cursor:pointer;">Submit</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                    @if($rfi->status === 'answered')
                                        <button wire:click="closeRfi({{ $rfi->id }})" style="padding:5px 12px;border-radius:5px;font-size:11px;font-weight:600;background:#6b7280;color:white;border:none;cursor:pointer;">✓ Close</button>
                                    @endif
                                    @if($rfi->status === 'closed')
                                        <button wire:click="reopenRfi({{ $rfi->id }})" style="padding:5px 12px;border-radius:5px;font-size:11px;font-weight:600;background:#f59e0b;color:white;border:none;cursor:pointer;">↻ Reopen</button>
                                    @endif
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
                    <div class="cde-share-header">
                        <div>
                            <h3 style="margin:0;font-size:16px;font-weight:700;color:var(--cde-text);">Sharing & Permissions</h3>
                            <p style="margin:3px 0 0;font-size:12px;color:var(--cde-muted);">{{ $shareDoc->document_number }} — {{ $shareDoc->title }}</p>
                        </div>
                        <button wire:click="$set('showShareModal', false)" style="background:none;border:none;cursor:pointer;color:var(--cde-subtle);padding:6px;">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="cde-share-body">
                        <div style="display:flex;gap:6px;margin-bottom:16px;">
                            <button wire:click="generateShareLink({{ $shareDoc->id }}, 'view', 7)" style="flex:1;padding:8px;border-radius:6px;border:1px solid var(--cde-border);background:var(--cde-surface);cursor:pointer;font-size:12px;font-weight:600;color:var(--cde-muted);">👁 View Link</button>
                            <button wire:click="generateShareLink({{ $shareDoc->id }}, 'download', 7)" style="flex:1;padding:8px;border-radius:6px;border:none;background:var(--cde-accent);cursor:pointer;font-size:12px;font-weight:600;color:white;">🔗 Download Link</button>
                        </div>
                        @php $shares = $this->getDocumentShares($shareDoc->id); @endphp
                        @if($shares->isNotEmpty())
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--cde-subtle);margin-bottom:6px;">People with access</div>
                            @foreach($shares as $share)
                                @if($share->shared_with)
                                    <div class="cde-share-person">
                                        <div class="cde-share-avatar">{{ strtoupper(substr($share->sharedWith->name ?? '?', 0, 1)) }}</div>
                                        <div style="flex:1;min-width:0;">
                                            <div class="cde-share-name">{{ $share->sharedWith->name ?? 'Unknown' }}</div>
                                            <div class="cde-share-email">{{ $share->sharedWith->email ?? '' }}</div>
                                        </div>
                                        <span class="cde-share-badge cde-share-badge-{{ $share->permission }}">{{ $share->permission }}</span>
                                        <button wire:click="revokeShare({{ $share->id }})" class="cde-share-revoke">✕</button>
                                    </div>
                                @endif
                                @if($share->share_token)
                                    <div class="cde-link-row">
                                        <span class="cde-link-token">{{ config('app.url') }}/share/doc/{{ $share->share_token }}</span>
                                        <span class="cde-share-badge cde-share-badge-{{ $share->permission }}">{{ $share->permission }}</span>
                                        @if($share->expires_at)
                                            <span style="font-size:10px;color:var(--cde-subtle);">{{ $share->expires_at->diffForHumans() }}</span>
                                        @endif
                                        <button wire:click="revokeShare({{ $share->id }})" class="cde-share-revoke">✕</button>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div style="text-align:center;padding:20px;color:var(--cde-subtle);font-size:12px;">🔒 No active shares</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif

    @else
    {{-- ══════════════════════════════════════════════════════════════════
         REQUIRED SUBMISSIONS TAB
         ══════════════════════════════════════════════════════════════════ --}}

    {{-- Stage Progress Cards --}}
    @php $subStats = $this->getSubmissionStats(); @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));gap:8px;margin-bottom:16px;">
        @foreach($subStats as $s)
            @if($s['total'] > 0)
                <div style="background:white;border:1px solid #e2e8f0;border-radius:8px;padding:12px;text-align:center;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#64748b;margin-bottom:6px;">{{ $s['label'] }}</div>
                    <div style="height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;margin-bottom:6px;">
                        <div style="width:{{ $s['completion'] }}%;height:100%;background:{{ $s['completion'] >= 80 ? '#10b981' : ($s['completion'] >= 40 ? '#d97706' : '#ef4444') }};border-radius:3px;transition:width .3s;"></div>
                    </div>
                    <div style="display:flex;justify-content:center;gap:8px;font-size:10px;">
                        <span style="color:#10b981;font-weight:700;">{{ $s['approved'] }}<span style="font-weight:400;color:#94a3b8;"> ✓</span></span>
                        <span style="color:#475569;font-weight:700;">{{ $s['total'] }}<span style="font-weight:400;color:#94a3b8;"> total</span></span>
                        @if($s['overdue'] > 0)
                            <span style="color:#ef4444;font-weight:700;">{{ $s['overdue'] }}<span style="font-weight:400;"> late</span></span>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Submissions by Stage --}}
    @foreach(\App\Models\DocumentSubmission::$stages as $stageKey => $stageLabel)
        @php $stageSubs = $this->getSubmissions($stageKey); @endphp
        @if($stageSubs->isNotEmpty())
            <x-filament::section collapsible>
                <x-slot name="heading">
                    <span style="display:flex;align-items:center;gap:6px;">
                        {{ $stageLabel }}
                        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;background:rgba(99,102,241,0.1);color:#6366f1;">{{ $stageSubs->count() }}</span>
                        @php
                            $stageApproved = $stageSubs->where('status', 'approved')->count();
                            $stagePct = $stageSubs->count() > 0 ? round(($stageApproved / $stageSubs->count()) * 100) : 0;
                        @endphp
                        @if($stageSubs->count() > 0)
                            <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;background:{{ $stagePct >= 80 ? '#dcfce7' : ($stagePct >= 40 ? '#fef3c7' : '#f1f5f9') }};color:{{ $stagePct >= 80 ? '#16a34a' : ($stagePct >= 40 ? '#d97706' : '#64748b') }};">
                                {{ $stagePct }}%
                            </span>
                        @endif
                    </span>
                </x-slot>

                <div style="overflow-x:auto;">
                    <table style="width:100%;font-size:12px;border-collapse:collapse;">
                        <thead>
                            <tr style="border-bottom:2px solid #e2e8f0;">
                                <th style="text-align:left;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Document / Report</th>
                                <th style="text-align:left;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Discipline</th>
                                <th style="text-align:center;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Due Date</th>
                                <th style="text-align:center;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Status</th>
                                <th style="text-align:center;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">File</th>
                                <th style="text-align:center;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stageSubs as $sub)
                                @php
                                    $statusColors = [
                                        'pending' => ['bg' => '#f1f5f9', 'text' => '#475569'],
                                        'submitted' => ['bg' => '#dbeafe', 'text' => '#2563eb'],
                                        'approved' => ['bg' => '#dcfce7', 'text' => '#16a34a'],
                                        'rejected' => ['bg' => '#fef2f2', 'text' => '#dc2626'],
                                        'overdue' => ['bg' => '#fef2f2', 'text' => '#dc2626'],
                                        'waived' => ['bg' => '#f5f3ff', 'text' => '#7c3aed'],
                                    ];
                                    $sc = $statusColors[$sub->status] ?? $statusColors['pending'];
                                    $isOverdue = $sub->isOverdue();
                                @endphp
                                <tr style="border-bottom:1px solid #f1f5f9;{{ $isOverdue ? 'background:rgba(239,68,68,0.02);' : '' }}">
                                    <td style="padding:8px 10px;font-weight:600;color:#334155;">
                                        {{ $sub->title }}
                                        @if($sub->description)
                                            <div style="font-size:10px;font-weight:400;color:#94a3b8;margin-top:1px;">{{ \Illuminate\Support\Str::limit($sub->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td style="padding:8px 10px;color:#64748b;">
                                        {{ \App\Models\DocumentSubmission::$disciplines[$sub->discipline] ?? $sub->discipline ?? '—' }}
                                    </td>
                                    <td style="text-align:center;padding:8px 10px;white-space:nowrap;">
                                        @if($sub->due_date)
                                            <span style="color:{{ $isOverdue ? '#ef4444' : '#475569' }};font-weight:{{ $isOverdue ? '700' : '400' }};">
                                                {{ $sub->due_date->format('M d, Y') }}
                                            </span>
                                            @if($isOverdue)
                                                <div style="font-size:9px;color:#ef4444;font-weight:700;">{{ $sub->due_date->diffForHumans() }}</div>
                                            @endif
                                        @else
                                            <span style="color:#e2e8f0;">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;padding:8px 10px;">
                                        <span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:10px;font-weight:700;background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">
                                            {{ ucfirst($sub->status) }}
                                        </span>
                                        @if($sub->status === 'rejected' && $sub->rejection_reason)
                                            <div style="font-size:9px;color:#ef4444;margin-top:2px;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $sub->rejection_reason }}">
                                                {{ $sub->rejection_reason }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align:center;padding:8px 10px;">
                                        @if($sub->file_path)
                                            <button wire:click="downloadSubmission({{ $sub->id }})" style="background:none;border:none;cursor:pointer;color:#2563eb;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:3px;" title="{{ $sub->file_name }}">
                                                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                                {{ $sub->formattedSize() }}
                                            </button>
                                        @else
                                            <span style="color:#e2e8f0;font-size:11px;">No file</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;padding:8px 10px;">
                                        <div style="display:flex;gap:4px;justify-content:center;">
                                            @if(in_array($sub->status, ['pending', 'rejected']))
                                                {{-- Upload / Submit button --}}
                                                <button
                                                    wire:click="mountAction('submitDocumentAction', {submissionId: {{ $sub->id }}})"
                                                    style="background:#2563eb;color:white;border:none;padding:4px 10px;border-radius:4px;font-size:10px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:3px;"
                                                    title="Upload & Submit"
                                                >
                                                    <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                                    Submit
                                                </button>
                                            @endif
                                            @if($sub->status === 'submitted')
                                                <button wire:click="reviewSubmission({{ $sub->id }}, 'approved')" style="background:#10b981;color:white;border:none;padding:4px 8px;border-radius:4px;font-size:10px;font-weight:600;cursor:pointer;" title="Approve">✓</button>
                                                <button wire:click="reviewSubmission({{ $sub->id }}, 'rejected')" style="background:#ef4444;color:white;border:none;padding:4px 8px;border-radius:4px;font-size:10px;font-weight:600;cursor:pointer;" title="Reject">✕</button>
                                            @endif
                                            @if($sub->status === 'approved')
                                                <span style="color:#10b981;font-size:11px;">✓ Done</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    @endforeach

    @php $anySubmissions = \App\Models\DocumentSubmission::where('cde_project_id', $this->record->id)->exists(); @endphp
    @if(!$anySubmissions)
        <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
            <svg style="width:48px;height:48px;margin:0 auto 12px;opacity:0.3;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75"/></svg>
            <div style="font-size:15px;font-weight:600;color:#64748b;margin-bottom:4px;">No Required Submissions Yet</div>
            <div style="font-size:12px;max-width:400px;margin:0 auto;">
                Use the <strong>"Add Required Submission"</strong> button above to define which reports and documents must be submitted at each project stage.
            </div>
        </div>
    @endif
    @endif {{-- end activeDocTab --}}

</x-filament-panels::page>