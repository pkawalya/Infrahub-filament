<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\ProjectFolder;
use App\Models\ProjectDocument;
use App\Models\DocumentVersion;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\UploadedFile;

class ProjectDocuments extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected string $view = 'filament.pages.project-documents';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $title = 'Project Documents';

    protected static bool $shouldRegisterNavigation = false;

    public ?int $projectId = null;
    public ?int $folderId = null;
    public ?Project $project = null;
    public ?ProjectFolder $currentFolder = null;
    public array $breadcrumbs = [];

    // Form state
    public ?array $uploadData = [];

    public function mount(): void
    {
        $this->projectId = request()->query('project');
        $this->folderId = request()->query('folder');

        if (!$this->projectId) {
            $this->redirect(route('filament.admin.resources.projects.index'));
            return;
        }

        $this->project = Project::findOrFail($this->projectId);

        if ($this->folderId) {
            $this->currentFolder = ProjectFolder::where('project_id', $this->projectId)
                ->findOrFail($this->folderId);
            $this->breadcrumbs = $this->currentFolder->breadcrumb;
        }
    }

    public function getTitle(): string
    {
        return $this->project ? "Documents: {$this->project->name}" : 'Project Documents';
    }

    public function getSubheading(): ?string
    {
        if ($this->currentFolder) {
            return "📁 " . $this->currentFolder->full_path;
        }
        return '📁 Root';
    }

    public function getBreadcrumbs(): array
    {
        $crumbs = [
            route('filament.admin.resources.projects.index') => 'Projects',
            route('filament.admin.resources.projects.view', ['record' => $this->projectId]) => $this->project?->name ?? 'Project',
            route('filament.admin.pages.project-documents', ['project' => $this->projectId]) => 'Documents',
        ];

        foreach ($this->breadcrumbs as $folder) {
            $crumbs[route('filament.admin.pages.project-documents', [
                'project' => $this->projectId,
                'folder' => $folder['id'],
            ])] = $folder['name'];
        }

        return $crumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadDocument')
                ->label('Upload Document')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalWidth('5xl')
                ->form([
                    FileUpload::make('files')
                        ->label('Select Files')
                        ->multiple()
                        ->maxSize(51200) // 50MB
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'text/plain',
                            'text/csv',
                            'application/zip',
                        ])
                        ->directory('temp-uploads')
                        ->storeFileNamesIn('original_filenames')
                        ->helperText('Max 50MB per file. Supported: PDF, Images, Word, Excel, PowerPoint, Text, CSV, ZIP')
                        ->required(),

                    Textarea::make('description')
                        ->label('Description (optional)')
                        ->rows(2)
                        ->placeholder('Add a description for these documents'),
                ])
                ->action(function (array $data): void {
                    $this->processUpload($data);
                }),

            Action::make('createFolder')
                ->label('New Folder')
                ->icon('heroicon-o-folder-plus')
                ->color('gray')
                ->modalWidth('lg')
                ->form([
                    TextInput::make('name')
                        ->label('Folder Name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Enter folder name'),

                    Textarea::make('description')
                        ->label('Description (optional)')
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    ProjectFolder::create([
                        'project_id' => $this->projectId,
                        'parent_id' => $this->folderId,
                        'name' => $data['name'],
                        'description' => $data['description'] ?? null,
                        'created_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->title('Folder created')
                        ->success()
                        ->send();
                }),

            Action::make('backToProject')
                ->label('Back to Project')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(route('filament.admin.resources.projects.view', ['record' => $this->projectId])),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProjectDocument::query()
                    ->where('project_id', $this->projectId)
                    ->where('folder_id', $this->folderId)
                    ->with(['currentVersion', 'creator', 'locker'])
            )
            ->columns([
                IconColumn::make('file_icon')
                    ->label('')
                    ->icon(fn(ProjectDocument $record) => $record->file_icon)
                    ->color(fn(ProjectDocument $record) => match ($record->file_type) {
                        'pdf' => 'danger',
                        'image' => 'success',
                        'document' => 'info',
                        'spreadsheet' => 'success',
                        'presentation' => 'warning',
                        default => 'gray',
                    })
                    ->width('40px'),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn(ProjectDocument $record) => $record->currentVersion?->original_filename),

                TextColumn::make('currentVersion.version_number')
                    ->label('Version')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('file_size_formatted')
                    ->label('Size'),

                TextColumn::make('version_count')
                    ->label('Versions')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_locked')
                    ->label('Locked')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('gray'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(ProjectDocument $record) => $record->status_color),

                TextColumn::make('creator.name')
                    ->label('Uploaded by')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('file_type')
                    ->options([
                        'pdf' => 'PDF',
                        'image' => 'Images',
                        'document' => 'Word Documents',
                        'spreadsheet' => 'Spreadsheets',
                        'presentation' => 'Presentations',
                        'other' => 'Other',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                        'superseded' => 'Superseded',
                    ]),
            ])
            ->actions([
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (ProjectDocument $record): ?StreamedResponse {
                        $version = $record->currentVersion;
                        if ($version && Storage::disk('local')->exists($version->file_path)) {
                            $record->logHistory('downloaded', 'Document downloaded', null, $version->id);
                            return response()->streamDownload(
                                fn() => print (Storage::disk('local')->get($version->file_path)),
                                $version->original_filename,
                                ['Content-Type' => $version->mime_type]
                            );
                        }

                        Notification::make()
                            ->title('File not found')
                            ->danger()
                            ->send();
                    }),

                Action::make('uploadNewVersion')
                    ->label('New Version')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('info')
                    ->modalWidth('lg')
                    ->visible(fn(ProjectDocument $record) => $record->canEdit())
                    ->form([
                        FileUpload::make('file')
                            ->label('Select New Version')
                            ->maxSize(51200)
                            ->directory('temp-uploads')
                            ->storeFileNamesIn('original_filename')
                            ->required(),

                        Toggle::make('major_version')
                            ->label('Major Version (e.g., 1.0 → 2.0)')
                            ->helperText('Enable for significant changes, disable for minor updates'),

                        Textarea::make('change_notes')
                            ->label('Change Notes')
                            ->placeholder('What changed in this version?')
                            ->rows(2),
                    ])
                    ->action(function (ProjectDocument $record, array $data): void {
                        $this->uploadNewVersion($record, $data);
                    }),

                Action::make('viewHistory')
                    ->label('History')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->modalWidth('2xl')
                    ->modalContent(fn(ProjectDocument $record) => view('filament.pages.partials.document-history', [
                        'history' => $record->history()->with('user', 'version')->limit(50)->get(),
                    ]))
                    ->modalSubmitAction(false),

                Action::make('toggleLock')
                    ->label(fn(ProjectDocument $record) => $record->is_locked ? 'Unlock' : 'Lock')
                    ->icon(fn(ProjectDocument $record) => $record->is_locked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed')
                    ->color(fn(ProjectDocument $record) => $record->is_locked ? 'success' : 'warning')
                    ->visible(fn(ProjectDocument $record) => $record->canEdit())
                    ->action(function (ProjectDocument $record): void {
                        if ($record->is_locked) {
                            $record->unlock();
                            Notification::make()->title('Document unlocked')->success()->send();
                        } else {
                            $record->lock();
                            Notification::make()->title('Document locked')->success()->send();
                        }
                    }),

                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (ProjectDocument $record): void {
                        $record->logHistory('deleted', 'Document deleted');
                        $record->delete();

                        Notification::make()
                            ->title('Document deleted')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No documents yet')
            ->emptyStateDescription('Upload documents or create folders to organize your project files.')
            ->emptyStateIcon('heroicon-o-document-plus');
    }

    /**
     * Get subfolders for the current location.
     */
    public function getSubfolders()
    {
        return ProjectFolder::where('project_id', $this->projectId)
            ->where('parent_id', $this->folderId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Navigate to a folder.
     */
    public function navigateToFolder(?int $folderId): void
    {
        $this->redirect(route('filament.admin.pages.project-documents', [
            'project' => $this->projectId,
            'folder' => $folderId,
        ]));
    }

    /**
     * Navigate up to parent folder.
     */
    public function navigateUp(): void
    {
        $parentId = $this->currentFolder?->parent_id;
        $this->navigateToFolder($parentId);
    }

    /**
     * Process file upload.
     */
    protected function processUpload(array $data): void
    {
        $files = $data['files'] ?? [];
        $description = $data['description'] ?? null;
        $uploadedCount = 0;
        $duplicateCount = 0;

        foreach ($files as $tempPath) {
            $fullPath = Storage::disk('public')->path($tempPath);

            if (!file_exists($fullPath)) {
                continue;
            }

            $originalFilename = $data['original_filenames'][$tempPath] ?? basename($tempPath);
            $mimeType = mime_content_type($fullPath);
            $fileSize = filesize($fullPath);
            $fileHash = md5_file($fullPath);

            // Check for duplicates
            $existingDuplicate = ProjectDocument::findDuplicate($this->projectId, $fileHash);
            if ($existingDuplicate) {
                $duplicateCount++;
                // Upload as new version to existing document
                $this->uploadNewVersionFromFile(
                    $existingDuplicate->document,
                    $fullPath,
                    $originalFilename,
                    $mimeType,
                    $fileSize,
                    $fileHash,
                    'Duplicate file detected - uploaded as new version'
                );
                Storage::disk('public')->delete($tempPath);
                continue;
            }

            // Create new document
            $document = ProjectDocument::create([
                'project_id' => $this->projectId,
                'folder_id' => $this->folderId,
                'title' => pathinfo($originalFilename, PATHINFO_FILENAME),
                'document_number' => ProjectDocument::generateDocumentNumber($this->projectId),
                'description' => $description,
                'file_type' => ProjectDocument::determineFileType($mimeType),
                'mime_type' => $mimeType,
                'status' => 'active',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Create version and move file
            $storagePath = "documents/{$this->projectId}/{$document->id}/" . time() . '_' . $originalFilename;
            Storage::disk('local')->put($storagePath, file_get_contents($fullPath));

            $version = DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => '1.0',
                'major_version' => 1,
                'minor_version' => 0,
                'file_path' => $storagePath,
                'original_filename' => $originalFilename,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'file_hash' => $fileHash,
                'change_type' => 'initial',
                'is_current' => true,
                'uploaded_by' => auth()->id(),
            ]);

            $document->update([
                'current_version_id' => $version->id,
            ]);

            $document->logHistory('created', 'Document uploaded', null, $version->id);

            Storage::disk('public')->delete($tempPath);
            $uploadedCount++;
        }

        $message = "{$uploadedCount} document(s) uploaded";
        if ($duplicateCount > 0) {
            $message .= ", {$duplicateCount} duplicate(s) added as new versions";
        }

        Notification::make()
            ->title('Upload complete')
            ->body($message)
            ->success()
            ->send();
    }

    /**
     * Upload new version of existing document.
     */
    protected function uploadNewVersion(ProjectDocument $document, array $data): void
    {
        $tempPath = $data['file'];
        $fullPath = Storage::disk('public')->path($tempPath);

        if (!file_exists($fullPath)) {
            Notification::make()->title('File not found')->danger()->send();
            return;
        }

        $originalFilename = $data['original_filename'][$tempPath] ?? basename($tempPath);
        $mimeType = mime_content_type($fullPath);
        $fileSize = filesize($fullPath);
        $fileHash = md5_file($fullPath);

        $this->uploadNewVersionFromFile(
            $document,
            $fullPath,
            $originalFilename,
            $mimeType,
            $fileSize,
            $fileHash,
            $data['change_notes'] ?? null,
            $data['major_version'] ?? false
        );

        Storage::disk('public')->delete($tempPath);

        Notification::make()
            ->title('New version uploaded')
            ->success()
            ->send();
    }

    /**
     * Create a new version from a file.
     */
    protected function uploadNewVersionFromFile(
        ProjectDocument $document,
        string $filePath,
        string $originalFilename,
        string $mimeType,
        int $fileSize,
        string $fileHash,
        ?string $changeNotes = null,
        bool $majorVersion = false
    ): DocumentVersion {
        $nextVersion = DocumentVersion::getNextVersion($document->id, $majorVersion);

        $storagePath = "documents/{$document->project_id}/{$document->id}/" . time() . '_' . $originalFilename;
        Storage::disk('local')->put($storagePath, file_get_contents($filePath));

        $version = DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => $nextVersion['string'],
            'major_version' => $nextVersion['major'],
            'minor_version' => $nextVersion['minor'],
            'file_path' => $storagePath,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'file_hash' => $fileHash,
            'change_notes' => $changeNotes,
            'change_type' => $majorVersion ? 'revision' : 'correction',
            'is_current' => true,
            'uploaded_by' => auth()->id(),
        ]);

        $version->makeCurrent();

        $document->update([
            'updated_by' => auth()->id(),
            'mime_type' => $mimeType,
            'file_type' => ProjectDocument::determineFileType($mimeType),
        ]);

        $document->logHistory('uploaded', "New version {$nextVersion['string']} uploaded", [
            'version' => $nextVersion['string'],
            'change_notes' => $changeNotes,
        ], $version->id);

        return $version;
    }
}
