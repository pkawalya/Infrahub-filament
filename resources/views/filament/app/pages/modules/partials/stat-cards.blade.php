{{-- Reusable stat cards row for module pages mapped to CDE Document Manager looks --}}
{{-- Usage: @include('filament.app.pages.modules.partials.stat-cards', ['stats' => [...] ]) --}}

@push('styles')
    <style>
        .mod-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .mod-stat-card {
            position: relative;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            overflow: hidden;
            transition: transform 250ms cubic-bezier(.4, 0, .2, 1), box-shadow 250ms cubic-bezier(.4, 0, .2, 1);
            background: white;
            border: 1px solid #e5e7eb;
        }

        .dark .mod-stat-card {
            background: rgba(31, 41, 55, .7);
            border-color: rgba(255, 255, 255, .08);
        }

        .mod-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -5px rgba(0, 0, 0, .1), 0 4px 10px -5px rgba(0, 0, 0, .05);
        }

        .mod-stat-card.primary {
            background: linear-gradient(135deg, #0f766e 0%, #14b8a6 50%, #2dd4bf 100%);
            color: white;
            border: none;
        }

        .mod-stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            opacity: 0.08;
            background: currentColor;
        }

        .mod-stat-card.primary::before {
            background: white;
        }

        .mod-stat-label {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.25rem;
            color: #6b7280;
        }

        .dark .mod-stat-label {
            color: #9ca3af;
        }

        .mod-stat-card.primary .mod-stat-label {
            color: rgba(255, 255, 255, .7);
        }

        .mod-stat-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.1;
            color: #111827;
        }

        .dark .mod-stat-card:not(.primary) .mod-stat-value {
            color: #f3f4f6;
        }

        .mod-stat-card.primary .mod-stat-value {
            color: white;
        }

        .mod-stat-sub {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            font-weight: 500;
            color: #6b7280;
        }

        .dark .mod-stat-sub {
            color: #9ca3af;
        }

        .mod-stat-sub.success {
            color: #10b981;
        }

        .mod-stat-sub.danger {
            color: #ef4444;
        }

        .mod-stat-sub.warning {
            color: #d97706;
        }

        .mod-stat-sub.info {
            color: #3b82f6;
        }

        .mod-stat-card.primary .mod-stat-sub {
            color: rgba(255, 255, 255, .7);
        }

        .mod-stat-card.primary .mod-stat-sub.success {
            color: rgba(255, 255, 255, .75);
        }

        .mod-stat-card.primary .mod-stat-sub.danger {
            color: #fca5a5;
        }

        .mod-stat-icon-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.75rem;
            flex-shrink: 0;
            transition: transform 300ms;
        }

        .mod-stat-card:hover .mod-stat-icon-wrap {
            transform: scale(1.1);
        }

        .mod-stat-card.primary .mod-stat-icon-wrap {
            background: rgba(255, 255, 255, .15);
            backdrop-filter: blur(8px);
        }

        /* Dark mode icon backgrounds if not primary */
        .dark .mod-stat-card:not(.primary) .mod-stat-icon-wrap {
            background: rgba(255, 255, 255, .08) !important;
        }

        @media (max-width: 768px) {
            .mod-stat-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media (max-width: 480px) {
            .mod-stat-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endpush

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