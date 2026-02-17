{{-- Reusable stat cards row for module pages --}}
{{-- Usage: @include('filament.app.pages.modules.partials.stat-cards', ['stats' => [...] ]) --}}
{{-- Each stat: ['label' => '', 'value' => '', 'sub' => '', 'sub_type' => 'success|danger|warning|info|neutral',
'icon_svg' => 'raw svg', 'icon_bg' => '#hex', 'primary' => false] --}}

@push('styles')
<style>
    .mod-stat-grid {
        display: grid;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    .mod-stat-card {
        border-radius: 0.75rem;
        padding: 1.125rem 1.25rem;
        transition: box-shadow 200ms, transform 200ms;
        background: white;
        border: 1px solid #e5e7eb;
        color: #111827;
    }
    .mod-stat-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.07);
        transform: translateY(-1px);
    }
    .mod-stat-card.primary {
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
        color: white;
        border: none;
    }
    .mod-stat-label {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
    }
    .mod-stat-card.primary .mod-stat-label { color: rgba(255,255,255,.75); }
    .mod-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
        margin-top: 0.125rem;
        color: #111827;
    }
    .mod-stat-card.primary .mod-stat-value { color: white; }
    .mod-stat-sub {
        font-size: 0.75rem;
        margin-top: 0.125rem;
        color: #6b7280;
    }
    .mod-stat-sub.success { color: #10b981; }
    .mod-stat-sub.danger { color: #ef4444; }
    .mod-stat-sub.warning { color: #d97706; }
    .mod-stat-sub.info { color: #3b82f6; }
    .mod-stat-card.primary .mod-stat-sub { color: rgba(255,255,255,.7); }
    .mod-stat-card.primary .mod-stat-sub.success { color: rgba(255,255,255,.75); }
    .mod-stat-card.primary .mod-stat-sub.danger { color: #fca5a5; }
    .mod-stat-card.primary .mod-stat-sub.warning { color: #fde68a; }
    .mod-stat-card.primary .mod-stat-sub.info { color: #93c5fd; }
    .mod-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.5rem;
        flex-shrink: 0;
    }
    .mod-stat-icon svg { width: 1.125rem; height: 1.125rem; }

    /* Dark mode */
    .dark .mod-stat-card:not(.primary) {
        background: rgba(255,255,255,.05);
        border-color: rgba(255,255,255,.1);
        color: #f3f4f6;
    }
    .dark .mod-stat-label { color: #9ca3af; }
    .dark .mod-stat-card.primary .mod-stat-label { color: rgba(255,255,255,.75); }
    .dark .mod-stat-value { color: #f9fafb; }
    .dark .mod-stat-card.primary .mod-stat-value { color: white; }
    .dark .mod-stat-sub { color: #9ca3af; }

    @media (max-width: 768px) {
        .mod-stat-grid { grid-template-columns: repeat(2, 1fr) !important; }
    }
    @media (max-width: 480px) {
        .mod-stat-grid { grid-template-columns: 1fr !important; }
    }
</style>
@endpush

<div class="mod-stat-grid" style="grid-template-columns: repeat({{ count($stats) }}, 1fr);">
    @foreach($stats as $stat)
        <div class="mod-stat-card {{ ($stat['primary'] ?? false) ? 'primary' : '' }}">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div class="mod-stat-label">{{ $stat['label'] }}</div>
                    <div class="mod-stat-value">{{ $stat['value'] }}</div>
                    @if(!empty($stat['sub']))
                        <div class="mod-stat-sub {{ $stat['sub_type'] ?? '' }}">{{ $stat['sub'] }}</div>
                    @endif
                </div>
                @if(!empty($stat['icon_svg']))
                    <div class="mod-stat-icon" style="background: {{ ($stat['primary'] ?? false) ? 'rgba(255,255,255,.15)' : ($stat['icon_bg'] ?? '#f0f9ff') }};">
                        {!! $stat['icon_svg'] !!}
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>