<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    <x-filament::section icon="heroicon-o-map-pin" icon-color="primary">
        <x-slot name="heading">Field Management</x-slot>
        <x-slot name="description">Daily logs, inspections, workforce tracking & site diaries.</x-slot>
    </x-filament::section>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
        <x-filament::section icon="heroicon-o-clipboard-document-list" icon-color="info">
            <x-slot name="heading">Daily Logs</x-slot>
            <x-slot name="description">Record daily site activities</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-magnifying-glass-circle" icon-color="warning">
            <x-slot name="heading">Inspections</x-slot>
            <x-slot name="description">Manage quality inspections</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-user-group" icon-color="success">
            <x-slot name="heading">Workforce</x-slot>
            <x-slot name="description">Track workforce & attendance</x-slot>
        </x-filament::section>
    </div>
</x-filament-panels::page>