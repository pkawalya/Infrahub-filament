<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    <div class="mt-4">
        {{ $this->table }}
    </div>
</x-filament-panels::page>