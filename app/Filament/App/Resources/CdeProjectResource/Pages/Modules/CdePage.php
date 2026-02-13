<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\CdeDocument;
use App\Models\CdeFolder;
use App\Models\CdeProject;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;

class CdePage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $moduleCode = 'cde';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Documents';
    protected static ?string $title = 'Document Management';
    protected string $view = 'filament.app.pages.modules.cde';

    public ?int $currentFolderId = null;
    public string $folderPath = 'Root';

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->currentFolderId = request()->query('folder') ? (int) request()->query('folder') : null;
        $this->updateFolderPath();
    }

    protected function updateFolderPath(): void
    {
        if ($this->currentFolderId) {
            $folder = CdeFolder::find($this->currentFolderId);
            if ($folder) {
                $path = [];
                $current = $folder;
                while ($current) {
                    array_unshift($path, $current->name);
                    $current = $current->parent;
                }
                $this->folderPath = implode(' / ', $path);
                return;
            }
        }
        $this->folderPath = 'Root';
    }

    // ── Header actions: Upload + Create Folder + Navigate Up ──────────
    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadDocument')
                ->label('Upload Document')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalWidth('2xl')
                ->schema([
                    Forms\Components\FileUpload::make('file')
                        ->label('Select File')
                        ->directory('cde-uploads/' . $this->record->id)
                        ->maxSize(51200) // 50MB
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->required()->maxLength(255)
                        ->placeholder('Document title'),
                    Forms\Components\TextInput::make('document_number')
                        ->label('Document Number')
                        ->default(fn() => 'DOC-' . str_pad((string) (CdeDocument::where('cde_project_id', $this->record->id)->count() + 1), 4, '0', STR_PAD_LEFT))
                        ->required(),
                    Forms\Components\Select::make('discipline')->options([
                        'architecture' => 'Architecture',
                        'structural' => 'Structural',
                        'mechanical' => 'Mechanical',
                        'electrical' => 'Electrical',
                        'plumbing' => 'Plumbing',
                        'civil' => 'Civil',
                        'landscape' => 'Landscape',
                        'other' => 'Other',
                    ])->default('other'),
                    Forms\Components\Select::make('revision')->options([
                        'P01' => 'P01',
                        'P02' => 'P02',
                        'P03' => 'P03',
                        'C01' => 'C01',
                        'C02' => 'C02',
                        'C03' => 'C03',
                        'A' => 'Rev A',
                        'B' => 'Rev B',
                        'C' => 'Rev C',
                    ])->default('P01')->label('Revision'),
                    Forms\Components\Select::make('status')->options([
                        'S0' => 'S0 - Work in Progress',
                        'S1' => 'S1 - For Coordination',
                        'S2' => 'S2 - For Information',
                        'S3' => 'S3 - For Review & Comment',
                        'S4' => 'S4 - For Stage Approval',
                        'S6' => 'S6 - For PIM Handover',
                        'S7' => 'S7 - For AIM Handover',
                    ])->default('S0')->label('Suitability / Status'),
                    Forms\Components\Textarea::make('description')->rows(2),
                ])
                ->action(function (array $data): void {
                    $filePath = $data['file'];
                    $fileSize = Storage::disk('public')->exists($filePath) ? Storage::disk('public')->size($filePath) : 0;
                    $fileType = pathinfo($filePath, PATHINFO_EXTENSION);

                    CdeDocument::create([
                        'company_id' => $this->record->company_id,
                        'cde_project_id' => $this->record->id,
                        'cde_folder_id' => $this->currentFolderId,
                        'title' => $data['title'],
                        'document_number' => $data['document_number'],
                        'discipline' => $data['discipline'] ?? 'other',
                        'revision' => $data['revision'] ?? 'P01',
                        'status' => $data['status'] ?? 'S0',
                        'description' => $data['description'] ?? null,
                        'file_path' => $filePath,
                        'file_size' => $fileSize,
                        'file_type' => $fileType,
                        'uploaded_by' => auth()->id(),
                    ]);

                    Notification::make()->title('Document uploaded successfully')->success()->send();
                }),

            Action::make('createFolder')
                ->label('New Folder')
                ->icon('heroicon-o-folder-plus')
                ->color('gray')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Folder Name')
                        ->required()->maxLength(255),
                    Forms\Components\Select::make('suitability_code')
                        ->label('Default Suitability')
                        ->options([
                            'S0' => 'S0 - WIP',
                            'S1' => 'S1 - Coordination',
                            'S2' => 'S2 - Information',
                            'S3' => 'S3 - Review',
                            'S4' => 'S4 - Approval',
                            'S6' => 'S6 - PIM',
                            'S7' => 'S7 - AIM',
                        ])->nullable(),
                    Forms\Components\Textarea::make('description')->rows(2),
                ])
                ->action(function (array $data): void {
                    CdeFolder::create([
                        'cde_project_id' => $this->record->id,
                        'company_id' => $this->record->company_id,
                        'parent_id' => $this->currentFolderId,
                        'name' => $data['name'],
                        'suitability_code' => $data['suitability_code'] ?? null,
                    ]);

                    Notification::make()->title('Folder created')->success()->send();
                }),

            Action::make('navigateUp')
                ->label('Go Up')
                ->icon('heroicon-o-arrow-up')
                ->color('gray')
                ->visible(fn() => $this->currentFolderId !== null)
                ->action(function (): void {
                    if ($this->currentFolderId) {
                        $folder = CdeFolder::find($this->currentFolderId);
                        $this->navigateToFolder($folder?->parent_id);
                    }
                }),
        ];
    }

    // ── Folder navigation ─────────────────────────────────────────────
    public function navigateToFolder(?int $folderId): void
    {
        $this->redirect(
            CdeProjectResource::getUrl('module-cde', [
                'record' => $this->record,
            ]) . ($folderId ? '?folder=' . $folderId : '')
        );
    }

    public function getSubfolders(): \Illuminate\Database\Eloquent\Collection
    {
        return CdeFolder::where('cde_project_id', $this->record->id)
            ->where('parent_id', $this->currentFolderId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    // ── Stats ──────────────────────────────────────────────────────────
    public function getStats(): array
    {
        $p = $this->record;
        $totalDocs = $p->documents()->count();
        $totalFolders = $p->folders()->count();
        $recentUploads = $p->documents()->where('created_at', '>=', now()->subDays(7))->count();

        return [
            [
                'label' => 'Documents',
                'value' => $totalDocs,
                'sub' => $recentUploads . ' this week',
                'sub_type' => 'info',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'
            ],
            [
                'label' => 'Folders',
                'value' => $totalFolders,
                'sub' => 'Organized',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Recent Uploads',
                'value' => $recentUploads,
                'sub' => 'Last 7 days',
                'sub_type' => $recentUploads > 0 ? 'success' : 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
        ];
    }

    // ── Document table ─────────────────────────────────────────────────
    public function table(Table $table): Table
    {
        return $table
            ->query(
                CdeDocument::query()
                    ->where('cde_project_id', $this->record->id)
                    ->where('cde_folder_id', $this->currentFolderId)
                    ->with(['uploadedBy', 'folder'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('document_number')
                    ->label('Doc #')->searchable()->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-document-text'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('discipline')->badge()->color('info'),
                Tables\Columns\TextColumn::make('revision')->badge()->color('warning'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(?string $state) => match ($state) {
                        'S0' => 'gray', 'S1' => 'info', 'S2' => 'primary',
                        'S3' => 'warning', 'S4' => 'success', 'S6' => 'success', 'S7' => 'success',
                        default => 'gray',
                    })
                    ->label('Suitability'),
                Tables\Columns\TextColumn::make('file_type')->label('Type')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return '-';
                        if ($state < 1024)
                            return $state . ' B';
                        if ($state < 1048576)
                            return round($state / 1024, 1) . ' KB';
                        return round($state / 1048576, 1) . ' MB';
                    }),
                Tables\Columns\TextColumn::make('uploadedBy.name')->label('Uploaded By'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M d, Y H:i')->sortable()->label('Uploaded'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Suitability')->options([
                    'S0' => 'S0 - WIP',
                    'S1' => 'S1 - Coordination',
                    'S2' => 'S2 - Information',
                    'S3' => 'S3 - Review',
                    'S4' => 'S4 - Approval',
                ]),
                Tables\Filters\SelectFilter::make('discipline')->options([
                    'architecture' => 'Architecture',
                    'structural' => 'Structural',
                    'mechanical' => 'Mechanical',
                    'electrical' => 'Electrical',
                    'civil' => 'Civil',
                    'other' => 'Other',
                ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (CdeDocument $record) {
                        if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                            return response()->streamDownload(
                                fn() => print (Storage::disk('public')->get($record->file_path)),
                                $record->title . '.' . $record->file_type,
                                ['Content-Type' => Storage::disk('public')->mimeType($record->file_path)]
                            );
                        }
                        Notification::make()->title('File not found')->danger()->send();
                    }),

                \Filament\Actions\Action::make('revise')
                    ->label('New Revision')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->schema([
                        Forms\Components\FileUpload::make('file')
                            ->label('Updated File')
                            ->directory('cde-uploads/' . $this->record->id)
                            ->maxSize(51200)->required(),
                        Forms\Components\Select::make('revision')->options([
                            'P01' => 'P01',
                            'P02' => 'P02',
                            'P03' => 'P03',
                            'C01' => 'C01',
                            'C02' => 'C02',
                            'C03' => 'C03',
                            'A' => 'Rev A',
                            'B' => 'Rev B',
                            'C' => 'Rev C',
                        ])->required()->label('New Revision'),
                        Forms\Components\Select::make('status')->options([
                            'S0' => 'S0 - WIP',
                            'S1' => 'S1 - Coordination',
                            'S2' => 'S2 - Information',
                            'S3' => 'S3 - Review',
                            'S4' => 'S4 - Approval',
                        ])->label('Suitability'),
                        Forms\Components\Textarea::make('description')->label('Change Notes')->rows(2),
                    ])
                    ->action(function (array $data, CdeDocument $record): void {
                        $filePath = $data['file'];
                        $fileSize = Storage::disk('public')->exists($filePath) ? Storage::disk('public')->size($filePath) : 0;
                        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);

                        $record->update([
                            'file_path' => $filePath,
                            'file_size' => $fileSize,
                            'file_type' => $fileType,
                            'revision' => $data['revision'],
                            'status' => $data['status'] ?? $record->status,
                            'description' => $data['description'] ?? $record->description,
                        ]);

                        Notification::make()->title('Revision uploaded: ' . $data['revision'])->success()->send();
                    }),

                \Filament\Actions\Action::make('editMeta')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->schema([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\TextInput::make('document_number')->required(),
                        Forms\Components\Select::make('discipline')->options([
                            'architecture' => 'Architecture',
                            'structural' => 'Structural',
                            'mechanical' => 'Mechanical',
                            'electrical' => 'Electrical',
                            'civil' => 'Civil',
                            'other' => 'Other',
                        ]),
                        Forms\Components\Select::make('status')->options([
                            'S0' => 'S0 - WIP',
                            'S1' => 'S1 - Coordination',
                            'S2' => 'S2 - Information',
                            'S3' => 'S3 - Review',
                            'S4' => 'S4 - Approval',
                            'S6' => 'S6 - PIM',
                            'S7' => 'S7 - AIM',
                        ])->label('Suitability'),
                        Forms\Components\Textarea::make('description')->rows(2),
                    ])
                    ->fillForm(fn(CdeDocument $record) => $record->toArray())
                    ->action(function (array $data, CdeDocument $record): void {
                        $record->update($data);
                        Notification::make()->title('Document updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('moveToFolder')
                    ->label('Move')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('gray')
                    ->schema([
                        Forms\Components\Select::make('cde_folder_id')
                            ->label('Move to Folder')
                            ->options(fn() => collect([null => '📁 Root'])->merge(
                                CdeFolder::where('cde_project_id', $this->record->id)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->mapWithKeys(fn($name, $id) => [$id => '📁 ' . $name])
                            ))
                            ->required(),
                    ])
                    ->action(function (array $data, CdeDocument $record): void {
                        $folderId = $data['cde_folder_id'] === '' ? null : $data['cde_folder_id'];
                        $record->update(['cde_folder_id' => $folderId]);
                        Notification::make()->title('Document moved')->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (CdeDocument $record): void {
                        if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                            Storage::disk('public')->delete($record->file_path);
                        }
                        $record->delete();
                        Notification::make()->title('Document deleted')->success()->send();
                    }),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Documents')
            ->emptyStateDescription('Upload documents or create folders to organize your project files.')
            ->emptyStateIcon('heroicon-o-document-plus');
    }
}
