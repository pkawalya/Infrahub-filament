<x-filament-panels::page>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

    <x-filament::section icon="heroicon-o-calendar-days" icon-color="primary">
        <x-slot name="heading">Planning & Progress</x-slot>
        <x-slot name="description">Project schedules, milestones & progress tracking.</x-slot>
    </x-filament::section>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
        <x-filament::section icon="heroicon-o-clock" icon-color="info">
            <x-slot name="heading">Timeline</x-slot>
            <x-slot name="description">Project schedule & Gantt view</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-flag" icon-color="warning">
            <x-slot name="heading">Milestones</x-slot>
            <x-slot name="description">Track key project milestones</x-slot>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-chart-bar" icon-color="success">
            <x-slot name="heading">Progress</x-slot>
            <x-slot name="description">S-curve & progress reports</x-slot>
        </x-filament::section>
    </div>
</x-filament-panels::page>