<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    {{-- Category Breakdown Summary --}}
    @php $categorySummary = $this->getCategorySummary(); @endphp
    @if(count($categorySummary) > 0)
        <div style="margin-bottom:1.25rem;">
            <h3
                style="font-size:0.85rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.75rem;color:var(--fi-color-gray-500, #6b7280);">
                Section Breakdown
            </h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:0.75rem;">
                @foreach($categorySummary as $cat)
                    <div style="
                        border-radius:0.75rem;
                        padding:1rem 1.125rem;
                        border:1px solid color-mix(in srgb, var(--fi-color-gray-400, #9ca3af) 30%, transparent);
                        background:color-mix(in srgb, var(--fi-color-gray-50, #f9fafb) 50%, transparent);
                    ">
                        <div
                            style="font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;color:var(--fi-color-gray-500, #6b7280);margin-bottom:0.375rem;">
                            {{ $cat['label'] }}
                        </div>
                        <div style="font-weight:700;font-size:1.1rem;color:var(--fi-color-gray-950, #0f172a);">
                            {{ $cat['total_formatted'] }}
                        </div>
                        <div style="font-size:0.7rem;color:var(--fi-color-gray-400, #9ca3af);margin-top:0.25rem;">
                            {{ $cat['count'] }} item{{ $cat['count'] !== 1 ? 's' : '' }}
                            @if($cat['variations'] > 0)
                                · incl. {{ \App\Support\CurrencyHelper::format($cat['variations'], 0) }} variations
                            @endif
                        </div>
                        @if($cat['progress'] > 0)
                            <div style="margin-top:0.625rem;">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:3px;">
                                    <span
                                        style="font-size:0.65rem;font-weight:500;color:var(--fi-color-gray-500, #6b7280);">Progress</span>
                                    <span
                                        style="font-size:0.65rem;font-weight:600;color:{{ $cat['progress'] >= 100 ? '#059669' : ($cat['progress'] >= 50 ? '#2563eb' : 'var(--fi-color-gray-500, #6b7280)') }};">{{ $cat['progress'] }}%</span>
                                </div>
                                <div
                                    style="height:5px;background:color-mix(in srgb, var(--fi-color-gray-200, #e5e7eb) 60%, transparent);border-radius:3px;overflow:hidden;">
                                    <div
                                        style="height:100%;width:{{ min($cat['progress'], 100) }}%;background:{{ $cat['progress'] >= 100 ? '#059669' : ($cat['progress'] >= 50 ? '#2563eb' : '#6366f1') }};border-radius:3px;transition:width 0.5s ease;">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{ $this->table }}
</x-filament-panels::page>