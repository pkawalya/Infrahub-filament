<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])
    {{ $this->table }}
</x-filament-panels::page>