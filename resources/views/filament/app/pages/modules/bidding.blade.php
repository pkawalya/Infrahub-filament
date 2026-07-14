<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    {{-- Tab switcher --}}
    <div class="flex items-center gap-1 mt-4 mb-2 border-b border-gray-200 dark:border-gray-700">
        @foreach(['tenders' => 'Tenders', 'bids' => 'Bids'] as $key => $label)
            <button
                wire:click="$set('activeTab', '{{ $key }}')"
                class="px-4 py-2 text-sm font-semibold border-b-2 transition-colors
                    {{ $this->activeTab === $key
                        ? 'border-primary-600 text-primary-600 dark:text-primary-400'
                        : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
