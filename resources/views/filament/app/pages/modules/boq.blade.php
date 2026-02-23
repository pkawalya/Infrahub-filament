<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    {{-- Category Breakdown --}}
    @php $categorySummary = $this->getCategorySummary(); @endphp
    @if(count($categorySummary) > 0)
        <div style="margin-bottom:0.75rem;">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:0.5rem;">
                <span
                    style="font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--fi-color-gray-500, #6b7280);">Section
                    Breakdown</span>
                <span style="font-size:0.6rem;color:var(--fi-color-gray-400, #9ca3af);">({{ count($categorySummary) }}
                    categories)</span>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                @foreach($categorySummary as $cat)
                    <div style="
                                border-radius:0.5rem;
                                padding:0.5rem 0.75rem;
                                border:1px solid color-mix(in srgb, var(--fi-color-gray-400, #9ca3af) 20%, transparent);
                                background:color-mix(in srgb, var(--fi-color-gray-50, #f9fafb) 50%, transparent);
                                min-width:140px;
                                flex:1;
                                max-width:200px;
                            ">
                        <div
                            style="font-weight:600;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.04em;color:var(--fi-color-gray-500, #6b7280);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $cat['label'] }}
                        </div>
                        <div style="font-weight:700;font-size:0.9rem;color:var(--fi-color-gray-950, #0f172a);">
                            {{ $cat['total_formatted'] }}
                        </div>
                        <div style="font-size:0.6rem;color:var(--fi-color-gray-400, #9ca3af);margin-top:1px;">
                            {{ $cat['count'] }} item{{ $cat['count'] !== 1 ? 's' : '' }}
                            @if($cat['variations'] > 0) · +var @endif
                        </div>
                        @if($cat['progress'] > 0)
                            <div style="margin-top:4px;display:flex;align-items:center;gap:4px;">
                                <div
                                    style="flex:1;height:3px;background:color-mix(in srgb, var(--fi-color-gray-200, #e5e7eb) 60%, transparent);border-radius:2px;overflow:hidden;">
                                    <div
                                        style="height:100%;width:{{ min($cat['progress'], 100) }}%;background:{{ $cat['progress'] >= 100 ? '#059669' : ($cat['progress'] >= 50 ? '#2563eb' : '#6366f1') }};border-radius:2px;">
                                    </div>
                                </div>
                                <span
                                    style="font-size:0.55rem;font-weight:600;color:{{ $cat['progress'] >= 100 ? '#059669' : ($cat['progress'] >= 50 ? '#2563eb' : 'var(--fi-color-gray-500)') }};">{{ $cat['progress'] }}%</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @push('styles')
        <style>
            .boq-help-strip {
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                border-radius: 6px;
                padding: 6px 14px;
                margin-bottom: 0.75rem;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 11px;
                color: #1e40af;
            }

            .dark .boq-help-strip {
                background: rgba(30, 64, 175, 0.08);
                border-color: rgba(59, 130, 246, 0.15);
                color: #93c5fd;
            }

            .boq-help-strip kbd {
                background: rgba(30, 64, 175, 0.08);
                border: 1px solid rgba(30, 64, 175, 0.12);
                border-radius: 3px;
                padding: 0 4px;
                font-size: 10px;
                font-family: monospace;
            }

            .dark .boq-help-strip kbd {
                background: rgba(96, 165, 250, 0.1);
                border-color: rgba(96, 165, 250, 0.15);
            }

            .boq-items-expand {
                margin-top: -6px;
                margin-bottom: 12px;
                border: 1px solid #e2e8f0;
                border-radius: 0 0 8px 8px;
                overflow: hidden;
            }

            .dark .boq-items-expand {
                border-color: rgba(255, 255, 255, 0.06);
            }

            .boq-items-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 11px;
            }

            .boq-items-table thead th {
                background: #f8fafc;
                padding: 5px 8px;
                font-weight: 700;
                font-size: 9px;
                text-transform: uppercase;
                letter-spacing: 0.3px;
                color: #64748b;
                border-bottom: 1px solid #e2e8f0;
                text-align: left;
            }

            .dark .boq-items-table thead th {
                background: rgba(255, 255, 255, 0.02);
                color: #94a3b8;
                border-bottom-color: rgba(255, 255, 255, 0.04);
            }

            .boq-items-table thead th.r {
                text-align: right;
            }

            .boq-items-table thead th.c {
                text-align: center;
            }

            .boq-items-table tbody td {
                padding: 4px 8px;
                border-bottom: 1px solid #f1f5f9;
                color: var(--fi-color-gray-700, #334155);
            }

            .dark .boq-items-table tbody td {
                border-bottom-color: rgba(255, 255, 255, 0.03);
                color: #cbd5e1;
            }

            .boq-items-table tbody td.r {
                text-align: right;
            }

            .boq-items-table tbody td.c {
                text-align: center;
            }

            .boq-items-table tbody td.code {
                font-weight: 600;
                font-family: monospace;
                font-size: 10px;
            }

            .boq-items-table tbody tr:hover {
                background: rgba(99, 102, 241, 0.02);
            }

            .boq-items-table tbody tr.var td {
                background: rgba(245, 158, 11, 0.03);
            }

            .boq-items-table tfoot td {
                padding: 5px 8px;
                font-weight: 700;
                font-size: 11px;
                border-top: 1px solid #e2e8f0;
                background: #f8fafc;
            }

            .dark .boq-items-table tfoot td {
                background: rgba(255, 255, 255, 0.02);
                border-top-color: rgba(255, 255, 255, 0.04);
            }

            .prog-mini {
                display: inline-block;
                width: 36px;
                height: 3px;
                background: #e2e8f0;
                border-radius: 2px;
                overflow: hidden;
                vertical-align: middle;
                margin-right: 3px;
            }

            .dark .prog-mini {
                background: rgba(255, 255, 255, 0.08);
            }

            .prog-mini .f {
                height: 100%;
                border-radius: 2px;
            }
        </style>
    @endpush

    {{-- Compact help strip --}}
    <div class="boq-help-strip">
        <span>📋</span>
        <span><strong>Import Items:</strong> Click <kbd>⋯</kbd> on any BOQ → <strong>Bulk Add (Paste)</strong> to paste from
            Excel, or <strong>Bulk Upload (File)</strong> to upload a CSV file. Format: Code, Description, Unit, Qty, Rate</span>
    </div>

    {{ $this->table }}

    @if($expandedBoqId)
        @php $boqItems = $this->getBoqItems($expandedBoqId); @endphp
        @if(!empty($boqItems))
            <div class="boq-items-expand" wire:key="boq-items-{{ $expandedBoqId }}">
                <table class="boq-items-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th class="c">Unit</th>
                            <th class="r">Qty</th>
                            <th class="r">Rate</th>
                            <th class="r">Amount</th>
                            <th class="c">Cat.</th>
                            <th class="c">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($boqItems as $item)
                            <tr class="{{ $item['is_variation'] ? 'var' : '' }}">
                                <td class="code">{{ $item['is_variation'] ? '🔸' : '' }}{{ $item['item_code'] }}</td>
                                <td title="{{ $item['description'] }}">
                                    {{ \Illuminate\Support\Str::limit($item['description'], 45) }}</td>
                                <td class="c">{{ $item['unit'] }}</td>
                                <td class="r">{{ $item['quantity'] }}</td>
                                <td class="r">{{ $item['unit_rate'] }}</td>
                                <td class="r" style="font-weight:600;">{{ $item['amount'] }}</td>
                                <td class="c"><span
                                        style="font-size:9px;background:rgba(100,116,139,0.06);padding:1px 4px;border-radius:3px;">{{ $item['category'] }}</span>
                                </td>
                                <td class="c">
                                    <span class="prog-mini"><span class="f"
                                            style="width:{{ min($item['progress_pct'], 100) }}%;background:{{ $item['progress_pct'] >= 100 ? '#059669' : ($item['progress_pct'] >= 50 ? '#3b82f6' : '#6366f1') }};"></span></span>
                                    <span
                                        style="font-size:10px;font-weight:600;color:{{ $item['progress_pct'] >= 100 ? '#059669' : ($item['progress_pct'] >= 50 ? '#3b82f6' : '#64748b') }};">{{ $item['progress_pct'] }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align:right;">Total ({{ count($boqItems) }})</td>
                            <td class="r">
                                {{ \App\Support\CurrencyHelper::format(array_sum(array_column($boqItems, 'amount_raw'))) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    @endif
</x-filament-panels::page>