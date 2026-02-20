<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

        {{-- Projects Card --}}
        <div
            class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-indigo-100 dark:border-indigo-900/50 shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10 group">
            <div
                class="absolute right-0 top-0 -mt-4 -mr-4 w-24 h-24 rounded-full bg-indigo-50 dark:bg-indigo-900/20 opacity-50 blur-2xl group-hover:scale-150 transition-transform duration-500">
            </div>
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-indigo-500 to-purple-600"></div>

            <div class="p-5">
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">Active Projects</h3>
                    <div
                        class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shadow-inner text-indigo-600 dark:text-indigo-400">
                        <x-heroicon-o-building-office class="w-5 h-5" />
                    </div>
                </div>

                <div class="flex items-end gap-2 relative z-10">
                    <div class="text-3xl font-black tracking-tight text-gray-900 dark:text-white leading-none">
                        {{ $stats['projects']['active'] }}
                    </div>
                    <div class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-1">
                        / {{ $stats['projects']['total'] }} total
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 relative z-10">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        {{ $stats['projects']['active'] }} projects currently in progress
                    </p>
                </div>
            </div>
        </div>

        {{-- Tasks Card --}}
        <div
            class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border {{ $stats['tasks']['overdue'] > 0 ? 'border-red-100 dark:border-red-900/50 hover:shadow-red-500/10' : 'border-emerald-100 dark:border-emerald-900/50 hover:shadow-emerald-500/10' }} shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl group">
            
            @if($stats['tasks']['overdue'] > 0)
                <div class="absolute right-0 top-0 -mt-4 -mr-4 w-24 h-24 rounded-full bg-red-50 dark:bg-red-900/20 opacity-50 blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            @else
                <div class="absolute right-0 top-0 -mt-4 -mr-4 w-24 h-24 rounded-full bg-emerald-50 dark:bg-emerald-900/20 opacity-50 blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            @endif
            
            <div
                class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b {{ $stats['tasks']['overdue'] > 0 ? 'from-red-500 to-orange-500' : 'from-emerald-400 to-teal-600' }}">
            </div>

            <div class="p-5">
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">Open Tasks</h3>
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center shadow-inner {{ $stats['tasks']['overdue'] > 0 ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' }}">
                        @if($stats['tasks']['overdue'] > 0)
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                        @else
                            <x-heroicon-o-check-circle class="w-5 h-5" />
                        @endif
                    </div>
                </div>

                <div class="flex items-end gap-2 relative z-10">
                    <div class="text-3xl font-black tracking-tight text-gray-900 dark:text-white leading-none">
                        {{ $stats['tasks']['open'] }}
                    </div>
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">
                        tasks pending
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 relative z-10">
                    <p
                        class="text-xs font-medium flex items-center gap-1.5 {{ $stats['tasks']['overdue'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                        @if($stats['tasks']['overdue'] > 0)
                            <span
                                class="inline-flex py-0.5 px-1.5 rounded-md bg-red-100 dark:bg-red-900/40 font-bold">{{ $stats['tasks']['overdue'] }}
                                overdue</span> — action needed
                        @else
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> ✓ All tasks on track
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Work Orders Card --}}
        <div
            class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-sky-100 dark:border-sky-900/50 shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-sky-500/10 group">
            <div
                class="absolute right-0 top-0 -mt-4 -mr-4 w-24 h-24 rounded-full bg-sky-50 dark:bg-sky-900/20 opacity-50 blur-2xl group-hover:scale-150 transition-transform duration-500">
            </div>
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-sky-400 to-blue-600"></div>

            <div class="p-5">
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">Work Orders</h3>
                    <div
                        class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center shadow-inner text-sky-600 dark:text-sky-400">
                        <x-heroicon-o-wrench-screwdriver class="w-5 h-5" />
                    </div>
                </div>

                <div class="flex items-end gap-2 relative z-10">
                    <div class="text-3xl font-black tracking-tight text-gray-900 dark:text-white leading-none">
                        {{ $stats['workOrders']['open'] }}
                    </div>
                    <div class="text-xs font-semibold text-sky-600 dark:text-sky-400 mb-1">
                        open WOs
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 relative z-10">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <span
                            class="inline-flex py-0.5 px-1.5 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">{{ $stats['workOrders']['completed'] }}</span>
                        completed this month
                    </p>
                </div>
            </div>
        </div>

        {{-- Revenue Card --}}
        <div
            class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-amber-100 dark:border-amber-900/50 shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/10 group">
            <div
                class="absolute right-0 top-0 -mt-4 -mr-4 w-24 h-24 rounded-full bg-amber-50 dark:bg-amber-900/20 opacity-50 blur-2xl group-hover:scale-150 transition-transform duration-500">
            </div>
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-amber-400 to-orange-500"></div>

            <div class="p-5">
                <div class="flex items-center justify-between mb-4 relative z-10">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">Revenue (MTD)</h3>
                    <div
                        class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center shadow-inner text-amber-600 dark:text-amber-400">
                        <x-heroicon-o-banknotes class="w-5 h-5" />
                    </div>
                </div>

                <div class="relative z-10">
                    <div
                        class="text-3xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600 leading-none">
                        {{ $stats['revenue']['current'] }}
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 relative z-10">
                    <p
                        class="text-xs font-bold flex items-center gap-1.5 {{ $stats['revenue']['change'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                        @if($stats['revenue']['change'] >= 0)
                            <x-heroicon-m-arrow-trending-up class="w-4 h-4" />
                            <span>+{{ $stats['revenue']['change'] }}%</span>
                        @else
                            <x-heroicon-m-arrow-trending-down class="w-4 h-4" />
                            <span>{{ $stats['revenue']['change'] }}%</span>
                        @endif
                        <span class="text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap">vs last
                            month</span>
                    </p>
                </div>
            </div>
        </div>

    </div>
</x-filament-widgets::widget>