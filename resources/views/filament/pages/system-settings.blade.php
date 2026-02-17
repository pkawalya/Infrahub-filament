<x-filament-panels::page>
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

        {{-- ===== Navigation Layout Section ===== --}}
        <div class="rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden bg-white dark:bg-gray-900/60 shadow-sm">
            <div class="px-6 pt-5 pb-3">
                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Navigation Layout
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose your preferred navigation style</p>
            </div>

            <div class="px-6 pb-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Sidebar Option --}}
                    <button
                        type="button"
                        wire:click="$set('data.navigation_style', 'sidebar')"
                        class="group relative rounded-2xl border-2 transition-all duration-300 overflow-hidden text-left
                            {{ $navStyle === 'sidebar'
    ? 'border-primary-500 dark:border-primary-400 bg-primary-50/50 dark:bg-primary-500/5 shadow-lg shadow-primary-500/10'
    : 'border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 hover:shadow-md' }}"
                    >
                        <div class="p-5">
                            {{-- Mini Layout Preview --}}
                            <div class="flex gap-2 mb-4 h-[80px] rounded-lg overflow-hidden border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5">
                                {{-- Sidebar --}}
                                <div class="w-[60px] flex flex-col gap-1.5 p-2 border-r border-gray-200 dark:border-white/10"
                                     style="background-color: {{ $navStyle === 'sidebar' ? ($hexColors[$current] ?? '#3b82f6') . '15' : 'transparent' }};">
                                    <div class="w-full h-2 rounded-full" style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }}; opacity: 0.7;"></div>
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
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-white">Sidebar Navigation</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Classic layout — best for desktop</p>
                                </div>
                                @if($navStyle === 'sidebar')
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center" style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};">
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </button>

                    {{-- Top Nav Option --}}
                    <button
                        type="button"
                        wire:click="$set('data.navigation_style', 'top')"
                        class="group relative rounded-2xl border-2 transition-all duration-300 overflow-hidden text-left
                            {{ $navStyle === 'top'
    ? 'border-primary-500 dark:border-primary-400 bg-primary-50/50 dark:bg-primary-500/5 shadow-lg shadow-primary-500/10'
    : 'border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 hover:shadow-md' }}"
                    >
                        <div class="p-5">
                            {{-- Mini Layout Preview --}}
                            <div class="flex flex-col gap-2 mb-4 h-[80px] rounded-lg overflow-hidden border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5">
                                {{-- Top Bar --}}
                                <div class="flex items-center gap-2 px-3 py-2 border-b border-gray-200 dark:border-white/10"
                                     style="background-color: {{ $navStyle === 'top' ? ($hexColors[$current] ?? '#3b82f6') . '15' : 'transparent' }};">
                                    <div class="w-4 h-2 rounded-full" style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }}; opacity: 0.7;"></div>
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
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Modern bar — great for tablets</p>
                                </div>
                                @if($navStyle === 'top')
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center" style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};">
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== Color Theme Section ===== --}}
        <div class="mt-5 rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden bg-white dark:bg-gray-900/60 shadow-sm">
            <div class="px-6 pt-5 pb-3">
                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span>🎨</span> Color Theme
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Choose a color scheme to personalize your experience and match your brand.
                </p>
            </div>

            {{-- Swatch Grid --}}
            <div class="px-6 pb-4">
                <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($gradients as $key => $gradient)
                                        <button
                                            type="button"
                                            wire:click="$set('data.panel_color', '{{ $key }}')"
                                            class="group relative rounded-2xl overflow-hidden transition-all duration-300 focus:outline-none
                                                {{ $current === $key
                        ? 'ring-2 ring-offset-2 dark:ring-offset-gray-900 ring-white/80 scale-[1.02] shadow-xl'
                        : 'hover:scale-[1.03] hover:shadow-lg shadow-sm' }}"
                                            title="{{ strip_tags($labels[$key] ?? $key) }}"
                                        >
                                            <div class="w-full" style="padding-bottom: 62.5%; background: {{ $gradient }};"></div>

                                            @if($current === $key)
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 backdrop-blur-[1px]">
                                                    <div class="w-10 h-10 rounded-full bg-white/90 dark:bg-white/95 flex items-center justify-center shadow-lg">
                                                        <svg class="w-6 h-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                                                 style="box-shadow: inset 0 0 30px rgba(255,255,255,0.15);"></div>

                                            <div class="absolute bottom-0 inset-x-0 px-3 py-2 bg-gradient-to-t from-black/60 to-transparent">
                                                <span class="text-xs font-semibold text-white drop-shadow-sm capitalize">{{ $key }}</span>
                                            </div>
                                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Live Preview Bar --}}
            @if($current)
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Preview</p>
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider">Button</span>
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-white text-xs font-bold shadow-md"
                                  style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Save Changes
                            </span>
                        </div>
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider">Badge</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold"
                                  style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }}20; color: {{ $hexColors[$current] ?? '#3b82f6' }};">
                                ● Active
                            </span>
                        </div>
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider">Navigation</span>
                            <div class="flex items-center gap-0.5 bg-gray-100 dark:bg-white/5 rounded-lg p-1">
                                <span class="px-3 py-1 rounded-md text-xs text-gray-500 dark:text-gray-400">Dashboard</span>
                                <span class="px-3 py-1 rounded-md text-xs font-semibold text-white shadow-sm"
                                      style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};">Analytics</span>
                                <span class="px-3 py-1 rounded-md text-xs text-gray-500 dark:text-gray-400">Reports</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-start gap-1 flex-1 min-w-[120px]">
                            <span class="text-[10px] text-gray-400 uppercase tracking-wider">Progress</span>
                            <div class="w-full h-2.5 rounded-full bg-gray-200 dark:bg-white/10 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" style="width: 72%; background-color: {{ $hexColors[$current] ?? '#3b82f6' }};"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="w-4 h-4 rounded-md shadow-sm" style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};"></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Current: <strong class="text-gray-700 dark:text-gray-200">{{ $labels[$current] ?? $current }}</strong>
                        </span>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check">
                Save Preferences
            </x-filament::button>
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