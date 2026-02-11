<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\SafetyIncident;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class SheqPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'sheq';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'SHEQ';
    protected static ?string $title = 'SHEQ Management';
    protected string $view = 'filament.app.pages.modules.sheq';

    public function getStats(): array
    {
        $r = $this->record;
        $total = $r->safetyIncidents()->count();
        $open = $r->safetyIncidents()->whereNotIn('status', ['closed', 'resolved'])->count();
        $critical = $r->safetyIncidents()->where('severity', 'critical')->count();

        return [
            [
                'label' => 'Total Incidents',
                'value' => $total,
                'sub' => $open . ' open',
                'sub_type' => $open > 0 ? 'danger' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>'
            ],
            [
                'label' => 'Critical',
                'value' => $critical,
                'sub' => $critical > 0 ? 'Needs attention' : 'None',
                'sub_type' => $critical > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#dc2626" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>',
                'icon_bg' => '#fef2f2'
            ],
            [
                'label' => 'Resolved',
                'value' => $r->safetyIncidents()->whereIn('status', ['closed', 'resolved'])->count(),
                'sub' => 'Handled',
                'sub_type' => 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SafetyIncident::query()->where('cde_project_id', $this->record->id))
            ->columns([
                Tables\Columns\TextColumn::make('incident_number')->label('Incident #')->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('severity')->badge()
                    ->color(fn(string $state) => match ($state) { 'critical' => 'danger', 'major' => 'warning', 'minor' => 'info', default => 'gray'}),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'resolved' => 'success', 'closed' => 'gray', 'investigating' => 'warning', 'reported' => 'info', default => 'gray'}),
                Tables\Columns\TextColumn::make('incident_date')->dateTime(),
            ])
            ->defaultSort('incident_date', 'desc')
            ->emptyStateHeading('No Safety Incidents')
            ->emptyStateDescription('No safety incidents have been reported for this project.')
            ->emptyStateIcon('heroicon-o-shield-check');
    }
}
