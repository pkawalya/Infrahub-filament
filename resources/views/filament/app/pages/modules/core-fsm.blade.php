<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
        <x-filament::section icon="heroicon-o-wrench-screwdriver" icon-color="primary">
            <x-slot name="heading">Work Orders</x-slot>
            <x-slot name="description">Manage field service operations</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-server-stack" icon-color="info">
            <x-slot name="heading">Assets</x-slot>
            <x-slot name="description">Track equipment & asset lifecycle</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-document-currency-dollar" icon-color="success">
            <x-slot name="heading">Invoices</x-slot>
            <x-slot name="description">Billing & payment tracking</x-slot>
        </x-filament::section>
    </div>
</x-filament-panels::page>