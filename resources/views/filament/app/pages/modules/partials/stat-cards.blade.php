{{-- Reusable stat cards row for module pages --}}
{{-- Supports: icon (heroicon name) OR icon_svg (raw SVG) --}}

<div class="mod-stat-grid" style="grid-template-columns: repeat({{ count($stats) }}, 1fr);">
    @foreach($stats as $stat)
        <div class="mod-stat-card {{ ($stat['primary'] ?? false) ? 'primary' : '' }}">
            <div
                style="display: flex; align-items: flex-start; justify-content: space-between; position: relative; z-index: 1;">
                <div>
                    <div class="mod-stat-label">{{ $stat['label'] }}</div>
                    <div class="mod-stat-value" title="{{ $stat['full_value'] ?? $stat['value'] }}">{{ $stat['value'] }}
                    </div>
                    @if(!empty($stat['sub']))
                        <div class="mod-stat-sub {{ $stat['sub_type'] ?? '' }}">{{ $stat['sub'] }}</div>
                    @endif
                </div>
                @if(!empty($stat['icon']) || !empty($stat['icon_svg']))
                    <div class="mod-stat-icon-wrap"
                        style="{{ (!($stat['primary'] ?? false) && !empty($stat['icon_bg'])) ? 'background: ' . $stat['icon_bg'] : '' }}">
                        <div style="width: 1.25rem; height: 1.25rem;">
                            @if(!empty($stat['icon']))
                                <x-dynamic-component :component="$stat['icon']"
                                    style="width:1.125rem;height:1.125rem;{{ !empty($stat['icon_color']) ? 'color:' . $stat['icon_color'] : '' }}" />
                            @else
                                {!! $stat['icon_svg'] !!}
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>