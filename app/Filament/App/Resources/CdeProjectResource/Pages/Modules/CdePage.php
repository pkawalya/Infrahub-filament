<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\CdeDocument;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class CdePage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'cde';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Documents';
    protected static ?string $title = 'Common Data Environment';
    protected string $view = 'filament.app.pages.modules.cde';

    public function getStats(): array
    {
        $r = $this->record;
        $docs = $r->documents();
        $thisWeek = (clone $docs)->where('created_at', '>=', now()->startOfWeek())->count();

        return [
            [
                'label' => 'Total Documents',
                'value' => $docs->count(),
                'sub' => '+' . $thisWeek . ' this week',
                'sub_type' => $thisWeek > 0 ? 'success' : 'neutral',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'
            ],
            [
                'label' => 'Folders',
                'value' => $r->folders()->count(),
                'sub' => 'Organized',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'RFIs',
                'value' => $r->rfis()->count(),
                'sub' => 'Requests',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>',
                'icon_bg' => '#fffbeb'
            ],
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(CdeDocument::query()->where('cde_project_id', $this->record->id))
            ->columns([
                Tables\Columns\TextColumn::make('document_number')->label('Doc #')->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('revision')->badge()->color('info'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'published' => 'success', 'shared' => 'info', 'wip' => 'warning', 'archived' => 'gray', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('discipline'),
                Tables\Columns\TextColumn::make('file_type'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('No Documents')
            ->emptyStateDescription('No documents have been uploaded for this project yet.')
            ->emptyStateIcon('heroicon-o-document');
    }
}
