<x-filament-panels::page>
    <style>
        .settings-section {
            border-radius: 1rem;
            border: 1px solid rgba(128, 128, 128, 0.15);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.03);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .dark .settings-section {
            background: rgba(255, 255, 255, 0.02);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .settings-section-header {
            padding: 1.5rem 1.5rem 1rem;
        }

        .settings-section-body {
            padding: 0 1.5rem 1.5rem;
        }

        /* Navigation cards */
        .nav-option-card {
            position: relative;
            border-radius: 1rem;
            border: 2px solid rgba(128, 128, 128, 0.15);
            overflow: hidden;
            text-align: left;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: transparent;
            width: 100%;
        }

        .nav-option-card:hover {
            border-color: rgba(128, 128, 128, 0.3);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }

        .nav-option-card.active {
            border-color: var(--c-primary, #3b82f6);
            box-shadow: 0 4px 24px rgba(59, 130, 246, 0.15);
        }

        .dark .nav-option-card {
            border-color: rgba(255, 255, 255, 0.08);
        }

        .dark .nav-option-card:hover {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .dark .nav-option-card.active {
            border-color: var(--c-primary, #3b82f6);
            background: rgba(59, 130, 246, 0.04);
        }

        .nav-option-inner {
            padding: 1.25rem;
        }

        /* Mini layout previews */
        .layout-preview {
            height: 80px;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid rgba(128, 128, 128, 0.15);
            margin-bottom: 1rem;
        }

        .dark .layout-preview {
            border-color: rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.03);
        }

        /* Color swatches */
        .swatch-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .swatch-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .swatch-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        .swatch-btn {
            position: relative;
            border-radius: 1rem;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            padding: 0;
            display: block;
            width: 100%;
        }

        .swatch-btn:hover {
            transform: scale(1.04);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.18);
        }

        .swatch-btn:focus {
            outline: none;
        }

        .swatch-btn.active {
            border-color: rgba(255, 255, 255, 0.7);
            transform: scale(1.02);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
        }

        .dark .swatch-btn.active {
            border-color: rgba(255, 255, 255, 0.6);
        }

        .swatch-color {
            width: 100%;
            aspect-ratio: 16 / 10;
            position: relative;
        }

        .swatch-check {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(1px);
        }

        .swatch-check-circle {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.92);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .swatch-label {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0.4rem 0.75rem;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.55), transparent);
            font-size: 0.7rem;
            font-weight: 600;
            color: white;
            text-transform: capitalize;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .swatch-hover-shine {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
            box-shadow: inset 0 0 30px rgba(255, 255, 255, 0.15);
        }

        .swatch-btn:hover .swatch-hover-shine {
            opacity: 1;
        }

        /* Preview bar */
        .preview-bar {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid rgba(128, 128, 128, 0.1);
            background: rgba(0, 0, 0, 0.02);
        }

        .dark .preview-bar {
            background: rgba(255, 255, 255, 0.015);
            border-top-color: rgba(255, 255, 255, 0.05);
        }

        .preview-label {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(128, 128, 128, 0.7);
            margin-bottom: 0.25rem;
        }

        .preview-items {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.5rem;
        }

        .preview-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.35rem;
        }
    </style>

    <form wire:submit.prevent="save">
        {{-- Hidden form fields for Livewire binding --}}
        <div class="hidden">{{ $this->form }}</div>

        @php
            $hexColors = \App\Support\ColorPalette::hex();
            $gradients = \App\Support\ColorPalette::gradients();
            $labels = \App\Support\ColorPalette::options();
            $current = $this->data['panel_color'] ?? 'blue';
            $navStyle = $this->data['navigation_style'] ?? 'sidebar';
        @endphp

        <div class="space-y-6">

            {{-- ===== Navigation Layout Section ===== --}}
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        Navigation Layout
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose your preferred navigation style</p>
                </div>

                <div class="settings-section-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Sidebar Option --}}
                        <button type="button" wire:click="$set('data.navigation_style', 'sidebar')"
                            class="nav-option-card {{ $navStyle === 'sidebar' ? 'active' : '' }}"
                            style="{{ $navStyle === 'sidebar' ? '--c-primary: ' . ($hexColors[$current] ?? '#3b82f6') . ';' : '' }}">
                            <div class="nav-option-inner">
                                {{-- Mini Layout Preview --}}
                                <div class="layout-preview flex gap-2 bg-gray-50 dark:bg-white/5">
                                    {{-- Sidebar --}}
                                    <div class="w-[60px] flex flex-col gap-1.5 p-2 border-r border-gray-200 dark:border-white/10"
                                        style="background-color: {{ $navStyle === 'sidebar' ? ($hexColors[$current] ?? '#3b82f6') . '15' : 'transparent' }};">
                                        <div class="w-full h-2 rounded-full"
                                            style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }}; opacity: 0.7;">
                                        </div>
                                        <div class="w-3/4 h-1.5 rounded-full bg-gray-300 dark:bg-white/20"></div>
                                        <div class="w-full h-1.5 rounded-full bg-gray-200 dark:bg-white/10"></div>
                                        <div class="w-5/6 h-1.5 rounded-full bg-gray-200 dark:bg-white/10"></div>
                                        <div class="w-2/3 h-1.5 rounded-full bg-gray-200 dark:bg-white/10"></div>
                                    </div>
                                    {{-- Content --}}
                                    <div class="flex-1 flex flex-col gap-1.5 p-2">
                                        <div class="w-1/2 h-2 rounded-full bg-gray-300 dark:bg-white/20"></div>
                                        <div class="flex-1 rounded bg-gray-100 dark:bg-white/5"></div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-800 dark:text-white">Sidebar Navigation
                                        </h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Classic layout — best
                                            for desktop</p>
                                    </div>
                                    @if($navStyle === 'sidebar')
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center"
                                            style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};">
                                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </button>

                        {{-- Top Nav Option --}}
                        <button type="button" wire:click="$set('data.navigation_style', 'top')"
                            class="nav-option-card {{ $navStyle === 'top' ? 'active' : '' }}"
                            style="{{ $navStyle === 'top' ? '--c-primary: ' . ($hexColors[$current] ?? '#3b82f6') . ';' : '' }}">
                            <div class="nav-option-inner">
                                {{-- Mini Layout Preview --}}
                                <div class="layout-preview flex flex-col gap-2 bg-gray-50 dark:bg-white/5">
                                    {{-- Top Bar --}}
                                    <div class="flex items-center gap-2 px-3 py-2 border-b border-gray-200 dark:border-white/10"
                                        style="background-color: {{ $navStyle === 'top' ? ($hexColors[$current] ?? '#3b82f6') . '15' : 'transparent' }};">
                                        <div class="w-4 h-2 rounded-full"
                                            style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }}; opacity: 0.7;">
                                        </div>
                                        <div class="flex gap-1.5 ml-auto">
                                            <div class="w-6 h-1.5 rounded-full bg-gray-300 dark:bg-white/20"></div>
                                            <div class="w-8 h-1.5 rounded-full bg-gray-200 dark:bg-white/10"></div>
                                            <div class="w-5 h-1.5 rounded-full bg-gray-200 dark:bg-white/10"></div>
                                        </div>
                                    </div>
                                    {{-- Content --}}
                                    <div class="flex-1 flex flex-col gap-1.5 px-3 pb-2">
                                        <div class="w-1/3 h-2 rounded-full bg-gray-300 dark:bg-white/20"></div>
                                        <div class="flex-1 rounded bg-gray-100 dark:bg-white/5"></div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-800 dark:text-white">Top Navigation</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Modern bar — great
                                            for tablets</p>
                                    </div>
                                    @if($navStyle === 'top')
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center"
                                            style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};">
                                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ===== Color Theme Section ===== --}}
            <div class="settings-section">
                <div class="settings-section-header">
                    <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <span>🎨</span> Color Theme
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Choose a color scheme to personalize your experience and match your brand.
                    </p>
                </div>

                {{-- Swatch Grid --}}
                <div class="settings-section-body">
                    <div class="swatch-grid">
                        @foreach($gradients as $key => $gradient)
                            <button type="button" wire:click="$set('data.panel_color', '{{ $key }}')"
                                class="swatch-btn {{ $current === $key ? 'active' : '' }}"
                                title="{{ strip_tags($labels[$key] ?? $key) }}">
                                <div class="swatch-color" style="background: {{ $gradient }};">
                                    @if($current === $key)
                                        <div class="swatch-check">
                                            <div class="swatch-check-circle">
                                                <svg class="w-6 h-6 text-gray-900" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="swatch-hover-shine"></div>
                                    <div class="swatch-label">{{ $key }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Live Preview Bar --}}
                @if($current)
                    <div class="preview-bar">
                        <p class="preview-label">Live Preview</p>
                        <div class="preview-items">
                            <div class="preview-item">
                                <span class="preview-label" style="margin-bottom: 0;">Button</span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-white text-xs font-bold shadow-md"
                                    style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Save Changes
                                </span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label" style="margin-bottom: 0;">Badge</span>
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold"
                                    style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }}20; color: {{ $hexColors[$current] ?? '#3b82f6' }};">
                                    ● Active
                                </span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label" style="margin-bottom: 0;">Navigation</span>
                                <div class="flex items-center gap-0.5 bg-gray-100 dark:bg-white/5 rounded-lg p-1">
                                    <span
                                        class="px-3 py-1 rounded-md text-xs text-gray-500 dark:text-gray-400">Dashboard</span>
                                    <span class="px-3 py-1 rounded-md text-xs font-semibold text-white shadow-sm"
                                        style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};">Analytics</span>
                                    <span
                                        class="px-3 py-1 rounded-md text-xs text-gray-500 dark:text-gray-400">Reports</span>
                                </div>
                            </div>
                            <div class="preview-item" style="flex: 1; min-width: 120px;">
                                <span class="preview-label" style="margin-bottom: 0;">Progress</span>
                                <div class="w-full h-2.5 rounded-full bg-gray-200 dark:bg-white/10 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                        style="width: 72%; background-color: {{ $hexColors[$current] ?? '#3b82f6' }};">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <div class="w-4 h-4 rounded-md shadow-sm"
                                style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};"></div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Current: <strong
                                    class="text-gray-700 dark:text-gray-200">{{ $labels[$current] ?? $current }}</strong>
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Save Button --}}
            <div class="flex justify-end">
                <x-filament::button type="submit" icon="heroicon-o-check">
                    Save Preferences
                </x-filament::button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('settings-saved', () => {
                setTimeout(() => window.location.reload(), 500);
            });
            Livewire.on('color-theme-updated', () => {
                setTimeout(() => window.location.reload(), 800);
            });
            Livewire.on('navigation-style-updated', () => {
                setTimeout(() => window.location.reload(), 800);
            });
        });
    </script>
</x-filament-panels::page>