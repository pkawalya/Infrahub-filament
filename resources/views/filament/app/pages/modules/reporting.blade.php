<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    <x-filament::section icon="heroicon-o-chart-bar" icon-color="primary">
        <x-slot name="heading">Reporting & Dashboards</x-slot>
        <x-slot name="description">Analytics, custom reports & project dashboards.</x-slot>
    </x-filament::section>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
        <x-filament::section icon="heroicon-o-document-chart-bar" icon-color="info">
            <x-slot name="heading">Custom Reports</x-slot>
            <x-slot name="description">Build & export reports</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-presentation-chart-line" icon-color="warning">
            <x-slot name="heading">Dashboards</x-slot>
            <x-slot name="description">Real-time project dashboards</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-table-cells" icon-color="success">
            <x-slot name="heading">Data Export</x-slot>
            <x-slot name="description">Export data to CSV & PDF</x-slot>
        </x-filament::section>
    </div>
</x-filament-panels::page>