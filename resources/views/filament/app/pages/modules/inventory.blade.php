<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    <x-filament::section icon="heroicon-o-cube" icon-color="primary">
        <x-slot name="heading">Inventory & Procurement</x-slot>
        <x-slot name="description">Stock management, purchase orders & material tracking.</x-slot>
    </x-filament::section>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
        <x-filament::section icon="heroicon-o-archive-box" icon-color="info">
            <x-slot name="heading">Stock Items</x-slot>
            <x-slot name="description">Track inventory levels</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-shopping-cart" icon-color="warning">
            <x-slot name="heading">Purchase Orders</x-slot>
            <x-slot name="description">Manage procurement</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-truck" icon-color="success">
            <x-slot name="heading">Deliveries</x-slot>
            <x-slot name="description">Track material deliveries</x-slot>
        </x-filament::section>
    </div>
</x-filament-panels::page>