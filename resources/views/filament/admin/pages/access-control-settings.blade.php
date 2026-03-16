<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="mt-6 flex items-center justify-between gap-3">
            {{-- Quick Block Button --}}
            <x-filament::button type="button" wire:click="quickBlock" wire:loading.attr="disabled" color="danger"
                icon="heroicon-o-shield-exclamation">
                <span wire:loading.remove wire:target="quickBlock">Block IP</span>
                <span wire:loading wire:target="quickBlock">Blocking…</span>
            </x-filament::button>

            {{-- Save Settings Button --}}
            <x-filament::button type="submit" icon="heroicon-o-check">
                Save Access Control Settings
            </x-filament::button>
        </div>
    </form>

    {{-- Active Blocks Summary --}}
    @php
        $activeBlocks = \App\Models\BlockedIp::active()->count();
        $totalHits = \App\Models\BlockedIp::active()->sum('hit_count');
        $geoEnabled = \App\Models\Setting::getValue('geo_restriction_enabled', config('security.geo_access.enabled', false));
        $allowedCountries = \App\Models\Setting::getValue('geo_allowed_countries');
        $countryCount = $allowedCountries ? count(array_filter(explode(',', $allowedCountries))) : count(config('security.geo_access.allowed_countries', []));
    @endphp

    <div class="mt-8 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] p-5">
        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
            <x-heroicon-o-shield-check class="w-4 h-4 text-emerald-500" />
            Current Status
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs text-gray-500 dark:text-gray-400">
            <div class="rounded-lg bg-white dark:bg-white/5 p-3 border border-gray-100 dark:border-white/5">
                <div class="text-2xl font-bold {{ $activeBlocks > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                    {{ $activeBlocks }}</div>
                <div class="mt-1 text-gray-500">Blocked IPs</div>
            </div>
            <div class="rounded-lg bg-white dark:bg-white/5 p-3 border border-gray-100 dark:border-white/5">
                <div class="text-2xl font-bold text-amber-600">{{ number_format($totalHits) }}</div>
                <div class="mt-1 text-gray-500">Total Block Hits</div>
            </div>
            <div class="rounded-lg bg-white dark:bg-white/5 p-3 border border-gray-100 dark:border-white/5">
                <div
                    class="text-2xl font-bold {{ filter_var($geoEnabled, FILTER_VALIDATE_BOOLEAN) ? 'text-emerald-600' : 'text-gray-400' }}">
                    {{ filter_var($geoEnabled, FILTER_VALIDATE_BOOLEAN) ? 'ON' : 'OFF' }}
                </div>
                <div class="mt-1 text-gray-500">Geo Restriction</div>
            </div>
            <div class="rounded-lg bg-white dark:bg-white/5 p-3 border border-gray-100 dark:border-white/5">
                <div class="text-2xl font-bold text-blue-600">{{ $countryCount }}</div>
                <div class="mt-1 text-gray-500">Allowed Countries</div>
            </div>
        </div>
    </div>

    {{-- Help Section --}}
    <div class="mt-4 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] p-5">
        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
            <x-heroicon-o-light-bulb class="w-4 h-4 text-amber-500" />
            Quick Reference
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-gray-500 dark:text-gray-400">
            <div class="space-y-1">
                <p class="font-semibold text-gray-700 dark:text-gray-300">🌍 Geo Restriction</p>
                <p>Uses <code class="bg-gray-100 dark:bg-white/5 px-1 rounded">ip-api.com</code> free API</p>
                <p>Country lookups cached per IP</p>
                <p class="text-[10px] text-gray-400">Private/localhost IPs always allowed</p>
            </div>
            <div class="space-y-1">
                <p class="font-semibold text-gray-700 dark:text-gray-300">🛡️ IP Blocking</p>
                <p>Supports single IPs & CIDR ranges</p>
                <p>e.g. <code class="bg-gray-100 dark:bg-white/5 px-1 rounded">10.0.0.0/8</code></p>
                <p class="text-[10px] text-gray-400">Also via CLI: php artisan ip:manage block</p>
            </div>
            <div class="space-y-1">
                <p class="font-semibold text-gray-700 dark:text-gray-300">🔑 .env Overrides</p>
                <p><code class="bg-gray-100 dark:bg-white/5 px-1 rounded">GEO_RESTRICTION_ENABLED=true</code></p>
                <p><code class="bg-gray-100 dark:bg-white/5 px-1 rounded">BLOCKED_IPS=1.2.3.4</code></p>
                <p class="text-[10px] text-gray-400">Settings here override .env values</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>