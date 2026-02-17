<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-calendar-days class="w-5 h-5 text-primary-500" />
                <span>Project Timeline</span>
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
                <div class="flex border-b border-gray-200 dark:border-gray-700 mb-1 min-w-[700px]">
                    @foreach($months as $month)
                        <div
                            class="text-xs font-medium px-1 py-1.5 text-center border-r border-gray-100 dark:border-gray-800 truncate
                                {{ $month['isCurrent'] ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 font-bold' : 'text-gray-500 dark:text-gray-400' }}"
                            style="width: {{ $month['width'] }}%; min-width: 40px;"
                        >
                            {{ $month['short'] }}
                        </div>
                    @endforeach
                </div>

                {{-- Timeline Body --}}
                <div class="relative min-w-[700px]" style="min-height: {{ count($projects) * 68 + 20 }}px;">

                    {{-- Grid lines for months --}}
                    <div class="absolute inset-0 flex pointer-events-none">
                        @foreach($months as $month)
                            <div
                                class="border-r border-gray-100 dark:border-gray-800 h-full"
                                style="width: {{ $month['width'] }}%;"
                            ></div>
                        @endforeach
                    </div>

                    {{-- Today marker --}}
                    @if($todayPercent !== null)
                        <div
                            class="absolute top-0 bottom-0 z-10 pointer-events-none"
                            style="left: {{ $todayPercent }}%;"
                        >
                            <div class="w-0.5 h-full bg-red-500 opacity-60"></div>
                            <div class="absolute -top-0.5 -left-2.5 bg-red-500 text-white text-[9px] font-bold px-1 rounded shadow">
                                TODAY
                            </div>
                        </div>
                    @endif

                    {{-- Project Bars --}}
                    @foreach($projects as $index => $project)
                        <div
                            class="absolute left-0 right-0"
                            style="top: {{ $index * 68 + 10 }}px; height: 58px;"
                        >
                            {{-- Project Label --}}
                            <a
                                href="{{ $project['url'] }}"
                                class="group absolute flex items-center rounded-lg shadow-sm border transition-all duration-200
                                       hover:shadow-md hover:scale-[1.01] hover:z-20 cursor-pointer overflow-hidden"
                                style="left: {{ $project['leftPercent'] }}%;
                                       width: {{ $project['widthPercent'] }}%;
                                       min-width: 100px;
                                       height: 46px;
                                       border-color: {{ $project['color'] }}33;
                                       background: linear-gradient(135deg, {{ $project['color'] }}10, {{ $project['color'] }}08);"
                                title="{{ $project['name'] }} — {{ $project['startDate'] }} to {{ $project['endDate'] }}"
                            >
                                {{-- Progress Fill --}}
                                <div
                                    class="absolute inset-y-0 left-0 rounded-l-lg opacity-20 transition-all duration-500"
                                    style="width: {{ $project['progress'] }}%;
                                           background-color: {{ $project['color'] }};"
                                ></div>

                                {{-- Content --}}
                                <div class="relative flex items-center justify-between w-full px-2.5 py-1.5 z-10">
                                    <div class="flex items-center gap-2 min-w-0">
                                        {{-- Status dot --}}
                                        <span
                                            class="inline-block w-2 h-2 rounded-full flex-shrink-0 ring-2 ring-white dark:ring-gray-800"
                                            style="background-color: {{ $project['color'] }};"
                                        ></span>
                                        <div class="min-w-0">
                                            <div class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                                {{ $project['name'] }}
                                            </div>
                                            <div class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                                {{ $project['statusLabel'] }}
                                                @if($project['budget'])
                                                    · {{ $project['budget'] }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-[10px] text-gray-400 dark:text-gray-500 whitespace-nowrap ml-2 flex-shrink-0">
                                        {{ $project['progress'] }}%
                                    </div>
                                </div>

                                {{-- Milestone markers --}}
                                @foreach($project['milestones'] as $milestone)
                                    @php
                                        // Convert absolute position to relative within the bar
                                        $relPos = $project['widthPercent'] > 0
                                            ? (($milestone['position'] - $project['leftPercent']) / $project['widthPercent']) * 100
                                            : 0;
                                        $relPos = max(0, min(100, $relPos));

                                        $mColor = match($milestone['status']) {
                                            'completed' => '#10b981',
                                            'in_progress' => '#3b82f6',
                                            default => $milestone['isOverdue'] ? '#ef4444' : '#9ca3af',
                                        };

                                        $mIcon = match($milestone['status']) {
                                            'completed' => '✓',
                                            'in_progress' => '●',
                                            default => $milestone['isOverdue'] ? '!' : '◇',
                                        };
                                    @endphp
                                    <div
                                        class="absolute z-10 group/ms"
                                        style="left: {{ $relPos }}%; bottom: -2px;"
                                        title="{{ $milestone['name'] }} — {{ $milestone['date'] }} ({{ $milestone['status'] }})"
                                    >
                                        <div
                                            class="w-3.5 h-3.5 -ml-1.5 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center text-white shadow-sm transition-transform hover:scale-150"
                                            style="background-color: {{ $mColor }}; font-size: 7px;"
                                        >
                                            {{ $mIcon }}
                                        </div>
                                        {{-- Tooltip --}}
                                        <div class="hidden group-hover/ms:block absolute bottom-5 left-1/2 -translate-x-1/2 bg-gray-900 dark:bg-gray-700 text-white text-[10px] px-2 py-1 rounded shadow-lg whitespace-nowrap z-50 pointer-events-none">
                                            <div class="font-semibold">{{ $milestone['name'] }}</div>
                                            <div class="text-gray-300">{{ $milestone['date'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Legend --}}
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 text-[10px] text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Completed</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span> In Progress</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span> Pending</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> Overdue</span>
                    <span class="ml-auto text-gray-400">◇ = Milestone</span>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
