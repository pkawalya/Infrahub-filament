<x-filament-widgets::widget>
    <div x-data="{ fullscreen: false }" x-on:keydown.escape.window="fullscreen = false"
        :class="fullscreen ? 'fixed inset-0 z-[9999] bg-white dark:bg-gray-900 overflow-auto p-4' : ''">

        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                        <x-heroicon-o-calendar-days class="w-4 h-4 text-white" />
                    </div>
                    <span class="text-base font-bold">Project Timeline</span>
                </div>
            </x-slot>

            <x-slot name="description">
                <div class="flex items-center justify-between">
                    <span>Click a project to view details</span>
                    <button type="button" x-on:click="fullscreen = !fullscreen" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border transition-all duration-200
                           border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400
                           hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200
                           dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400 dark:hover:border-indigo-700">
                        <template x-if="!fullscreen">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                            </svg>
                        </template>
                        <template x-if="fullscreen">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                            </svg>
                        </template>
                        <span x-text="fullscreen ? 'Exit Fullscreen' : 'Fullscreen'"></span>
                    </button>
                </div>
            </x-slot>

            @if($projects->isEmpty())
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-calendar class="w-12 h-12 mx-auto mb-3 opacity-40" />
                    <p>No projects with scheduled dates yet.</p>
                </div>
            @else
                <div class="timeline-container overflow-x-auto relative isolate">
                    @php
                        $monthCount = count($months);
                        $minTotalWidth = max(700, $monthCount * 40); // ensure enough space
                    @endphp

                    {{-- Month Headers --}}
                    <div class="flex border-b-2 border-gray-200 dark:border-gray-700 mb-2"
                        style="min-width: {{ $minTotalWidth }}px;">
                        @foreach($months as $month)
                            <div class="text-xs font-semibold px-1 py-2 text-center border-r border-gray-100 dark:border-gray-800 truncate
                                                                                        {{ $month['isCurrent'] ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 font-bold rounded-t-lg' : 'text-gray-500 dark:text-gray-400' }}"
                                style="width: {{ $month['width'] }}%;">
                                {{ $month['short'] }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Timeline Body --}}
                    <div class="relative"
                        style="min-width: {{ $minTotalWidth }}px; min-height: {{ count($projects) * 90 + 30 }}px;">

                        {{-- Grid lines for months --}}
                        <div class="absolute inset-0 flex pointer-events-none">
                            @foreach($months as $month)
                                <div class="border-r border-gray-100 dark:border-gray-800/50 h-full {{ $month['isCurrent'] ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : '' }}"
                                    style="width: {{ $month['width'] }}%;"></div>
                            @endforeach
                        </div>

                        {{-- Today marker --}}
                        @if($todayPercent !== null)
                            <div class="absolute top-0 bottom-0 z-30 pointer-events-none" style="left: {{ $todayPercent }}%;">
                                <div class="h-full -left-px"
                                    style="width: 2px; background: repeating-linear-gradient(to bottom, #2563eb 0px, #2563eb 6px, transparent 6px, transparent 10px);">
                                </div>
                                <div class="absolute -top-1 -left-[22px] text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-lg"
                                    style="background: #2563eb; box-shadow: 0 2px 8px rgba(37,99,235,0.4);">
                                    TODAY
                                </div>
                            </div>
                        @endif

                        {{-- Project Bars --}}
                        @foreach($projects as $index => $project)
                            @php
                                $barColors = [
                                    'planning' => ['from' => '#818cf8', 'to' => '#6366f1', 'bg' => 'rgba(99,102,241,0.08)', 'border' => 'rgba(99,102,241,0.25)', 'glow' => 'rgba(99,102,241,0.2)'],
                                    'active' => ['from' => '#34d399', 'to' => '#10b981', 'bg' => 'rgba(16,185,129,0.08)', 'border' => 'rgba(16,185,129,0.25)', 'glow' => 'rgba(16,185,129,0.2)'],
                                    'on_hold' => ['from' => '#fbbf24', 'to' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.08)', 'border' => 'rgba(245,158,11,0.25)', 'glow' => 'rgba(245,158,11,0.2)'],
                                    'completed' => ['from' => '#9ca3af', 'to' => '#6b7280', 'bg' => 'rgba(107,114,128,0.06)', 'border' => 'rgba(107,114,128,0.2)', 'glow' => 'rgba(107,114,128,0.15)'],
                                    'cancelled' => ['from' => '#f87171', 'to' => '#ef4444', 'bg' => 'rgba(239,68,68,0.06)', 'border' => 'rgba(239,68,68,0.2)', 'glow' => 'rgba(239,68,68,0.15)'],
                                ];
                                $colors = $barColors[$project['status']] ?? $barColors['planning'];
                            @endphp
                            <div class="absolute left-0 right-0" style="top: {{ $index * 90 + 10 }}px; height: 80px;">

                                @php
                                    $actualPct = $project['progress'];
                                    $expectedPct = $project['expectedProgress'];
                                    $isDone = $project['status'] === 'completed';
                                    $isCancelled = $project['status'] === 'cancelled';
                                @endphp

                                {{-- Annotation labels above the bar --}}
                                <div class="absolute left-0 right-0"
                                    style="left: {{ $project['leftPercent'] }}%; width: {{ $project['widthPercent'] }}%; min-width: 140px; top: 0; height: 24px;">
                                    {{-- "% Done" label at actual progress boundary --}}
                                    @if($actualPct > 0 && $actualPct < 100)
                                        <div class="absolute flex flex-col items-center pointer-events-none"
                                            style="left: {{ $actualPct }}%; transform: translateX(-50%);">
                                            <span class="text-[9px] font-bold whitespace-nowrap px-1 rounded"
                                                style="color: #22a34d; background: rgba(34,163,77,0.08);">
                                                {{ $actualPct }}% Completed
                                            </span>
                                            <span class="w-px h-1.5" style="background: #22a34d;"></span>
                                        </div>
                                    @endif

                                    {{-- "% Time" label at expected progress red line --}}
                                    @if($expectedPct > 0 && $expectedPct < 100 && !$isDone && !$isCancelled)
                                        @php $labelsClose = abs($expectedPct - $actualPct) < 12; @endphp
                                        <div class="absolute flex flex-col items-center pointer-events-none"
                                            style="left: {{ $expectedPct }}%; transform: translateX(-50%); {{ $labelsClose ? 'top: -12px;' : '' }}">
                                            <span class="text-[9px] font-bold whitespace-nowrap px-1 rounded"
                                                style="color: #ef4444; background: rgba(239,68,68,0.08);">
                                                {{ $expectedPct }}% Time spent
                                            </span>
                                            <span class="w-px"
                                                style="background: #ef4444; height: {{ $labelsClose ? '8px' : '6px' }};"></span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Project Bar --}}
                                <a href="{{ $project['url'] }}"
                                    class="group absolute flex items-center rounded-lg shadow-sm border transition-all duration-300
                                                                                               hover:shadow-xl hover:scale-[1.01] hover:z-30 cursor-pointer overflow-visible"
                                    style="left: {{ $project['leftPercent'] }}%;
                                                                                               width: {{ $project['widthPercent'] }}%;
                                                                                               min-width: 140px;
                                                                                               top: 26px;
                                                                                               height: 50px;
                                                                                               border-color: {{ $colors['border'] }};
                                                                                               background: white;">

                                    {{-- Bar fills (clipped) --}}
                                    <div class="absolute inset-0 rounded-lg overflow-hidden pointer-events-none">
                                        {{-- Brown/orange full bar --}}
                                        @if(!$isDone && !$isCancelled)
                                            <div class="absolute inset-y-0 left-0 right-0" style="background: #c2710c;"></div>
                                        @endif

                                        {{-- Green progress fill --}}
                                        <div class="absolute inset-y-0 left-0 transition-all duration-700 ease-out"
                                            style="width: {{ $actualPct }}%;
                                                                                               background: {{ $isDone ? '#6b7280' : '#22a34d' }};">
                                        </div>
                                    </div>

                                    {{-- Red vertical line --}}
                                    @if($expectedPct > 0 && $expectedPct < 100 && !$isDone && !$isCancelled)
                                        <div class="absolute inset-y-0 z-[5] pointer-events-none"
                                            style="left: {{ $expectedPct }}%;">
                                            <div class="absolute top-0 bottom-0"
                                                style="width: 3px; background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,0.6);">
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Content --}}
                                    <div class="relative flex items-center justify-between w-full px-3 py-1.5 z-10">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span
                                                class="inline-flex items-center justify-center w-6 h-6 rounded-full flex-shrink-0 shadow-sm"
                                                style="background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.4);">
                                                @if($project['status'] === 'completed')
                                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @elseif($project['status'] === 'active')
                                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                                    </svg>
                                                @elseif($project['status'] === 'on_hold')
                                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                                                    </svg>
                                                @elseif($project['status'] === 'planning')
                                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18 18 6M6 6l12 12" />
                                                    </svg>
                                                @endif
                                            </span>
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-white truncate"
                                                    style="text-shadow: 0 1px 3px rgba(0,0,0,0.4);">
                                                    {{ $project['name'] }}
                                                </div>
                                                <div class="text-[10px] text-white/80 truncate flex items-center gap-1"
                                                    style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                                    <span class="truncate">
                                                        {{ $project['statusLabel'] }}
                                                        @if($project['budget'])
                                                            · {{ $project['budget'] }}
                                                        @endif
                                                        · {{ $project['startDate'] }} to {{ $project['endDate'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 ml-2 flex-shrink-0">
                                            <div class="text-right leading-tight">
                                                <div class="text-sm font-extrabold text-white"
                                                    style="text-shadow: 0 1px 3px rgba(0,0,0,0.4);">
                                                    {{ $project['progress'] }}%
                                                </div>
                                                <div class="text-[9px] font-bold px-1.5 py-0.5 rounded-full whitespace-nowrap"
                                                    style="color: white; background: rgba(0,0,0,0.25);">
                                                    {{ $project['varianceLabel'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- HOVER POPOVER --}}
                                    @php
                                        $mTotal = count($project['milestones']);
                                        $mDone = collect($project['milestones'])->where('status', 'completed')->count();
                                        $variance = $actualPct - $expectedPct;
                                    @endphp
                                    <div
                                        class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 pointer-events-none">
                                        <div
                                            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-3 text-left">
                                            {{-- Arrow --}}
                                            <div
                                                class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white dark:bg-gray-800 border-l border-t border-gray-200 dark:border-gray-700 rotate-45">
                                            </div>

                                            <div class="text-sm font-bold text-gray-900 dark:text-white mb-2">
                                                {{ $project['name'] }}
                                            </div>

                                            <div class="space-y-1.5 text-[11px]">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Status</span>
                                                    <span class="font-semibold"
                                                        style="color: {{ $colors['to'] }};">{{ $project['statusLabel'] }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Duration</span>
                                                    <span
                                                        class="font-medium text-gray-700 dark:text-gray-300">{{ $project['startDate'] }}
                                                        → {{ $project['endDate'] }}</span>
                                                </div>
                                                @if($project['budget'])
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Budget</span>
                                                        <span
                                                            class="font-semibold text-gray-700 dark:text-gray-300">{{ $project['budget'] }}</span>
                                                    </div>
                                                @endif

                                                <div class="border-t border-gray-100 dark:border-gray-700 pt-1.5 mt-1.5"></div>

                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-500">Completed</span>
                                                    <span class="font-bold" style="color: #22a34d;">{{ $actualPct }}%</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-500">Time Spent</span>
                                                    <span class="font-bold" style="color: #ef4444;">{{ $expectedPct }}%</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-500">Variance</span>
                                                    <span class="font-bold px-1.5 py-0.5 rounded text-[10px]"
                                                        style="color: white; background: {{ $project['varianceColor'] }};">
                                                        @if($variance > 0) ▲ {{ abs($variance) }}% Ahead
                                                        @elseif($variance < 0) ▼ {{ abs($variance) }}% Behind
                                                        @else ● On Track
                                                        @endif
                                                    </span>
                                                </div>

                                                @if($mTotal > 0)
                                                    <div class="border-t border-gray-100 dark:border-gray-700 pt-1.5 mt-1.5"></div>
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-gray-500">Milestones</span>
                                                        <span
                                                            class="font-medium text-gray-700 dark:text-gray-300">{{ $mDone }}/{{ $mTotal }}
                                                            completed</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Milestone markers --}}
                                    @foreach($project['milestones'] as $milestone)
                                        @php
                                            $relPos = $project['widthPercent'] > 0
                                                ? (($milestone['position'] - $project['leftPercent']) / $project['widthPercent']) * 100
                                                : 0;
                                            $relPos = max(0, min(100, $relPos));

                                            $mColor = match ($milestone['status']) {
                                                'completed' => '#10b981',
                                                'in_progress' => '#3b82f6',
                                                default => $milestone['isOverdue'] ? '#ef4444' : '#9ca3af',
                                            };

                                            $mIcon = match ($milestone['status']) {
                                                'completed' => '✓',
                                                'in_progress' => '●',
                                                default => $milestone['isOverdue'] ? '!' : '◇',
                                            };
                                        @endphp
                                        <div class="absolute z-10 group/ms" style="left: {{ $relPos }}%; bottom: -3px;"
                                            title="{{ $milestone['name'] }} — {{ $milestone['date'] }} ({{ $milestone['status'] }})">
                                            <div class="w-4 h-4 -ml-2 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center text-white shadow-md transition-transform hover:scale-150"
                                                style="background-color: {{ $mColor }}; font-size: 7px; box-shadow: 0 0 6px {{ $mColor }}40;">
                                                {{ $mIcon }}
                                            </div>
                                            {{-- Tooltip --}}
                                            <div
                                                class="hidden group-hover/ms:block absolute bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 dark:bg-gray-700 text-white text-[10px] px-2.5 py-1.5 rounded-lg shadow-xl whitespace-nowrap z-50 pointer-events-none border border-gray-700">
                                                <div class="font-bold">{{ $milestone['name'] }}</div>
                                                <div class="text-gray-300">{{ $milestone['date'] }}</div>
                                                <div
                                                    class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 dark:bg-gray-700 rotate-45 border-r border-b border-gray-700">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Legend --}}
                    <div
                        class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-5 pt-4 border-t border-gray-200 dark:border-gray-700/50 text-[11px] text-gray-600 dark:text-gray-400">
                        <span class="font-semibold text-gray-800 dark:text-gray-300 mr-1">Legend:</span>
                        <span class="flex items-center gap-1.5">
                            <span
                                class="w-5 h-2.5 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 inline-block shadow-sm"></span>
                            Active
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span
                                class="w-5 h-2.5 rounded-full bg-gradient-to-r from-indigo-400 to-indigo-600 inline-block shadow-sm"></span>
                            Planning
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span
                                class="w-5 h-2.5 rounded-full bg-gradient-to-r from-amber-400 to-amber-600 inline-block shadow-sm"></span>
                            On Hold
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span
                                class="w-5 h-2.5 rounded-full bg-gradient-to-r from-gray-400 to-gray-600 inline-block shadow-sm"></span>
                            Completed
                        </span>
                        <span class="flex items-center gap-1.5 border-l border-gray-200 dark:border-gray-700 pl-4">
                            <span class="w-4 h-0.5 inline-block rounded-full" style="background: #ef4444;"></span>
                            Expected
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-5 h-2.5 rounded-sm inline-block"
                                style="background: rgba(194,65,12,0.18); border: 1px solid rgba(194,65,12,0.3);"></span>
                            Behind
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-4 h-0.5 inline-block rounded-full" style="background: #2563eb;"></span>
                            Today
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span
                                class="w-3 h-3 rounded-full bg-gray-400 inline-flex items-center justify-center text-white text-[7px] shadow-sm">◇</span>
                            Milestone
                        </span>
                    </div>
                </div>
            @endif
        </x-filament::section>

        <style>
            @keyframes shimmer {
                0% {
                    transform: translateX(-100%);
                }

                100% {
                    transform: translateX(100%);
                }
            }
        </style>

    </div>
</x-filament-widgets::widget>