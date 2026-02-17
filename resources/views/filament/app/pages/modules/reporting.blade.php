<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    <x-filament::section icon="heroicon-o-chart-bar" icon-color="primary">
        <x-slot name="heading">Reporting & Dashboards</x-slot>
        <x-slot name="description">Analytics, custom reports & project dashboards.</x-slot>
    </x-filament::section>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
        <x-filament::section icon="heroicon-o-document-chart-bar" icon-color="info">
            <x-slot name="heading">Custom Reports</x-slot>
            <x-slot name="description">Build & export reports</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-presentation-chart-line" icon-color="warning">
            <x-slot name="heading">Dashboards</x-slot>
            <x-slot name="description">Real-time project dashboards</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-table-cells" icon-color="success">
            <x-slot name="heading">Data Export</x-slot>
            <x-slot name="description">Export data to CSV & PDF</x-slot>
        </x-filament::section>
    </div>

    {{-- Activity Log / Audit Trail --}}
    <x-filament::section icon="heroicon-o-clock" icon-color="gray" collapsible>
        <x-slot name="heading">Activity Log</x-slot>
        <x-slot name="description">Recent actions across all project modules</x-slot>

        @php $logs = $this->getActivityLogs(); @endphp

        @if($logs->isEmpty())
            <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-medium">No activity recorded yet</p>
                <p class="text-xs mt-1">Activity will appear here as team members work on this project.</p>
            </div>
        @else
            <div class="relative">
                {{-- Timeline line --}}
                <div class="absolute left-5 top-0 bottom-0 w-px bg-gray-200 dark:bg-gray-700"></div>

                <div class="space-y-1">
                    @foreach($logs as $log)
                        @php
                            $actionColors = [
                                'created' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'updated' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                                'deleted' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                                'status_changed' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
                                'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'submitted' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
                                'commented' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                            ];
                            $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400';
                            $actionIcons = [
                                'created' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />',
                                'updated' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />',
                                'deleted' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />',
                                'status_changed' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />',
                                'approved' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                                'rejected' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                            ];
                            $iconPath = $actionIcons[$log->action] ?? '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        @endphp
                        <div class="relative pl-12 py-2.5 group">
                            {{-- Dot on timeline --}}
                            <div
                                class="absolute left-3 top-3.5 w-4 h-4 rounded-full {{ $colorClass }} flex items-center justify-center ring-2 ring-white dark:ring-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    {!! $iconPath !!}
                                </svg>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        <span class="font-medium">{{ $log->user?->name ?? 'System' }}</span>
                                        <span class="text-gray-500 dark:text-gray-400">{{ $log->description }}</span>
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ $log->created_at->diffForHumans() }}
                                        @if($log->ip_address)
                                            · {{ $log->ip_address }}
                                        @endif
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-semibold uppercase tracking-wider {{ $colorClass }} shrink-0">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>