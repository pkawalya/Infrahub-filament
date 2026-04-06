{{-- Reusable stat cards row for module pages --}}
{{-- Supports: icon (heroicon name) OR icon_svg (raw SVG), optional href for click-through --}}

<div class="mod-stat-grid" style="grid-template-columns: repeat({{ count($stats) }}, 1fr);">
    @foreach($stats as $stat)
        @php $href = $stat['href'] ?? null; @endphp

        @if($href)
            <a href="{{ $href }}" class="mod-stat-card {{ ($stat['primary'] ?? false) ? 'primary' : '' }} mod-stat-card--link" wire:navigate>
        @else
            <div class="mod-stat-card {{ ($stat['primary'] ?? false) ? 'primary' : '' }}">
        @endif

            <div style="display: flex; align-items: flex-start; justify-content: space-between; position: relative; z-index: 1;">
                <div style="min-width:0; flex:1;">
                    <div class="mod-stat-label">{{ $stat['label'] }}</div>
                    <div class="mod-stat-value" title="{{ $stat['full_value'] ?? $stat['value'] }}">{{ $stat['value'] }}</div>
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

            @if($href)
                <div class="mod-stat-card__arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:0.75rem;height:0.75rem;opacity:0.4;">
                        <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                    </svg>
                </div>
            @endif

        @if($href)
            </a>
        @else
            </div>
        @endif
    @endforeach
</div>