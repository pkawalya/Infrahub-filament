<x-filament-widgets::widget>
    @php
        $tenantStats = [
            [
                'label' => 'Active Projects',
                'value' => $stats['projects']['active'],
                'sub' => $stats['projects']['active'] . ' projects currently in progress',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-building-office class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />'),
                'icon_bg' => '#e0e7ff',
                'primary' => true,
            ],
            [
                'label' => 'Open Tasks',
                'value' => $stats['tasks']['open'],
                'sub' => $stats['tasks']['overdue'] > 0 ? $stats['tasks']['overdue'] . ' overdue — action needed' : '✓ All tasks on track',
                'sub_type' => $stats['tasks']['overdue'] > 0 ? 'danger' : 'success',
                'icon_svg' => $stats['tasks']['overdue'] > 0
                    ? \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400" />')
                    : \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />'),
                'icon_bg' => $stats['tasks']['overdue'] > 0 ? '#fee2e2' : '#d1fae5',
                'primary' => false,
            ],
            [
                'label' => 'Work Orders',
                'value' => $stats['workOrders']['open'],
                'sub' => $stats['workOrders']['completed'] . ' completed this month',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-wrench-screwdriver class="w-5 h-5 text-sky-600 dark:text-sky-400" />'),
                'icon_bg' => '#e0f2fe',
                'primary' => false,
            ],
            [
                'label' => 'Revenue (MTD)',
                'value' => $stats['revenue']['current'],
                'sub' => ($stats['revenue']['change'] >= 0 ? '+' : '') . $stats['revenue']['change'] . '% vs last month',
                'sub_type' => $stats['revenue']['change'] >= 0 ? 'success' : 'danger',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-banknotes class="w-5 h-5 text-amber-600 dark:text-amber-400" />'),
                'icon_bg' => '#fef3c7',
                'primary' => false,
            ]
        ];
    @endphp

    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $tenantStats])
</x-filament-widgets::widget>