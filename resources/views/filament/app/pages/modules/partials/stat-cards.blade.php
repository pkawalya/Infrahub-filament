{{-- Reusable stat cards row for module pages mapped to CDE Document Manager looks --}}
{{-- Usage: @include('filament.app.pages.modules.partials.stat-cards', ['stats' => [...] ]) --}}


<div class="mod-stat-grid" style="grid-template-columns: repeat({{ count($stats) }}, 1fr);">
    @foreach($stats as $stat)
        <div class="mod-stat-card {{ ($stat['primary'] ?? false) ? 'primary' : '' }}">
            <div
                style="display: flex; align-items: flex-start; justify-content: space-between; position: relative; z-index: 1;">
                <div>
                    <div class="mod-stat-label">{{ $stat['label'] }}</div>
                    <div class="mod-stat-value">{{ $stat['value'] }}</div>
                    @if(!empty($stat['sub']))
                        <div class="mod-stat-sub {{ $stat['sub_type'] ?? '' }}">{{ $stat['sub'] }}</div>
                    @endif
                </div>
                @if(!empty($stat['icon_svg']))
                    <div class="mod-stat-icon-wrap"
                        style="{{ (!($stat['primary'] ?? false) && !empty($stat['icon_bg'])) ? 'background: ' . $stat['icon_bg'] : '' }}">
                        <div style="width: 1.25rem; height: 1.25rem;">
                            {!! $stat['icon_svg'] !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>