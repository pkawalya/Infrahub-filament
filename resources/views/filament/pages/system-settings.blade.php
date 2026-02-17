<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        {{-- Color Preview Swatches --}}
        <div class="mt-4 rounded-xl border border-gray-200 dark:border-white/10 p-5 bg-white dark:bg-white/5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">🎨 Color Preview</h3>
            <div class="grid grid-cols-6 sm:grid-cols-9 gap-3">
                @php
                    $hexColors = \App\Support\ColorPalette::hex();
                    $labels = \App\Support\ColorPalette::options();
                    $current = $this->data['panel_color'] ?? 'blue';
                @endphp
                @foreach($hexColors as $key => $hex)
                    <button type="button" wire:click="$set('data.panel_color', '{{ $key }}')"
                        title="{{ strip_tags($labels[$key] ?? $key) }}"
                        class="group relative flex flex-col items-center gap-1.5">
                        <div class="w-10 h-10 rounded-xl shadow-sm transition-all duration-200 group-hover:scale-110 group-hover:shadow-lg ring-offset-2 dark:ring-offset-gray-900 {{ $current === $key ? 'ring-2 ring-primary-500 scale-110 shadow-lg' : 'ring-1 ring-gray-200 dark:ring-white/10' }}"
                            style="background-color: {{ $hex }};"></div>
                        <span
                            class="text-[10px] font-medium {{ $current === $key ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500' }} capitalize truncate max-w-[48px]">
                            {{ $key }}
                        </span>
                    </button>
                @endforeach
            </div>

            @if($current)
                <div class="mt-4 flex items-center gap-3 pt-3 border-t border-gray-100 dark:border-white/5">
                    <div class="w-6 h-6 rounded-lg shadow-sm"
                        style="background-color: {{ $hexColors[$current] ?? '#3b82f6' }};"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Active: <strong class="text-gray-800 dark:text-white">{{ $labels[$current] ?? $current }}</strong>
                    </span>
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
        });
    </script>
</x-filament-panels::page>