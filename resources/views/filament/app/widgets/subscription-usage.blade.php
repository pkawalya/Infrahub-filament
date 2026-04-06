@php
    $_vd     = $this->getViewData();
    $usage   = $_vd['usage']   ?? [];
    $alerts  = $_vd['alerts']  ?? [];
    $expiry  = $_vd['expiry']  ?? null;
    $plan    = $_vd['plan']    ?? null;
    $company = $_vd['company'] ?? null;
@endphp

@if(count($usage) || $expiry)
<div class="space-y-2 mb-2">

    {{-- ── Expiry / Trial Banner ── --}}
    @if($expiry)
        @php
            $isExpired = $expiry['type'] === 'expired';
            $cls = $isExpired || $expiry['urgent']
                ? 'bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-800 text-red-700 dark:text-red-300'
                : ($expiry['warn']
                    ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300'
                    : 'bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300');
            $icon = $isExpired ? 'heroicon-o-exclamation-triangle'
                : ($expiry['urgent'] ? 'heroicon-o-fire' : 'heroicon-o-clock');
        @endphp
        <div class="flex items-center justify-between gap-4 px-4 py-2.5 rounded-xl border text-sm {{ $cls }}">
            <div class="flex items-center gap-2">
                @svg($icon, 'w-4 h-4 flex-shrink-0')
                <span class="font-medium">
                    @if($isExpired)
                        Your subscription has <strong>expired</strong>. Renew to avoid interruption.
                    @elseif($expiry['type'] === 'trial')
                        Free trial ends in <strong>{{ $expiry['days'] }} {{ Str::plural('day', $expiry['days']) }}</strong> &mdash; {{ $expiry['date'] }}
                    @else
                        Subscription renews in <strong>{{ $expiry['days'] }} {{ Str::plural('day', $expiry['days']) }}</strong> &mdash; {{ $expiry['date'] }}
                    @endif
                </span>
            </div>
            <a href="{{ \App\Filament\App\Pages\UpgradePlan::getUrl() }}"
               class="text-xs font-bold px-3 py-1 rounded-lg border border-current hover:bg-white/30 transition-colors whitespace-nowrap">
                {{ $isExpired ? 'Renew Now' : 'Manage Plan' }} →
            </a>
        </div>
    @endif

    {{-- ── Plan Usage Card ── --}}
    @if(count($usage))
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">

        {{-- Card Header --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-chart-bar class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">Plan Usage</span>
                    @if($plan)
                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full
                                     bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300">
                            {{ $plan->name }}
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ \App\Filament\App\Pages\UpgradePlan::getUrl() }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg
                      bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300
                      border border-violet-200 dark:border-violet-700
                      hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors">
                <x-heroicon-o-rocket-launch class="w-3.5 h-3.5" />
                Upgrade
            </a>
        </div>

        {{-- 3-column meters grid --}}
        <div class="grid divide-x divide-gray-100 dark:divide-gray-800"
             style="grid-template-columns: repeat({{ count($usage) }}, 1fr);">
            @foreach($usage as $item)
                @php
                    $pct      = $item['pct'];
                    $isOver   = $pct >= 100;
                    $isDanger = $pct >= 90;
                    $isWarn   = $pct >= 80;

                    $barStyle = $isOver
                        ? 'background:linear-gradient(90deg,#ef4444,#dc2626)'
                        : ($isDanger
                            ? 'background:linear-gradient(90deg,#f87171,#ef4444)'
                            : ($isWarn
                                ? 'background:linear-gradient(90deg,#fbbf24,#f59e0b)'
                                : 'background:linear-gradient(90deg,#6366f1,#8b5cf6)'));

                    $pctClr = $isOver
                        ? 'text-red-600 dark:text-red-400'
                        : ($isWarn ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500');
                @endphp
                <div class="px-5 py-3.5" wire:key="usage-{{ $item['key'] }}">

                    {{-- Label + count --}}
                    <div class="flex items-center justify-between mb-2.5">
                        <div class="flex items-center gap-1.5 min-w-0">
                            @svg($item['icon'], 'w-3.5 h-3.5 text-gray-400 flex-shrink-0')
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 truncate">
                                {{ $item['label'] }}
                            </span>
                            @if($isWarn)
                                <span class="flex-shrink-0 inline-flex items-center justify-center w-3.5 h-3.5 rounded-full text-[9px] font-black
                                     {{ $isOver ? 'bg-red-100 dark:bg-red-900/30 text-red-600' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600' }}">
                                    {{ $isOver ? '!' : '↑' }}
                                </span>
                            @endif
                        </div>
                        <span class="text-xs font-bold {{ $pctClr }} flex-shrink-0 ml-2">
                            {{ $item['used'] }}<span class="font-medium opacity-60">/{{ $item['max'] }}</span>
                            <span class="font-medium text-gray-400 dark:text-gray-500 ml-0.5">{{ $item['unit'] }}</span>
                        </span>
                    </div>

                    {{-- Progress bar --}}
                    <div class="h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700"
                             style="width: {{ $pct }}%; {{ $barStyle }}"
                             role="progressbar"
                             aria-valuenow="{{ $pct }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between mt-1.5">
                        <span class="text-[11px] {{ $pctClr }} font-semibold">{{ $pct }}% used</span>
                        @if($isOver)
                            <a href="{{ \App\Filament\App\Pages\UpgradePlan::getUrl() }}"
                               class="text-[11px] font-bold text-red-600 dark:text-red-400 hover:underline">
                                Upgrade →
                            </a>
                        @else
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">{{ $item['left'] }} {{ $item['unit'] }} left</span>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

    </div>
    @endif

</div>
@endif
