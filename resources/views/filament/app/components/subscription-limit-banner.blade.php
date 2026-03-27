@php
    use App\Filament\App\Widgets\SubscriptionUsageWidget;

    if (!auth()->check()) return;

    $company = auth()->user()?->company;
    if (!$company) return;

    // Build alerts inline (lightweight — no DB joins, uses cached company model)
    $bannerItems = [];

    // ── Usage alerts ─────────────────────────────────────────────
    $maxUsers = $company->getEffectiveMaxUsers();
    if ($maxUsers > 0) {
        $used = $company->users()->count();
        $pct  = round(($used / $maxUsers) * 100);
        if ($pct >= 80) {
            $bannerItems[] = [
                'text'    => "Team members: {$used}/{$maxUsers} ({$pct}% used)",
                'urgent'  => $pct >= 100,
                'icon'    => 'heroicon-o-users',
            ];
        }
    }

    $maxProjects = $company->getEffectiveMaxProjects();
    if ($maxProjects > 0) {
        $used = $company->projects()->count();
        $pct  = round(($used / $maxProjects) * 100);
        if ($pct >= 80) {
            $bannerItems[] = [
                'text'   => "Projects: {$used}/{$maxProjects} ({$pct}% used)",
                'urgent' => $pct >= 100,
                'icon'   => 'heroicon-o-folder-open',
            ];
        }
    }

    $maxStorage = $company->getEffectiveMaxStorageGb();
    if ($maxStorage > 0) {
        $usedGb = round($company->current_storage_bytes / 1024 ** 3, 1);
        $pct    = min(100, round(($usedGb / $maxStorage) * 100));
        if ($pct >= 80) {
            $bannerItems[] = [
                'text'   => "Storage: {$usedGb}/{$maxStorage} GB ({$pct}% used)",
                'urgent' => $pct >= 100,
                'icon'   => 'heroicon-o-circle-stack',
            ];
        }
    }

    // ── Expiry alerts ─────────────────────────────────────────────
    if ($company->is_trial && $company->trial_ends_at?->isFuture()) {
        $days = (int) now()->diffInDays($company->trial_ends_at);
        if ($days <= 7) {
            $bannerItems[] = [
                'text'   => "Trial expires in {$days} " . Str::plural('day', $days) . " ({$company->trial_ends_at->format('M j')})",
                'urgent' => $days <= 2,
                'icon'   => 'heroicon-o-clock',
            ];
        }
    }

    if ($company->subscription_expires_at && $company->billing_cycle !== 'unlimited') {
        if (!$company->subscription_expires_at->isFuture()) {
            $bannerItems[] = [
                'text'   => "Subscription expired on {$company->subscription_expires_at->format('M j, Y')}",
                'urgent' => true,
                'icon'   => 'heroicon-o-exclamation-triangle',
            ];
        } elseif (now()->diffInDays($company->subscription_expires_at) <= 14) {
            $days = (int) now()->diffInDays($company->subscription_expires_at);
            $bannerItems[] = [
                'text'   => "Subscription renews in {$days} " . Str::plural('day', $days),
                'urgent' => $days <= 3,
                'icon'   => 'heroicon-o-credit-card',
            ];
        }
    }

    if (empty($bannerItems)) return;

    $hasUrgent    = collect($bannerItems)->contains('urgent', true);
    $upgradeUrl   = \App\Filament\App\Pages\UpgradePlan::getUrl();
@endphp

<div x-data="{ open: true }" x-show="open" x-cloak
     class="w-full {{ $hasUrgent
         ? 'bg-red-50 dark:bg-red-950/50 border-b border-red-200 dark:border-red-800'
         : 'bg-amber-50 dark:bg-amber-950/50 border-b border-amber-200 dark:border-amber-800' }}">
    <div class="max-w-full px-4 py-2 flex items-center gap-3 flex-wrap">

        {{-- Icon --}}
        <div class="flex-shrink-0">
            @if($hasUrgent)
                <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-red-500" />
            @else
                <x-heroicon-o-exclamation-circle class="w-4 h-4 text-amber-500" />
            @endif
        </div>

        {{-- Pills --}}
        <div class="flex flex-wrap gap-2 flex-1">
            @foreach($bannerItems as $item)
                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                    {{ $item['urgent']
                        ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300'
                        : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300' }}">
                    @svg($item['icon'], 'w-3 h-3')
                    {{ $item['text'] }}
                </span>
            @endforeach
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 flex-shrink-0 ml-auto">
            <a href="{{ $upgradeUrl }}"
               class="text-xs font-semibold px-2.5 py-1 rounded-lg
                {{ $hasUrgent
                    ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 hover:bg-red-200'
                    : 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 hover:bg-amber-200' }}
                transition-colors">
                Upgrade →
            </a>
            <button @click="open = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    title="Dismiss">
                <x-heroicon-o-x-mark class="w-4 h-4" />
            </button>
        </div>
    </div>
</div>
