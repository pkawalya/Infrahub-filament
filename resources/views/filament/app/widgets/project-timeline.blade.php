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
                        $minTotalWidth = max(700, $monthCount * 40);
                    @endphp

                    {{-- Month Headers --}}
                    <div class="flex border-b border-gray-200 dark:border-gray-700 mb-2"
                        style="min-width: {{ $minTotalWidth }}px;">
                        @foreach($months as $month)
                            <div class="text-[10px] font-semibold px-1 py-2 text-center truncate
                                {{ $month['isCurrent'] ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/80 dark:bg-indigo-900/30 font-bold' : 'text-gray-400 dark:text-gray-500' }}"
                                style="width: {{ $month['width'] }}%;">
                                {{ $month['short'] }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Timeline Body --}}
                    <div class="relative"
                        style="min-width: {{ $minTotalWidth }}px; min-height: {{ count($projects) * 72 + 20 }}px;">

                        {{-- Grid lines --}}
                        <div class="absolute inset-0 flex pointer-events-none">
                            @foreach($months as $month)
                                <div class="border-r h-full {{ $month['isCurrent'] ? 'bg-indigo-50/20 dark:bg-indigo-900/5 border-indigo-100 dark:border-indigo-800/30' : 'border-gray-50 dark:border-gray-800/30' }}"
                                    style="width: {{ $month['width'] }}%;"></div>
                            @endforeach
                        </div>

                        {{-- Today marker --}}
                        @if($todayPercent !== null)
                            <div class="absolute top-0 bottom-0 z-30 pointer-events-none" style="left: {{ $todayPercent }}%;">
                                <div class="h-full"
                                    style="width: 1.5px; background: repeating-linear-gradient(to bottom, #6366f1 0px, #6366f1 4px, transparent 4px, transparent 8px);">
                                </div>
                                <div class="absolute -top-0.5 -left-[18px] text-white text-[8px] font-bold px-1.5 py-0.5 rounded-md shadow-lg"
                                    style="background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 2px 8px rgba(99,102,241,0.4);">
                                    TODAY
                                </div>
                            </div>
                        @endif

                        {{-- Project Bars --}}
                        @foreach($projects as $index => $project)
                            @php
                                $barThemes = [
                                    'planning'  => ['bar' => 'linear-gradient(135deg, #818cf8, #6366f1)', 'track' => '#c7d2fe', 'text' => '#312e81', 'badge_bg' => 'rgba(99,102,241,0.15)', 'badge_text' => '#4f46e5'],
                                    'active'    => ['bar' => 'linear-gradient(135deg, #34d399, #059669)', 'track' => '#a7f3d0', 'text' => '#064e3b', 'badge_bg' => 'rgba(5,150,105,0.15)', 'badge_text' => '#059669'],
                                    'on_hold'   => ['bar' => 'linear-gradient(135deg, #fbbf24, #d97706)', 'track' => '#fde68a', 'text' => '#78350f', 'badge_bg' => 'rgba(217,119,6,0.15)', 'badge_text' => '#b45309'],
                                    'completed' => ['bar' => 'linear-gradient(135deg, #9ca3af, #6b7280)', 'track' => '#d1d5db', 'text' => '#1f2937', 'badge_bg' => 'rgba(107,114,128,0.15)', 'badge_text' => '#374151'],
                                    'cancelled' => ['bar' => 'linear-gradient(135deg, #f87171, #dc2626)', 'track' => '#fecaca', 'text' => '#7f1d1d', 'badge_bg' => 'rgba(220,38,38,0.15)', 'badge_text' => '#dc2626'],
                                ];
                                $theme = $barThemes[$project['status']] ?? $barThemes['planning'];
                                $actualPct = $project['progress'];
                                $expectedPct = $project['expectedProgress'];
                                $variance = $actualPct - $expectedPct;
                                $isDone = $project['status'] === 'completed';
                                $isCancelled = $project['status'] === 'cancelled';
                            @endphp
                            <div class="absolute left-0 right-0" style="top: {{ $index * 72 + 6 }}px; height: 66px;">

                                {{-- Project Bar --}}
                                <a href="{{ $project['url'] }}"
                                    class="group absolute flex items-center rounded-xl transition-all duration-300
                                           hover:shadow-lg hover:scale-[1.015] hover:z-30 cursor-pointer overflow-visible"
                                    style="left: {{ $project['leftPercent'] }}%;
                                           width: {{ $project['widthPercent'] }}%;
                                           min-width: 160px;
                                           top: 4px;
                                           height: 42px;
                                           background: {{ $theme['track'] }};
                                           border: 1px solid {{ $theme['track'] }};
                                           box-shadow: 0 1px 3px rgba(0,0,0,0.06);">

                                    {{-- Progress fill --}}
                                    <div class="absolute inset-0 rounded-xl overflow-hidden pointer-events-none">
                                        <div class="absolute inset-y-0 left-0 transition-all duration-700 ease-out rounded-l-xl"
                                            style="width: {{ $actualPct }}%; background: {{ $theme['bar'] }};"></div>
                                    </div>

                                    {{-- Expected progress marker --}}
                                    @if($expectedPct > 0 && $expectedPct < 100 && !$isDone && !$isCancelled)
                                        <div class="absolute inset-y-0 z-[5] pointer-events-none"
                                            style="left: {{ $expectedPct }}%;">
                                            <div class="absolute top-1 bottom-1"
                                                style="width: 2px; background: rgba(0,0,0,0.3); border-radius: 1px;"></div>
                                        </div>
                                    @endif

                                    {{-- Content --}}
                                    <div class="relative flex items-center justify-between w-full px-3 z-10">
                                        <div class="flex items-center gap-2 min-w-0">
                                            {{-- Status icon --}}
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-md flex-shrink-0"
                                                style="background: rgba(255,255,255,0.5); backdrop-filter: blur(4px);">
                                                @if($project['status'] === 'completed')
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="{{ $theme['text'] }}" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @elseif($project['status'] === 'active')
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="{{ $theme['text'] }}" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                                                @elseif($project['status'] === 'on_hold')
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="{{ $theme['text'] }}" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
                                                @elseif($project['status'] === 'planning')
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="{{ $theme['text'] }}" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                                @else
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="{{ $theme['text'] }}" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                                @endif
                                            </span>
                                            <div class="min-w-0">
                                                <div class="text-[11px] font-bold truncate" style="color: {{ $theme['text'] }};">
                                                    {{ $project['name'] }}
                                                </div>
                                                <div class="text-[9px] truncate flex items-center gap-1 opacity-70" style="color: {{ $theme['text'] }};">
                                                    {{ $project['statusLabel'] }}
                                                    @if($project['budget']) · {{ $project['budget'] }} @endif
                                                    · {{ $project['startDate'] }} to {{ $project['endDate'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 ml-2 flex-shrink-0">
                                            <span class="text-sm font-extrabold" style="color: {{ $theme['text'] }};">
                                                {{ $project['progress'] }}%
                                            </span>
                                            @if(!$isDone && !$isCancelled)
                                                <span class="text-[8px] font-bold px-1.5 py-0.5 rounded-md whitespace-nowrap"
                                                    style="background: {{ $variance >= 0 ? 'rgba(5,150,105,0.15)' : 'rgba(220,38,38,0.15)' }};
                                                           color: {{ $variance >= 0 ? '#059669' : '#dc2626' }};">
                                                    {{ $project['varianceLabel'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- HOVER POPOVER --}}
                                    @php
                                        $mTotal = count($project['milestones']);
                                        $mDone = collect($project['milestones'])->where('status', 'completed')->count();
                                    @endphp
                                    <div class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 pointer-events-none">
                                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-3 text-left">
                                            <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white dark:bg-gray-800 border-l border-t border-gray-200 dark:border-gray-700 rotate-45"></div>
                                            <div class="text-sm font-bold text-gray-900 dark:text-white mb-2">{{ $project['name'] }}</div>
                                            <div class="space-y-1.5 text-[11px]">
                                                <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-semibold" style="color: {{ $theme['badge_text'] }};">{{ $project['statusLabel'] }}</span></div>
                                                <div class="flex justify-between"><span class="text-gray-500">Duration</span><span class="font-medium text-gray-700 dark:text-gray-300">{{ $project['startDate'] }} → {{ $project['endDate'] }}</span></div>
                                                @if($project['budget'])
                                                    <div class="flex justify-between"><span class="text-gray-500">Budget</span><span class="font-semibold text-gray-700 dark:text-gray-300">{{ $project['budget'] }}</span></div>
                                                @endif
                                                <div class="border-t border-gray-100 dark:border-gray-700 pt-1.5 mt-1.5"></div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-500">Completed</span>
                                                    <div class="flex items-center gap-2">
                                                        <div style="width:60px;height:5px;background:#e5e7eb;border-radius:3px;overflow:hidden;">
                                                            <div style="height:100%;width:{{ $actualPct }}%;background:{{ $theme['badge_text'] }};border-radius:3px;"></div>
                                                        </div>
                                                        <span class="font-bold" style="color: {{ $theme['badge_text'] }};">{{ $actualPct }}%</span>
                                                    </div>
                                                </div>
                                                <div class="flex justify-between items-center"><span class="text-gray-500">Time Spent</span><span class="font-bold text-gray-600">{{ $expectedPct }}%</span></div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-500">Variance</span>
                                                    <span class="font-bold px-1.5 py-0.5 rounded-md text-[10px]"
                                                        style="color: {{ $variance >= 0 ? '#059669' : '#dc2626' }}; background: {{ $variance >= 0 ? 'rgba(5,150,105,0.1)' : 'rgba(220,38,38,0.1)' }};">
                                                        @if($variance > 0) ▲ {{ abs($variance) }}% Ahead
                                                        @elseif($variance < 0) ▼ {{ abs($variance) }}% Behind
                                                        @else ● On Track
                                                        @endif
                                                    </span>
                                                </div>
                                                @if($mTotal > 0)
                                                    <div class="border-t border-gray-100 dark:border-gray-700 pt-1.5 mt-1.5"></div>
                                                    <div class="flex justify-between items-center"><span class="text-gray-500">Milestones</span><span class="font-medium text-gray-700 dark:text-gray-300">{{ $mDone }}/{{ $mTotal }} done</span></div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Milestone markers --}}
                                    @foreach($project['milestones'] as $milestone)
                                        @php
                                            $relPos = $project['widthPercent'] > 0 ? (($milestone['position'] - $project['leftPercent']) / $project['widthPercent']) * 100 : 0;
                                            $relPos = max(0, min(100, $relPos));
                                            $mColor = match ($milestone['status']) { 'completed' => '#10b981', 'in_progress' => '#3b82f6', default => $milestone['isOverdue'] ? '#ef4444' : '#9ca3af' };
                                            $mIcon = match ($milestone['status']) { 'completed' => '✓', 'in_progress' => '●', default => $milestone['isOverdue'] ? '!' : '◇' };
                                        @endphp
                                        <div class="absolute z-10 group/ms" style="left: {{ $relPos }}%; bottom: -4px;"
                                            title="{{ $milestone['name'] }} — {{ $milestone['date'] }} ({{ $milestone['status'] }})">
                                            <div class="w-3.5 h-3.5 -ml-1.5 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center text-white shadow-sm transition-transform hover:scale-150"
                                                style="background-color: {{ $mColor }}; font-size: 6px;">
                                                {{ $mIcon }}
                                            </div>
                                            <div class="hidden group-hover/ms:block absolute bottom-5 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[9px] px-2 py-1.5 rounded-lg shadow-xl whitespace-nowrap z-50 pointer-events-none">
                                                <div class="font-bold">{{ $milestone['name'] }}</div>
                                                <div class="text-gray-300">{{ $milestone['date'] }}</div>
                                                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </a>

                                {{-- Annotation labels below bar --}}
                                <div class="absolute pointer-events-none flex items-center gap-3"
                                    style="left: {{ $project['leftPercent'] }}%; width: {{ $project['widthPercent'] }}%; top: 50px; min-width: 160px;">
                                    @if($actualPct > 0 && !$isDone)
                                        <span class="text-[9px] font-bold whitespace-nowrap px-1 rounded"
                                            style="color: {{ $theme['badge_text'] }}; background: {{ $theme['badge_bg'] }};">
                                            {{ $actualPct }}% done
                                        </span>
                                    @endif
                                    @if($expectedPct > 0 && $expectedPct < 100 && !$isDone && !$isCancelled && abs($variance) > 0)
                                        <span class="text-[9px] font-bold whitespace-nowrap px-1 rounded"
                                            style="color: {{ $variance >= 0 ? '#059669' : '#dc2626' }}; background: {{ $variance >= 0 ? 'rgba(5,150,105,0.08)' : 'rgba(220,38,38,0.08)' }};">
                                            {{ $expectedPct }}% time · {{ abs($variance) }}% {{ $variance >= 0 ? 'ahead' : 'behind' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Legend --}}
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 text-[10px] text-gray-500 dark:text-gray-400">
                        <span class="font-semibold text-gray-600 dark:text-gray-300">Legend:</span>
                        <span class="flex items-center gap-1"><span class="w-4 h-2 rounded-sm inline-block" style="background:linear-gradient(135deg,#34d399,#059669);"></span> Active</span>
                        <span class="flex items-center gap-1"><span class="w-4 h-2 rounded-sm inline-block" style="background:linear-gradient(135deg,#818cf8,#6366f1);"></span> Planning</span>
                        <span class="flex items-center gap-1"><span class="w-4 h-2 rounded-sm inline-block" style="background:linear-gradient(135deg,#fbbf24,#d97706);"></span> On Hold</span>
                        <span class="flex items-center gap-1"><span class="w-4 h-2 rounded-sm inline-block" style="background:linear-gradient(135deg,#9ca3af,#6b7280);"></span> Completed</span>
                        <span class="border-l border-gray-200 dark:border-gray-700 pl-3 flex items-center gap-1"><span class="w-3 h-0.5 inline-block rounded-full" style="background:#6366f1;"></span> Today</span>
                        <span class="flex items-center gap-1"><span class="w-0.5 h-3 inline-block rounded-full" style="background:rgba(0,0,0,0.3);"></span> Expected</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-gray-400 inline-flex items-center justify-center text-white text-[6px]">◇</span> Milestone</span>
                    </div>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-widgets::widget>