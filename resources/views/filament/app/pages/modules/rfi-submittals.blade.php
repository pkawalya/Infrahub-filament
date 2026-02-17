<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    {{-- Tab switcher --}}
    <div class="flex gap-2 mb-4">
        <button wire:click="$set('activeTab', 'rfis')"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                {{ $this->activeTab === 'rfis'
    ? 'bg-primary-600 text-white shadow-md'
    : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' }}">
            <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                RFIs
                @php $rfiCount = $this->record->rfis()->count() @endphp
                @if($rfiCount > 0)
                    <span
                        class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-semibold rounded-full
                            {{ $this->activeTab === 'rfis' ? 'bg-white/20 text-white' : 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' }}">
                        {{ $rfiCount }}
                    </span>
                @endif
            </span>
        </button>

        <button wire:click="$set('activeTab', 'submittals')"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                {{ $this->activeTab === 'submittals'
    ? 'bg-primary-600 text-white shadow-md'
    : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' }}">
            <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Submittals
                @php $subCount = $this->record->submittals()->count() @endphp
                @if($subCount > 0)
                    <span
                        class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-semibold rounded-full
                            {{ $this->activeTab === 'submittals' ? 'bg-white/20 text-white' : 'bg-info-100 text-info-700 dark:bg-info-900 dark:text-info-300' }}">
                        {{ $subCount }}
                    </span>
                @endif
            </span>
        </button>
    </div>

    {{ $this->table }}
</x-filament-panels::page>