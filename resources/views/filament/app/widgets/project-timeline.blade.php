<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                    <x-heroicon-o-calendar-days class="w-4 h-4 text-white" />
                </div>
                <span class="text-base font-bold">Project Timeline</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Click a project to view details
        </x-slot>

        @if($projects->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <x-heroicon-o-calendar class="w-12 h-12 mx-auto mb-3 opacity-40" />
                <p>No projects with scheduled dates yet.</p>
            </div>
        @else
            <div class="timeline-container overflow-x-auto">
                {{-- Month Headers --}}
                <div class="flex border-b-2 border-gray-200 dark:border-gray-700 mb-2 min-w-[700px]">
                    @foreach($months as $month)
                        <div class="text-xs font-semibold px-1 py-2 text-center border-r border-gray-100 dark:border-gray-800 truncate
                                        {{ $month['isCurrent'] ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 font-bold rounded-t-lg' : 'text-gray-500 dark:text-gray-400' }}"
                            style="width: {{ $month['width'] }}%; min-width: 40px;">
                            {{ $month['short'] }}
                        </div>
                    @endforeach
                </div>

                {{-- Timeline Body --}}
                <div class="relative min-w-[700px]" style="min-height: {{ count($projects) * 72 + 20 }}px;">

                    {{-- Grid lines for months --}}
                    <div class="absolute inset-0 flex pointer-events-none">
                        @foreach($months as $month)
                            <div class="border-r border-gray-100 dark:border-gray-800/50 h-full {{ $month['isCurrent'] ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : '' }}"
                                style="width: {{ $month['width'] }}%;"></div>
                        @endforeach
                    </div>

                    {{-- Today marker --}}
                    @if($todayPercent !== null)
                        <div class="absolute top-0 bottom-0 z-10 pointer-events-none" style="left: {{ $todayPercent }}%;">
                            <div class="w-0.5 h-full" style="background: linear-gradient(to bottom, #ef4444, #f97316); opacity: 0.7;"></div>
                            <div class="absolute -top-0.5 -left-3 bg-gradient-to-r from-red-500 to-orange-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-lg shadow-red-500/30">
                                TODAY
                            </div>
                        </div>
                    @endif

                    {{-- Project Bars --}}
                    @foreach($projects as $index => $project)
                        @php
                            $barColors = [
                                'planning'  => ['from' => '#818cf8', 'to' => '#6366f1', 'bg' => 'rgba(99,102,241,0.08)', 'border' => 'rgba(99,102,241,0.25)', 'glow' => 'rgba(99,102,241,0.2)'],
                                'active'    => ['from' => '#34d399', 'to' => '#10b981', 'bg' => 'rgba(16,185,129,0.08)', 'border' => 'rgba(16,185,129,0.25)', 'glow' => 'rgba(16,185,129,0.2)'],
                                'on_hold'   => ['from' => '#fbbf24', 'to' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.08)', 'border' => 'rgba(245,158,11,0.25)', 'glow' => 'rgba(245,158,11,0.2)'],
                                'completed' => ['from' => '#9ca3af', 'to' => '#6b7280', 'bg' => 'rgba(107,114,128,0.06)', 'border' => 'rgba(107,114,128,0.2)', 'glow' => 'rgba(107,114,128,0.15)'],
                                'cancelled' => ['from' => '#f87171', 'to' => '#ef4444', 'bg' => 'rgba(239,68,68,0.06)', 'border' => 'rgba(239,68,68,0.2)', 'glow' => 'rgba(239,68,68,0.15)'],
                            ];
                            $colors = $barColors[$project['status']] ?? $barColors['planning'];
                        @endphp
                        <div class="absolute left-0 right-0" style="top: {{ $index * 72 + 10 }}px; height: 62px;">
                            {{-- Project Bar --}}
                            <a href="{{ $project['url'] }}" class="group absolute flex items-center rounded-xl shadow-sm border transition-all duration-300
                                               hover:shadow-lg hover:scale-[1.015] hover:z-20 cursor-pointer overflow-hidden"
                                style="left: {{ $project['leftPercent'] }}%;
                                               width: {{ $project['widthPercent'] }}%;
                                               min-width: 120px;
                                               height: 50px;
                                               border-color: {{ $colors['border'] }};
                                               background: {{ $colors['bg'] }};
                                               backdrop-filter: blur(4px);"
                                title="{{ $project['name'] }} — {{ $project['startDate'] }} to {{ $project['endDate'] }}">

                                {{-- Colored left edge accent --}}
                                <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-xl"
                                    style="background: linear-gradient(to bottom, {{ $colors['from'] }}, {{ $colors['to'] }});"></div>

                                {{-- Expected Progress marker (where it should be) --}}
                                @if($project['expectedProgress'] > 0 && $project['expectedProgress'] < 100)
                                    <div class="absolute inset-y-0 z-[5] pointer-events-none"
                                        style="left: {{ $project['expectedProgress'] }}%;">
                                        <div class="w-[2px] h-full border-l-2 border-dashed border-gray-400 dark:border-gray-500 opacity-50"></div>
                                        <div class="absolute -top-0.5 -left-[5px]">
                                            <div class="w-0 h-0 border-l-[5px] border-r-[5px] border-t-[5px] border-l-transparent border-r-transparent border-t-gray-400 dark:border-t-gray-500 opacity-70"></div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Actual Progress Fill with gradient --}}
                                <div class="absolute inset-y-0 left-0 rounded-l-xl transition-all duration-700 ease-out"
                                    style="width: {{ $project['progress'] }}%;
                                           background: linear-gradient(135deg, {{ $colors['from'] }}40, {{ $colors['to'] }}30);"></div>

                                {{-- Animated shimmer on active projects --}}
                                @if($project['status'] === 'active')
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                                        style="animation: shimmer 3s ease-in-out infinite;"></div>
                                @endif

                                {{-- Content --}}
                                <div class="relative flex items-center justify-between w-full px-3 py-1.5 z-10">
                                    <div class="flex items-center gap-2 min-w-0">
                                        {{-- Status indicator --}}
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full flex-shrink-0 shadow-sm"
                                            style="background: linear-gradient(135deg, {{ $colors['from'] }}, {{ $colors['to'] }});">
                                            @if($project['status'] === 'completed')
                                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @elseif($project['status'] === 'active')
                                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                                            @elseif($project['status'] === 'on_hold')
                                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
                                            @elseif($project['status'] === 'planning')
                                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                            @else
                                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                            @endif
                                        </span>
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-gray-900 dark:text-gray-100 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ $project['name'] }}
                                            </div>
                                            <div class="text-[10px] text-gray-500 dark:text-gray-400 truncate flex items-center gap-1">
                                                <span class="inline-block w-1.5 h-1.5 rounded-full" style="background: {{ $colors['to'] }};"></span>
                                                {{ $project['statusLabel'] }}
                                                @if($project['budget'])
                                                    · {{ $project['budget'] }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Progress + Variance --}}
                                    <div class="flex items-center gap-2 ml-2 flex-shrink-0">
                                        <div class="text-right leading-tight">
                                            <div class="text-sm font-extrabold" style="color: {{ $colors['to'] }};">
                                                {{ $project['progress'] }}%
                                            </div>
                                            <div class="text-[9px] font-bold px-1.5 py-0.5 rounded-full whitespace-nowrap"
                                                style="color: {{ $project['varianceColor'] }};
                                                       background: {{ $project['varianceColor'] }}15;">
                                                {{ $project['varianceLabel'] }}
                                            </div>
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
                                        <div class="hidden group-hover/ms:block absolute bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 dark:bg-gray-700 text-white text-[10px] px-2.5 py-1.5 rounded-lg shadow-xl whitespace-nowrap z-50 pointer-events-none border border-gray-700">
                                            <div class="font-bold">{{ $milestone['name'] }}</div>
                                            <div class="text-gray-300">{{ $milestone['date'] }}</div>
                                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 dark:bg-gray-700 rotate-45 border-r border-b border-gray-700"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Legend --}}
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-5 pt-4 border-t border-gray-200 dark:border-gray-700/50 text-[11px] text-gray-600 dark:text-gray-400">
                    <span class="font-semibold text-gray-800 dark:text-gray-300 mr-1">Legend:</span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-5 h-2.5 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 inline-block shadow-sm"></span>
                        Active
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-5 h-2.5 rounded-full bg-gradient-to-r from-indigo-400 to-indigo-600 inline-block shadow-sm"></span>
                        Planning
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-5 h-2.5 rounded-full bg-gradient-to-r from-amber-400 to-amber-600 inline-block shadow-sm"></span>
                        On Hold
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-5 h-2.5 rounded-full bg-gradient-to-r from-gray-400 to-gray-600 inline-block shadow-sm"></span>
                        Completed
                    </span>
                    <span class="flex items-center gap-1.5 border-l border-gray-200 dark:border-gray-700 pl-4">
                        <span class="w-4 border-t-2 border-dashed border-gray-400 inline-block"></span>
                        Expected
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-4 h-0.5 bg-gradient-to-r from-red-500 to-orange-500 inline-block rounded-full"></span>
                        Today
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-gray-400 inline-flex items-center justify-center text-white text-[7px] shadow-sm">◇</span>
                        Milestone
                    </span>
                </div>
            </div>
        @endif
    </x-filament::section>

    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</x-filament-widgets::widget>