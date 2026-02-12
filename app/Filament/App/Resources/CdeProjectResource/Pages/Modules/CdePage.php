<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\CdeDocument;
use App\Models\CdeFolder;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
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

    protected function getDocFormSchema(): array
    {
        $projectId = $this->record->id;
        return [
            Section::make('Document Details')->schema([
                Forms\Components\TextInput::make('document_number')->label('Document #')->required(),
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\Select::make('cde_folder_id')->label('Folder')
                    ->options(CdeFolder::where('cde_project_id', $projectId)->pluck('name', 'id'))->searchable()->nullable(),
                Forms\Components\Select::make('discipline')->options(['architecture' => 'Architecture', 'structural' => 'Structural', 'mechanical' => 'Mechanical', 'electrical' => 'Electrical', 'plumbing' => 'Plumbing', 'civil' => 'Civil', 'landscape' => 'Landscape', 'interior' => 'Interior', 'general' => 'General'])->searchable(),
                Forms\Components\Select::make('status')->options(['wip' => 'Work in Progress', 'shared' => 'Shared', 'published' => 'Published', 'archived' => 'Archived'])->required()->default('wip'),
                Forms\Components\TextInput::make('revision')->default('A')->maxLength(10),
                Forms\Components\TextInput::make('file_type')->placeholder('e.g. PDF, DWG, BIM')->label('File Type'),
                Forms\Components\TextInput::make('file_size')->numeric()->label('File Size (bytes)'),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;
        $companyId = $this->record->company_id;

        return $table
            ->query(CdeDocument::query()->where('cde_project_id', $projectId))
            ->columns([
                Tables\Columns\TextColumn::make('document_number')->label('Doc #')->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('revision')->badge()->color('info'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'published' => 'success', 'shared' => 'info', 'wip' => 'warning', 'archived' => 'gray', default => 'gray'}),
                Tables\Columns\TextColumn::make('discipline'),
                Tables\Columns\TextColumn::make('file_type'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->headerActions([
                Action::make('create')->label('New Document')->icon('heroicon-o-plus')
                    ->schema($this->getDocFormSchema())
                    ->action(function (array $data) use ($projectId, $companyId): void {
                        $data['cde_project_id'] = $projectId;
                        $data['company_id'] = $companyId;
                        $data['uploaded_by'] = auth()->id();
                        CdeDocument::create($data);
                        Notification::make()->title('Document created')->success()->send();
                    }),
            ])
            ->recordActions([
                Action::make('view')->icon('heroicon-o-eye')->color('gray')
                    ->schema($this->getDocFormSchema())
                    ->fillForm(fn(CdeDocument $record) => $record->toArray())
                    ->modalSubmitAction(false),
                Action::make('edit')->icon('heroicon-o-pencil')
                    ->schema($this->getDocFormSchema())
                    ->fillForm(fn(CdeDocument $record) => $record->toArray())
                    ->action(function (array $data, CdeDocument $record): void {
                        $record->update($data);
                        Notification::make()->title('Document updated')->success()->send();
                    }),
                Action::make('new_revision')->icon('heroicon-o-arrow-path')->color('info')->label('Rev Up')->requiresConfirmation()
                    ->modalDescription('Create a new revision of this document?')
                    ->action(function (CdeDocument $record) {
                        $currentRev = $record->revision ?? 'A';
                        $record->update(['revision' => chr(ord($currentRev) + 1)]);
                    }),
                Action::make('delete')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                    ->action(fn(CdeDocument $record) => $record->delete()),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateHeading('No Documents')
            ->emptyStateDescription('No documents have been uploaded for this project yet.')
            ->emptyStateIcon('heroicon-o-document');
    }
}
