<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    {{-- Tab Navigation --}}
    <div
        style="display: flex; gap: 4px; border-bottom: 2px solid rgba(255,255,255,0.06); padding-bottom: 0; margin-bottom: 1.5rem;">
        @foreach(['incidents' => '🛡️ Incidents', 'inspections' => '📋 Inspections', 'snags' => '🐛 Snag / Defect List'] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')" style="padding: 10px 20px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; border-radius: 8px 8px 0 0; transition: all 0.2s;
                           {{ $this->activeTab === $tab
            ? 'background: var(--primary-600, #2563eb); color: white;'
            : 'background: transparent; color: var(--gray-400, #9ca3af);' }}"
                onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                {{ $label }}
                @if($tab === 'incidents')
                    <span
                        style="background: rgba(239,68,68,0.15); color: #ef4444; font-size: 11px; padding: 2px 7px; border-radius: 99px; margin-left: 6px;">
                        {{ \App\Models\SafetyIncident::where('cde_project_id', $this->record->id)->whereIn('status', ['reported', 'investigating'])->count() }}
                    </span>
                @elseif($tab === 'snags')
                    <span
                        style="background: rgba(245,158,11,0.15); color: #f59e0b; font-size: 11px; padding: 2px 7px; border-radius: 99px; margin-left: 6px;">
                        {{ \App\Models\SnagItem::where('cde_project_id', $this->record->id)->whereIn('status', ['open', 'in_progress'])->count() }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    @if($this->activeTab === 'incidents')
        {{ $this->table }}

    @elseif($this->activeTab === 'inspections')
        {{-- Inspections Section --}}
        <x-filament::section icon="heroicon-o-clipboard-document-check" icon-color="info">
            <x-slot name="heading">Safety Inspections</x-slot>
            <x-slot name="description">Schedule, conduct, and record safety inspections with checklist templates.</x-slot>

            @php
                $inspections = \App\Models\SafetyInspection::where('cde_project_id', $this->record->id)
                    ->with(['inspector:id,name', 'template:id,name'])
                    ->orderByDesc('scheduled_date')
                    ->limit(50)
                    ->get();
            @endphp

            @if($inspections->isEmpty())
                <div style="text-align: center; padding: 3rem 1rem; color: var(--gray-400, #9ca3af);">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:48px;height:48px;margin:0 auto 12px;opacity:0.4;"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                    </svg>
                    <p style="font-weight:600;font-size:14px;">No Inspections Yet</p>
                    <p style="font-size:12px;margin-top:4px;">Use the "Schedule Inspection" button above to create one.</p>
                </div>
            @else
                <div style="display: grid; gap: 8px;">
                    @foreach($inspections as $inspection)
                        @php
                            $statusColor = match ($inspection->status) {
                                'completed' => 'background: rgba(16,185,129,0.1); color: #10b981;',
                                'in_progress' => 'background: rgba(245,158,11,0.1); color: #f59e0b;',
                                default => 'background: rgba(59,130,246,0.1); color: #3b82f6;',
                            };
                        @endphp
                        <div
                            style="display: flex; align-items: center; gap: 16px; padding: 12px 16px; border-radius: 10px; background: var(--gray-50, rgba(255,255,255,0.02)); border: 1px solid var(--gray-200, rgba(255,255,255,0.05));">
                            <div
                                style="flex-shrink:0; width:36px; height:36px; border-radius: 8px; {{ $statusColor }} display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px;">
                                {{ $inspection->score ?? '?' }}
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:600; font-size:14px;">{{ $inspection->title }}</div>
                                <div style="font-size:12px; color: var(--gray-400, #9ca3af); margin-top:2px;">
                                    {{ $inspection->inspection_number }} · {{ $inspection->type ?? 'General' }}
                                    @if($inspection->inspector) · Inspector: {{ $inspection->inspector->name }} @endif
                                    @if($inspection->template) · Template: {{ $inspection->template->name }} @endif
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <span
                                    style="padding: 3px 10px; border-radius: 99px; font-size:11px; font-weight:600; text-transform:uppercase; {{ $statusColor }}">
                                    {{ $inspection->status }}
                                </span>
                                <div style="font-size:11px; color: var(--gray-400, #9ca3af); margin-top:4px;">
                                    {{ $inspection->scheduled_date?->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>

        {{-- Inspection Templates --}}
        <x-filament::section icon="heroicon-o-document-duplicate" icon-color="warning" collapsible collapsed>
            <x-slot name="heading">Inspection Templates</x-slot>
            <x-slot name="description">Reusable checklist templates for recurring inspections.</x-slot>

            @php
                $templates = \App\Models\InspectionTemplate::where('company_id', $this->record->company_id)
                    ->where('is_active', true)
                    ->withCount('checklistItems')
                    ->orderBy('name')
                    ->get();
            @endphp

            @if($templates->isEmpty())
                <p style="text-align:center; color: var(--gray-400, #9ca3af); padding: 1.5rem; font-size:13px;">
                    No inspection templates created yet. Templates help standardize your inspections.
                </p>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px;">
                    @foreach($templates as $template)
                        <div
                            style="padding: 16px; border-radius: 10px; border: 1px solid var(--gray-200, rgba(255,255,255,0.06)); background: var(--gray-50, rgba(255,255,255,0.02));">
                            <div style="font-weight:600; font-size:14px; margin-bottom:4px;">{{ $template->name }}</div>
                            <div style="font-size:12px; color: var(--gray-400, #9ca3af);">
                                {{ \App\Models\InspectionTemplate::$types[$template->type] ?? $template->type ?? 'General' }}
                                · {{ $template->checklist_items_count }} items
                            </div>
                            @if($template->description)
                                <div style="font-size:12px; color: var(--gray-500, #6b7280); margin-top:6px;">
                                    {{ Str::limit($template->description, 80) }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>

    @elseif($this->activeTab === 'snags')
        {{-- Snag / Defect List --}}
        @php
            $snags = \App\Models\SnagItem::where('cde_project_id', $this->record->id)
                ->with(['reporter:id,name', 'assignee:id,name'])
                ->orderByDesc('created_at')
                ->limit(100)
                ->get();
            $openCount = $snags->whereIn('status', ['open', 'in_progress'])->count();
            $resolvedCount = $snags->whereIn('status', ['resolved', 'verified', 'closed'])->count();
        @endphp

        {{-- Snag summary strip --}}
        <div style="display:grid; grid-template-columns: repeat(4,1fr); gap:12px; margin-bottom:1.5rem;">
            <div
                style="padding:16px; border-radius:10px; background: var(--gray-50, rgba(255,255,255,0.02)); border:1px solid var(--gray-200, rgba(255,255,255,0.06)); text-align:center;">
                <div style="font-size:24px; font-weight:800;">{{ $snags->count() }}</div>
                <div style="font-size:11px; color:var(--gray-400,#9ca3af); text-transform:uppercase; letter-spacing:0.5px;">
                    Total Snags</div>
            </div>
            <div
                style="padding:16px; border-radius:10px; background: rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.1); text-align:center;">
                <div style="font-size:24px; font-weight:800; color:#ef4444;">{{ $openCount }}</div>
                <div style="font-size:11px; color:#ef4444; text-transform:uppercase; letter-spacing:0.5px;">Open</div>
            </div>
            <div
                style="padding:16px; border-radius:10px; background: rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.1); text-align:center;">
                <div style="font-size:24px; font-weight:800; color:#10b981;">{{ $resolvedCount }}</div>
                <div style="font-size:11px; color:#10b981; text-transform:uppercase; letter-spacing:0.5px;">Resolved</div>
            </div>
            <div
                style="padding:16px; border-radius:10px; background: rgba(245,158,11,0.05); border:1px solid rgba(245,158,11,0.1); text-align:center;">
                <div style="font-size:24px; font-weight:800; color:#f59e0b;">
                    {{ $snags->where('severity', 'critical')->count() }}</div>
                <div style="font-size:11px; color:#f59e0b; text-transform:uppercase; letter-spacing:0.5px;">Critical</div>
            </div>
        </div>

        @if($snags->isEmpty())
            <x-filament::section>
                <div style="text-align:center; padding:3rem; color:var(--gray-400,#9ca3af);">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:48px;height:48px;margin:0 auto 12px;opacity:0.4;"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 12.75c1.148 0 2.278.08 3.383.237 1.037.146 1.866.966 1.866 2.013 0 3.728-2.35 6.75-5.25 6.75S6.75 18.728 6.75 15c0-1.046.83-1.867 1.866-2.013A24.204 24.204 0 0112 12.75zm0 0c2.883 0 5.647.508 8.207 1.44a23.91 23.91 0 01-1.152-6.135c-.117-1.94-1.307-3.596-3.055-4.422A23.986 23.986 0 0012 3c-1.378 0-2.727.12-4.041.345C6.211 4.171 5.001 5.826 4.883 7.766a23.915 23.915 0 01-1.09 5.924c2.56-.932 5.324-1.44 8.207-1.44z" />
                    </svg>
                    <p style="font-weight:600; font-size:14px;">No Snag Items Yet</p>
                    <p style="font-size:12px; margin-top:4px;">Use the "Report Snag" button above to log defects and punch list
                        items.</p>
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <div style="overflow-x:auto;">
                    <table style="width:100%; font-size:13px; border-collapse:collapse;">
                        <thead>
                            <tr
                                style="text-align:left; color:var(--gray-400,#9ca3af); font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">
                                <th style="padding:8px 12px;">Snag #</th>
                                <th style="padding:8px 12px;">Title</th>
                                <th style="padding:8px 12px;">Category</th>
                                <th style="padding:8px 12px;">Severity</th>
                                <th style="padding:8px 12px;">Status</th>
                                <th style="padding:8px 12px;">Assigned To</th>
                                <th style="padding:8px 12px;">Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($snags as $snag)
                                            @php
                                                $sevColor = match ($snag->severity) {
                                                    'critical' => 'background:rgba(239,68,68,0.1);color:#ef4444;',
                                                    'major' => 'background:rgba(245,158,11,0.1);color:#f59e0b;',
                                                    default => 'background:rgba(107,114,128,0.1);color:#6b7280;',
                                                };
                                                $statusColor = match ($snag->status) {
                                                    'open' => 'background:rgba(239,68,68,0.1);color:#ef4444;',
                                                    'in_progress' => 'background:rgba(59,130,246,0.1);color:#3b82f6;',
                                                    'resolved' => 'background:rgba(16,185,129,0.1);color:#10b981;',
                                                    'verified' => 'background:rgba(16,185,129,0.15);color:#059669;',
                                                    'closed' => 'background:rgba(107,114,128,0.1);color:#6b7280;',
                                                    default => 'background:rgba(107,114,128,0.1);color:#6b7280;',
                                                };
                                                $isOverdue = $snag->due_date && $snag->due_date->isPast() && !in_array($snag->status, ['resolved', 'verified', 'closed']);
                                            @endphp
                                <tr
                                                style="border-top:1px solid var(--gray-200, rgba(255,255,255,0.05)); {{ $isOverdue ? 'background:rgba(239,68,68,0.03);' : '' }}">
                                                <td style="padding:10px 12px; font-weight:600; white-space:nowrap;">{{ $snag->snag_number }}
                                                </td>
                                                <td style="padding:10px 12px; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                                    title="{{ $snag->title }}">{{ $snag->title }}</td>
                                                <td style="padding:10px 12px;">
                                                    {{ \App\Models\SnagItem::$categories[$snag->category] ?? $snag->category ?? '—' }}</td>
                                                <td style="padding:10px 12px;"><span
                                                        style="padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;{{ $sevColor }}">{{ ucfirst($snag->severity) }}</span>
                                                </td>
                                                <td style="padding:10px 12px;"><span
                                                        style="padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;{{ $statusColor }}">{{ \App\Models\SnagItem::$statuses[$snag->status] ?? $snag->status }}</span>
                                                </td>
                                                <td style="padding:10px 12px; color:var(--gray-400,#9ca3af);">
                                                    {{ $snag->assignee?->name ?? '—' }}</td>
                                                <td
                                                    style="padding:10px 12px; {{ $isOverdue ? 'color:#ef4444;font-weight:600;' : 'color:var(--gray-400,#9ca3af);' }}">
                                                    {{ $snag->due_date?->format('M d, Y') ?? '—' }}
                                                    @if($isOverdue) ⚠ @endif
                                                </td>
                                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    @endif
</x-filament-panels::page>