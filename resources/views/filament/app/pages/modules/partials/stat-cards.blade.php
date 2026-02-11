{{-- Reusable stat cards row for module pages --}}
{{-- Usage: @include('filament.app.pages.modules.partials.stat-cards', ['stats' => [...] ]) --}}
{{-- Each stat: ['label' => '', 'value' => '', 'sub' => '', 'sub_type' => 'success|danger|neutral', 'color' => '#hex',
'icon' => 'svg path', 'primary' => false] --}}

<div style="display: grid; grid-template-columns: repeat({{ count($stats) }}, 1fr); gap: 1rem; margin-bottom: 0.5rem;">
    @foreach($stats as $stat)
        <div style="
                background: {{ ($stat['primary'] ?? false) ? 'linear-gradient(135deg, #0f766e 0%, #0d9488 100%)' : 'white' }};
                color: {{ ($stat['primary'] ?? false) ? 'white' : '#111827' }};
                border-radius: 0.75rem;
                padding: 1.125rem 1.25rem;
                border: {{ ($stat['primary'] ?? false) ? 'none' : '1px solid #e5e7eb' }};
                transition: box-shadow 200ms;
            ">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div
                        style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: {{ ($stat['primary'] ?? false) ? 'rgba(255,255,255,.75)' : '#6b7280' }};">
                        {{ $stat['label'] }}
                    </div>
                    <div
                        style="font-size: 1.75rem; font-weight: 700; line-height: 1.2; margin-top: 0.125rem; color: {{ ($stat['primary'] ?? false) ? 'white' : '#111827' }};">
                        {{ $stat['value'] }}
                    </div>
                    @if(!empty($stat['sub']))
                        @php
                            $subColor = match ($stat['sub_type'] ?? 'neutral') {
                                'success' => ($stat['primary'] ?? false) ? 'rgba(255,255,255,.75)' : '#10b981',
                                'danger' => ($stat['primary'] ?? false) ? '#fca5a5' : '#ef4444',
                                default => ($stat['primary'] ?? false) ? 'rgba(255,255,255,.7)' : '#6b7280',
                            };
                        @endphp
                        <div style="font-size: 0.75rem; color: {{ $subColor }}; margin-top: 0.125rem;">{{ $stat['sub'] }}</div>
                    @endif
                </div>
                @if(!empty($stat['icon_svg']))
                    <div style="
                                display: flex; align-items: center; justify-content: center;
                                width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; flex-shrink: 0;
                                background: {{ ($stat['primary'] ?? false) ? 'rgba(255,255,255,.15)' : ($stat['icon_bg'] ?? '#f0f9ff') }};
                            ">
                        {!! $stat['icon_svg'] !!}
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>