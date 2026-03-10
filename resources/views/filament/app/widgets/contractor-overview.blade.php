<x-filament-widgets::widget>
    @php
        $contractorStats = [
            [
                'label' => 'Equipment Active',
                'value' => $equipment['active'],
                'sub' => $equipment['idle'] . ' idle / available',
                'sub_type' => $equipment['idle'] > 3 ? 'warning' : 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-truck class="w-5 h-5 text-violet-600 dark:text-violet-400" />'),
                'icon_bg' => '#ede9fe',
                'primary' => false,
            ],
            [
                'label' => 'Fuel Cost (MTD)',
                'value' => $equipment['fuel_cost'],
                'sub' => 'This month\'s fuel expenditure',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-fire class="w-5 h-5 text-orange-600 dark:text-orange-400" />'),
                'icon_bg' => '#ffedd5',
                'primary' => false,
            ],
            [
                'label' => 'Tender Pipeline',
                'value' => $tenders['pipeline'] + $tenders['submitted'],
                'sub' => $tenders['pipeline_value'] . ' total pipeline value',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-document-magnifying-glass class="w-5 h-5 text-blue-600 dark:text-blue-400" />'),
                'icon_bg' => '#dbeafe',
                'primary' => false,
            ],
            [
                'label' => 'Tenders Won',
                'value' => $tenders['won'],
                'sub' => $tenders['submitted'] . ' awaiting decision',
                'sub_type' => $tenders['won'] > 0 ? 'success' : 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-trophy class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />'),
                'icon_bg' => '#d1fae5',
                'primary' => false,
            ],
            [
                'label' => 'Crew Today',
                'value' => $crew['present'],
                'sub' => $crew['absent'] > 0 ? $crew['absent'] . ' absent' : '✓ Full attendance',
                'sub_type' => $crew['absent'] > 0 ? 'warning' : 'success',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-users class="w-5 h-5 text-cyan-600 dark:text-cyan-400" />'),
                'icon_bg' => '#cffafe',
                'primary' => false,
            ],
            [
                'label' => 'Compliance Alerts',
                'value' => $compliance['expiring_subs'] + $compliance['expiring_certs'],
                'sub' => $compliance['expiring_subs'] . ' subcontractors, ' . $compliance['expiring_certs'] . ' certifications expiring',
                'sub_type' => ($compliance['expiring_subs'] + $compliance['expiring_certs']) > 0 ? 'danger' : 'success',
                'icon_svg' => ($compliance['expiring_subs'] + $compliance['expiring_certs']) > 0
                    ? \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400" />')
                    : \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-shield-check class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />'),
                'icon_bg' => ($compliance['expiring_subs'] + $compliance['expiring_certs']) > 0 ? '#fee2e2' : '#d1fae5',
                'primary' => false,
            ],
        ];
    @endphp

    <div class="mb-1">
        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contractor
            Operations</h3>
    </div>
    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $contractorStats])
</x-filament-widgets::widget>