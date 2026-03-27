<x-filament-panels::page>
    @php
        $items      = $this->getTrashedItems();
        $counts     = $this->getTypeCounts();
        $registry   = \App\Filament\App\Pages\CompanyTrash::modelRegistry();
        $totalCount = $counts['all'] ?? 0;
    @endphp

    {{-- ── Stats Bar ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                <x-heroicon-o-trash class="w-5 h-5 text-red-600 dark:text-red-400" />
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalCount }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Total In Trash</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                <x-heroicon-o-archive-box class="w-5 h-5 text-amber-600 dark:text-amber-400" />
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ count(array_filter($counts, fn($c, $k) => $k !== 'all' && $c > 0, ARRAY_FILTER_USE_BOTH)) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Categories Affected</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                <x-heroicon-o-arrow-uturn-left class="w-5 h-5 text-green-600 dark:text-green-400" />
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $items->count() }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Visible / Filtered</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <x-heroicon-o-clock class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $items->isNotEmpty() ? $items->first()['deleted_at']?->diffForHumans() : '—' }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Last Deleted</div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        {{-- ── Search + Type Tabs ── --}}
        <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
            {{-- Search --}}
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400" />
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search trash..."
                    class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                />
            </div>

            {{-- Type filter pills --}}
            <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto pr-1">
                <button
                    wire:click="$set('activeType', 'all')"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium transition-colors {{ $this->activeType === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                >
                    All
                    @if(($counts['all'] ?? 0) > 0)
                        <span class="bg-white/20 rounded-full px-1">{{ $counts['all'] }}</span>
                    @endif
                </button>
                @foreach($registry as $typeKey => $meta)
                    @if(($counts[$typeKey] ?? 0) > 0)
                        <button
                            wire:click="$set('activeType', '{{ $typeKey }}')"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium transition-colors {{ $this->activeType === $typeKey ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                        >
                            {{ $meta['label'] }}
                            <span class="{{ $this->activeType === $typeKey ? 'bg-white/20' : 'bg-gray-200 dark:bg-gray-600' }} rounded-full px-1">{{ $counts[$typeKey] }}</span>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- ── Items List ── --}}
        @if($items->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    <x-heroicon-o-trash class="w-10 h-10 text-gray-300 dark:text-gray-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Trash is empty</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if($this->search !== '')
                        No trashed records match "{{ $this->search }}"
                    @else
                        Deleted records will appear here and can be restored or permanently removed.
                    @endif
                </p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($items as $item)
                    <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors group"
                         wire:key="trash-{{ $item['type_key'] }}-{{ $item['id'] }}">

                        {{-- Icon --}}
                        <div class="w-9 h-9 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0">
                            @svg($item['icon'], 'w-4 h-4 text-red-500 dark:text-red-400')
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $item['label'] }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                    {{ $item['type_label'] }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                Deleted {{ $item['deleted_at']?->diffForHumans() ?? '—' }}
                                &nbsp;·&nbsp; {{ $item['deleted_at']?->format('M d, Y H:i') }}
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                            <button
                                wire:click="restore('{{ $item['type_key'] }}', {{ $item['id'] }})"
                                wire:loading.attr="disabled"
                                title="Restore"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors border border-green-200 dark:border-green-800"
                            >
                                <x-heroicon-o-arrow-uturn-left class="w-3.5 h-3.5" />
                                Restore
                            </button>
                            <button
                                wire:click="forceDelete('{{ $item['type_key'] }}', {{ $item['id'] }})"
                                wire:confirm="Permanently delete this {{ $item['type_label'] }}? This cannot be undone."
                                title="Delete permanently"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-200 dark:border-red-800"
                            >
                                <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── Footer count ── --}}
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/40">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Showing {{ $items->count() }} {{ Str::plural('record', $items->count()) }}
                    @if($this->activeType !== 'all') in {{ $registry[$this->activeType]['label'] ?? '' }} @endif
                    @if($this->search !== '') matching "{{ $this->search }}" @endif
                </span>
            </div>
        @endif
    </div>
</x-filament-panels::page>
