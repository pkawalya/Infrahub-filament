@php
    $usage  = $this->getViewData()['usage']  ?? [];
    $alerts = $this->getViewData()['alerts'] ?? [];
    $expiry = $this->getViewData()['expiry'] ?? null;
    $plan   = $this->getViewData()['plan']   ?? null;

    $hasAnyAlert = count($alerts) > 0
        || ($expiry && ($expiry['warn'] || $expiry['urgent']));
@endphp

@if(count($usage) || $expiry)
<div class="space-y-3 mb-2">

    {{-- ── Expiry / Trial Banner ── --}}
    @if($expiry)
        @php
            $isExpired = $expiry['type'] === 'expired';
            $bannerBg  = $isExpired
                ? 'bg-red-50 dark:bg-red-950/40 border-red-300 dark:border-red-700'
                : ($expiry['urgent']
                    ? 'bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-700'
                    : ($expiry['warn']
                        ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-700'
                        : 'bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-700'));
            $textColor = $isExpired || $expiry['urgent']
                ? 'text-red-700 dark:text-red-300'
                : ($expiry['warn'] ? 'text-amber-700 dark:text-amber-300' : 'text-blue-700 dark:text-blue-300');
            $icon = $isExpired ? 'heroicon-o-exclamation-triangle'
                : ($expiry['urgent'] ? 'heroicon-o-fire' : 'heroicon-o-clock');
        @endphp
        <div class="flex items-center justify-between gap-4 px-4 py-3 rounded-xl border {{ $bannerBg }} {{ $textColor }}">
            <div class="flex items-center gap-2">
                @svg($icon, 'w-5 h-5 flex-shrink-0')
                <span class="text-sm font-medium">
                    @if($isExpired)
                        ⚠️ Your subscription has <strong>expired</strong>. Renew to avoid service interruption.
                    @elseif($expiry['type'] === 'trial')
                        Your free trial ends in <strong>{{ $expiry['days'] }} {{ Str::plural('day', $expiry['days']) }}</strong> ({{ $expiry['date'] }}).
                    @else
                        Your subscription renews in <strong>{{ $expiry['days'] }} {{ Str::plural('day', $expiry['days']) }}</strong> ({{ $expiry['date'] }}).
                    @endif
                </span>
            </div>
            <a href="{{ \App\Filament\App\Pages\UpgradePlan::getUrl() }}"
               class="text-xs font-semibold px-3 py-1.5 rounded-lg border {{ $textColor }} border-current hover:bg-white/30 transition-colors whitespace-nowrap">
                {{ $isExpired ? 'Renew Now' : 'Manage Plan' }} →
            </a>
        </div>
    @endif

    {{-- ── Usage Bars ── --}}
    @if(count($usage))
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center">
                    <x-heroicon-o-chart-bar class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Plan Usage</div>
                    @if($plan)
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $plan->name }} plan</div>
                    @endif
                </div>
            </div>
            <a href="{{ \App\Filament\App\Pages\UpgradePlan::getUrl() }}"
               class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline">
                Upgrade →
            </a>
        </div>

        {{-- Usage Meters --}}
        <div class="divide-y divide-gray-50 dark:divide-gray-800/60 px-5 py-2">
            @foreach($usage as $item)
                @php
                    $pct = $item['pct'];
                    $isOver    = $pct >= 100;
                    $isDanger  = $pct >= 90;
                    $isWarn    = $pct >= 80;

                    $barColor  = $isOver   ? 'bg-red-500'
                               : ($isDanger ? 'bg-red-400'
                               : ($isWarn   ? 'bg-amber-400'
                                            : 'bg-primary-500'));
                    $labelColor = $isOver  ? 'text-red-600 dark:text-red-400'
                                : ($isWarn ? 'text-amber-600 dark:text-amber-400'
                                           : 'text-gray-500 dark:text-gray-400');
                @endphp
                <div class="py-3" wire:key="usage-{{ $item['key'] }}">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            @svg($item['icon'], 'w-4 h-4 text-gray-400')
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $item['label'] }}
                            </span>
                            @if($isWarn)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold
                                    {{ $isOver ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
                                              : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                                    {{ $isOver ? 'LIMIT REACHED' : 'ALMOST FULL' }}
                                </span>
                            @endif
                        </div>
                        <span class="text-xs font-medium {{ $labelColor }}">
                            {{ $item['used'] }} / {{ $item['max'] }} {{ $item['unit'] }}
                        </span>
                    </div>

                    {{-- Progress bar --}}
                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $barColor }}"
                             style="width: {{ $pct }}%"
                             role="progressbar"
                             aria-valuenow="{{ $pct }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>

                    {{-- Sub-label --}}
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-[11px] {{ $labelColor }}">
                            {{ $pct }}% used
                        </span>
                        @if(!$isOver)
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                {{ $item['left'] }} {{ $item['unit'] }} remaining
                            </span>
                        @else
                            <a href="{{ \App\Filament\App\Pages\UpgradePlan::getUrl() }}"
                               class="text-[11px] font-semibold text-red-600 dark:text-red-400 hover:underline">
                                Upgrade to add more →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endif
