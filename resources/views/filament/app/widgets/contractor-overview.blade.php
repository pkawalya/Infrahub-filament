<x-filament-widgets::widget>
    @php
        $contractorStats = [
            [
                'label'    => 'Equipment Active',
                'value'    => $equipment['active'],
                'sub'      => $equipment['idle'] . ' idle / available',
                'sub_type' => $equipment['idle'] > 3 ? 'warning' : 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-truck class="w-5 h-5 text-violet-600 dark:text-violet-400" />'),
                'icon_bg'  => '#ede9fe',
                'primary'  => false,
                'href'     => route('filament.app.resources.assets.index'),
            ],
            [
                'label'    => 'Fuel Cost (MTD)',
                'value'    => $equipment['fuel_cost'],
                'sub'      => "This month's fuel expenditure",
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-fire class="w-5 h-5 text-orange-600 dark:text-orange-400" />'),
                'icon_bg'  => '#ffedd5',
                'primary'  => false,
                'href'     => null,
            ],
            [
                'label'    => 'Tender Pipeline',
                'value'    => $tenders['pipeline'] + $tenders['submitted'],
                'sub'      => $tenders['pipeline_value'] . ' total pipeline value',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-document-magnifying-glass class="w-5 h-5 text-blue-600 dark:text-blue-400" />'),
                'icon_bg'  => '#dbeafe',
                'primary'  => false,
                'href'     => route('filament.app.resources.tenders.index'),
            ],
            [
                'label'    => 'Tenders Won',
                'value'    => $tenders['won'],
                'sub'      => $tenders['submitted'] . ' awaiting decision',
                'sub_type' => $tenders['won'] > 0 ? 'success' : 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-trophy class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />'),
                'icon_bg'  => '#d1fae5',
                'primary'  => false,
                'href'     => route('filament.app.resources.tenders.index'),
            ],
            [
                'label'    => 'Crew Today',
                'value'    => $crew['present'],
                'sub'      => $crew['absent'] > 0 ? $crew['absent'] . ' absent' : '✓ Full attendance',
                'sub_type' => $crew['absent'] > 0 ? 'warning' : 'success',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-users class="w-5 h-5 text-cyan-600 dark:text-cyan-400" />'),
                'icon_bg'  => '#cffafe',
                'primary'  => false,
                'href'     => route('filament.app.resources.crew-attendances.index'),
            ],
            [
                'label'    => 'Compliance Alerts',
                'value'    => $compliance['expiring_subs'] + $compliance['expiring_certs'],
                'sub'      => $compliance['expiring_subs'] . ' subcontractors, ' . $compliance['expiring_certs'] . ' certifications expiring',
                'sub_type' => ($compliance['expiring_subs'] + $compliance['expiring_certs']) > 0 ? 'danger' : 'success',
                'icon_svg' => ($compliance['expiring_subs'] + $compliance['expiring_certs']) > 0
                    ? \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400" />')
                    : \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-shield-check class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />'),
                'icon_bg'  => ($compliance['expiring_subs'] + $compliance['expiring_certs']) > 0 ? '#fee2e2' : '#d1fae5',
                'primary'  => false,
                'href'     => route('filament.app.resources.subcontractors.index'),
            ],
        ];
    @endphp

    {{-- Section Header --}}
    <div class="db-section-hdr">
        <div class="db-section-hdr__inner">
            <div class="db-section-hdr__icon">
                <x-heroicon-o-building-office-2 class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
            </div>
            <span class="db-section-hdr__lbl">Contractor Operations</span>
        </div>
        <div class="db-section-hdr__line"></div>
    </div>

    @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $contractorStats])
</x-filament-widgets::widget>