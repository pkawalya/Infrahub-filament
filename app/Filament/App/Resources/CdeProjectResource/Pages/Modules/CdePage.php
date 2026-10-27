<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\CdeDocument;
use App\Models\CdeDocumentComment;
use App\Models\CdeDocumentVersion;
use App\Models\CdeFolder;
use App\Models\CdeProject;
use App\Models\DocumentShare;
use App\Models\DocumentSubmission;
use App\Models\Transmittal;
use App\Models\TransmittalItem;
use App\Models\Rfi;
use App\Models\User;
use App\Services\AsiteStructureService;
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
use App\Filament\App\Concerns\ExportsTableCsv;
use App\Services\AiAssistantService;
use App\Support\StoragePath;
use ZipArchive;

class CdePage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;
    use ExportsTableCsv;

    protected static string $moduleCode = 'cde';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Documents';
    protected static ?string $title = 'Document Management';
    protected string $view = 'filament.app.pages.modules.cde';

    // ── Folder & Navigation State ─────────────────────────────────────
    public ?int $currentFolderId = null;
    public string $folderPath = 'Root';
    public bool $showShareModal = false;
    public ?int $sharingDocumentId = null;
    public string $activeDocTab = 'files'; // 'files', 'distribution', 'comments', 'pipeline'

    // ── ASITE Explorer & Tree Navigation ──────────────────────────────
    public string $folderSearch = '';
    public array $expandedFolderIds = [];
    public bool $isTreeCollapsed = false;

    // ── ASITE Quick Task Strip Filter ─────────────────────────────────
    // 'all', 'incomplete', 'overdue', 'due_today', 'unread_comments'
    public string $quickFilter = 'all';

    // ── ASITE Table Search, Filter & Sort ─────────────────────────────
    public string $searchDoc = '';
    public string $filterDiscipline = '';
    public string $filterStatus = '';
    public string $filterFileType = '';
    public string $sortBy = 'date'; // 'date', 'doc_ref', 'title'
    public string $sortDirection = 'desc';
    public int $perPage = 250;
    public int $currentPage = 1;

    // ── Multi-select / Batch operations ───────────────────────────────
    public array $selectedDocIds = [];
    public bool $selectAll = false;

    // ── ASITE Slide-over Document Detail Drawer ───────────────────────
    public bool $drawerOpen = false;
    public ?int $selectedDocId = null;
    public string $drawerTab = 'attributes'; // 'attributes', 'versions', 'comments', 'audit'
    public string $newCommentText = '';

    // ── Modals: New Folder & Rename Folder (Clean Design, No ISO) ──────
    public bool $showNewFolderModal = false;
    public ?int $newFolderParentId = null;
    public string $newFolderName = '';
    public string $newFolderDescription = ''; // Comments & purpose for this folder

    public bool $showRenameFolderModal = false;
    public ?int $renameFolderId = null;
    public string $renameFolderName = '';
    public string $renameFolderDescription = '';

    // ── Comments Hub & Collaboration State ────────────────────────────
    public string $commentFilterStatus = 'all'; // 'all', 'open', 'resolved'
    public string $commentSearch = '';
    public ?int $commentTargetDocId = null;
    public ?int $replyToCommentId = null;
    public string $replyCommentText = '';
    public bool $showNewCommentBox = false;
    public string $newFolderCommentText = '';

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->currentFolderId = request()->query('folder') ? (int) request()->query('folder') : null;

        // Auto-initialize standard ASITE ISO structure if project has 0 folders
        if ($this->record->folders()->count() === 0) {
            AsiteStructureService::initialize($this->record, true);
        }

        // If no folder specified in query, pick folder with documents or first folder
        if ($this->currentFolderId === null) {
            $folderWithDocs = CdeFolder::where('cde_project_id', $this->record->id)
                ->where('name', 'like', '%01.1.2%')
                ->first()
                ?? CdeFolder::where('cde_project_id', $this->record->id)
                    ->whereHas('documents')
                    ->first()
                ?? CdeFolder::where('cde_project_id', $this->record->id)->first();

            if ($folderWithDocs) {
                $this->currentFolderId = $folderWithDocs->id;
            }
        }

        $this->updateFolderPath();
        $this->expandAncestorsOfCurrentFolder();
    }

    protected function expandAncestorsOfCurrentFolder(): void
    {
        if ($this->currentFolderId) {
            $folder = CdeFolder::find($this->currentFolderId);
            while ($folder) {
                if (!in_array($folder->id, $this->expandedFolderIds, true)) {
                    $this->expandedFolderIds[] = $folder->id;
                }
                $folder = $folder->parent;
            }
        }
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

    // ── Header Actions ────────────────────────────────────────────────
    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadDocument')
                ->label('Upload Document')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->size('xs')
                ->modalWidth('2xl')
                ->schema([
                    Forms\Components\FileUpload::make('file')
                        ->label('Select File')
                        ->directory(StoragePath::documents($this->record))
                        ->maxSize(102400) // 100MB
                        ->required()
                        ->validationAttribute('file')
                        ->validationMessages([
                            'required' => 'Please select a file to upload.',
                            'max' => 'The file is too large. Maximum size is 100MB.',
                        ]),
                    Forms\Components\TextInput::make('document_number')
                        ->label('Doc Ref (ISO 19650 standard)')
                        ->default(function () {
                            $projCode = $this->record->code ?: 'KAATC';
                            $count = $this->record->documents()->count() + 1;
                            return "{$projCode}-AFX-PM-ALL-ALL-RPT-{$count}";
                        })
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->required()->maxLength(255)
                        ->placeholder('Document title'),
                    Forms\Components\Select::make('cde_folder_id')
                        ->label('Target Folder')
                        ->options(fn() => CdeFolder::where('cde_project_id', $this->record->id)
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->default(fn() => $this->currentFolderId)
                        ->searchable()
                        ->nullable(),
                    Forms\Components\Select::make('discipline')->options([
                        'architecture' => 'Architecture',
                        'structural' => 'Structural',
                        'mechanical' => 'Mechanical',
                        'electrical' => 'Electrical',
                        'plumbing' => 'Plumbing',
                        'civil' => 'Civil',
                        'landscape' => 'Landscape',
                        'other' => 'Other',
                    ])->default('civil'),
                    Forms\Components\Select::make('revision')->options([
                        'P01' => 'P01',
                        'P02' => 'P02',
                        'P03' => 'P03',
                        'P04' => 'P04',
                        'P05' => 'P05',
                        'P06' => 'P06',
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
                    ])->default('S4')->label('Suitability / Status'),
                    Forms\Components\DatePicker::make('due_date')
                        ->label('Task Review Due Date')
                        ->nullable(),
                    Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $filePath = $data['file'];
                    $fileSize = Storage::disk('public')->exists($filePath) ? Storage::disk('public')->size($filePath) : 0;
                    $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                    $targetFolder = $data['cde_folder_id'] ?? $this->currentFolderId;

                    CdeDocument::create([
                        'company_id' => $this->record->company_id,
                        'cde_project_id' => $this->record->id,
                        'cde_folder_id' => $targetFolder,
                        'title' => $data['title'],
                        'document_number' => $data['document_number'],
                        'discipline' => $data['discipline'] ?? 'other',
                        'revision' => $data['revision'] ?? 'P01',
                        'status' => $data['status'] ?? 'S0',
                        'due_date' => $data['due_date'] ?? null,
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
                ->size('xs')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Folder Name')
                        ->placeholder('e.g. 01.2 Site Surveys & Photos')
                        ->required()->maxLength(255),
                    Forms\Components\Select::make('parent_id')
                        ->label('Parent Folder')
                        ->options(fn() => collect([null => '📁 Root Level'])->merge(
                            CdeFolder::where('cde_project_id', $this->record->id)->orderBy('name')->pluck('name', 'id')
                        ))
                        ->default(fn() => $this->currentFolderId)
                        ->searchable(),
                    Forms\Components\Textarea::make('description')
                        ->label('Comments & Purpose')
                        ->placeholder('Add comments, notes, or guidelines for files in this folder...')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $folder = CdeFolder::create([
                        'cde_project_id' => $this->record->id,
                        'company_id' => $this->record->company_id,
                        'parent_id' => $data['parent_id'] ?? $this->currentFolderId,
                        'name' => $data['name'],
                        'description' => $data['description'] ?? null,
                        'created_by' => auth()->id(),
                    ]);

                    if (!empty($data['description'])) {
                        CdeDocumentComment::create([
                            'cde_folder_id' => $folder->id,
                            'user_id' => auth()->id() ?? User::where('company_id', $this->record->company_id)->value('id') ?? 1,
                            'comment' => $data['description'],
                            'is_resolved' => false,
                        ]);
                    }

                    if ($folder->parent_id && !in_array($folder->parent_id, $this->expandedFolderIds, true)) {
                        $this->expandedFolderIds[] = $folder->parent_id;
                    }

                    $this->currentFolderId = $folder->id;
                    $this->updateFolderPath();
                    Notification::make()->title('Folder created successfully')->success()->send();
                }),

            Action::make('initAsiteTemplate')
                ->label('ASITE ISO Template')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->size('xs')
                ->requiresConfirmation()
                ->modalHeading('Load ASITE ISO 19650 Template')
                ->modalDescription('This will set up the standard ASITE numbered folder structure (01 Development Management, 02 Design, 03 Planning, 04 Commercial, 05 Contract Management) and populate standard protocol documents.')
                ->action(fn() => $this->initializeAsiteStructure()),

            Action::make('createTransmittal')
                ->label('Create Transmittal')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->size('xs')
                ->modalWidth('3xl')
                ->schema([
                    Section::make('Transmittal Details')->schema([
                        Forms\Components\TextInput::make('transmittal_number')->label('Transmittal #')
                            ->default(fn() => 'TR-' . str_pad((string) (Transmittal::where('cde_project_id', $this->record->id)->count() + 1), 4, '0', STR_PAD_LEFT))
                            ->required()->maxLength(50),
                        Forms\Components\TextInput::make('subject')->required()->maxLength(255)->columnSpanFull(),
                        Forms\Components\TextInput::make('to_organization')->label('To Organization')->required()->maxLength(255),
                        Forms\Components\TextInput::make('to_contact')->label('Contact Person')->maxLength(255),
                        Forms\Components\Select::make('purpose')->options(Transmittal::$purposes)->required()->default('for_information'),
                        Forms\Components\Textarea::make('description')->label('Cover Note')->rows(3)->columnSpanFull(),
                    ])->columns(2),
                    Section::make('Attached Documents')->schema([
                        Forms\Components\CheckboxList::make('document_ids')
                            ->label('Select Documents to Transmit')
                            ->options(
                                fn() => CdeDocument::where('cde_project_id', $this->record->id)
                                    ->orderByDesc('created_at')
                                    ->get()
                                    ->mapWithKeys(fn($doc) => [$doc->id => "{$doc->document_number} — {$doc->title} (Rev {$doc->revision})"])
                                    ->toArray()
                            )
                            ->searchable()
                            ->columns(1)
                            ->columnSpanFull(),
                    ]),
                ])
                ->action(function (array $data): void {
                    $transmittal = Transmittal::create([
                        'company_id' => $this->record->company_id,
                        'cde_project_id' => $this->record->id,
                        'transmittal_number' => $data['transmittal_number'],
                        'subject' => $data['subject'],
                        'description' => $data['description'] ?? null,
                        'to_organization' => $data['to_organization'],
                        'to_contact' => $data['to_contact'] ?? null,
                        'purpose' => $data['purpose'],
                        'from_user_id' => auth()->id(),
                        'status' => 'draft',
                    ]);

                    if (!empty($data['document_ids'])) {
                        foreach ($data['document_ids'] as $docId) {
                            TransmittalItem::create([
                                'transmittal_id' => $transmittal->id,
                                'cde_document_id' => $docId,
                                'copies' => 1,
                            ]);
                        }
                    }

                    Notification::make()->title('Transmittal created with ' . count($data['document_ids'] ?? []) . ' document(s)')->success()->send();
                }),

            Action::make('createRfi')
                ->label('New RFI')
                ->icon('heroicon-o-question-mark-circle')
                ->color('warning')
                ->size('xs')
                ->modalWidth('2xl')
                ->schema([
                    Section::make('Request For Information')->schema([
                        Forms\Components\TextInput::make('rfi_number')->label('RFI #')
                            ->default(fn() => 'RFI-' . str_pad((string) (Rfi::where('cde_project_id', $this->record->id)->count() + 1), 4, '0', STR_PAD_LEFT))
                            ->required()->maxLength(50),
                        Forms\Components\Select::make('priority')->options(Rfi::$priorities)->default('medium')->required(),
                        Forms\Components\TextInput::make('subject')->required()->maxLength(255)->columnSpanFull(),
                        Forms\Components\Textarea::make('question')->required()->rows(4)->columnSpanFull(),
                        Forms\Components\Select::make('assigned_to')->label('Assign To')
                            ->options(fn() => User::where('company_id', $this->record->company_id)->pluck('name', 'id'))
                            ->searchable()->nullable(),
                        Forms\Components\DatePicker::make('due_date')->label('Due Date')->nullable(),
                        Forms\Components\Select::make('cde_document_id')->label('Related Document')
                            ->options(fn() => CdeDocument::where('cde_project_id', $this->record->id)
                                ->orderBy('document_number')
                                ->get()
                                ->mapWithKeys(fn($doc) => [$doc->id => "{$doc->document_number} — {$doc->title}"])
                                ->toArray())
                            ->searchable()->nullable()->placeholder('Select a document (optional)'),
                    ])->columns(2),
                ])
                ->action(function (array $data): void {
                    Rfi::create([
                        'company_id' => $this->record->company_id,
                        'cde_project_id' => $this->record->id,
                        'cde_document_id' => $data['cde_document_id'] ?? null,
                        'rfi_number' => $data['rfi_number'],
                        'subject' => $data['subject'],
                        'question' => $data['question'],
                        'priority' => $data['priority'],
                        'assigned_to' => $data['assigned_to'] ?? null,
                        'due_date' => $data['due_date'] ?? null,
                        'raised_by' => auth()->id(),
                        'status' => 'open',
                    ]);
                    Notification::make()->title('RFI created successfully.')->success()->send();
                }),
        ];
    }

    // ── ASITE Folder Tree Operations ──────────────────────────────────
    public function selectFolder(?int $folderId): void
    {
        $this->currentFolderId = $folderId;
        $this->selectedDocIds = [];
        $this->selectAll = false;
        $this->currentPage = 1;
        $this->updateFolderPath();
        $this->expandAncestorsOfCurrentFolder();
    }

    public function toggleFolderExpand(int $folderId): void
    {
        if (in_array($folderId, $this->expandedFolderIds, true)) {
            $this->expandedFolderIds = array_values(array_diff($this->expandedFolderIds, [$folderId]));
        } else {
            $this->expandedFolderIds[] = $folderId;
        }
    }

    public function expandAllFolders(): void
    {
        $this->expandedFolderIds = CdeFolder::where('cde_project_id', $this->record->id)->pluck('id')->toArray();
    }

    public function collapseAllFolders(): void
    {
        $this->expandedFolderIds = [];
    }

    public function toggleTreeCollapse(): void
    {
        $this->isTreeCollapsed = !$this->isTreeCollapsed;
    }

    public function setQuickFilter(string $filter): void
    {
        $this->quickFilter = $filter;
        $this->currentPage = 1;
    }

    public function toggleFlag(int $docId): void
    {
        $doc = CdeDocument::where('cde_project_id', $this->record->id)->find($docId);
        if ($doc) {
            $doc->update(['is_flagged' => !$doc->is_flagged]);
        }
    }

    public function toggleSelectDoc(int $docId): void
    {
        if (in_array($docId, $this->selectedDocIds, true)) {
            $this->selectedDocIds = array_values(array_diff($this->selectedDocIds, [$docId]));
        } else {
            $this->selectedDocIds[] = $docId;
        }
        $docs = $this->getDocuments();
        $this->selectAll = count($this->selectedDocIds) > 0 && count($this->selectedDocIds) >= $docs->count();
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedDocIds = [];
            $this->selectAll = false;
        } else {
            $this->selectedDocIds = $this->getDocuments()->pluck('id')->toArray();
            $this->selectAll = true;
        }
    }

    public function setSort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function openDrawer(int $docId, string $tab = 'attributes'): void
    {
        $this->selectedDocId = $docId;
        $this->drawerTab = $tab;
        $this->drawerOpen = true;
    }

    public function closeDrawer(): void
    {
        $this->drawerOpen = false;
    }

    public function postComment(): void
    {
        if (!$this->selectedDocId || empty(trim($this->newCommentText))) {
            return;
        }

        CdeDocumentComment::create([
            'cde_document_id' => $this->selectedDocId,
            'user_id' => auth()->id() ?? User::where('company_id', $this->record->company_id)->value('id') ?? 1,
            'comment' => trim($this->newCommentText),
            'is_resolved' => false,
        ]);

        $this->newCommentText = '';
        Notification::make()->title('Comment added')->success()->send();
    }

    public function toggleCommentResolved(int $commentId): void
    {
        $comment = CdeDocumentComment::find($commentId);
        if ($comment) {
            $comment->update(['is_resolved' => !$comment->is_resolved]);
            Notification::make()->title($comment->is_resolved ? 'Comment marked as resolved' : 'Comment reopened')->success()->send();
        }
    }

    public function openNewFolderModal(?int $parentId = null): void
    {
        $this->newFolderParentId = $parentId ?? $this->currentFolderId;
        $this->newFolderName = '';
        $this->newFolderDescription = '';
        $this->showNewFolderModal = true;
    }

    public function submitNewFolder(): void
    {
        if (empty(trim($this->newFolderName))) {
            Notification::make()->title('Folder name is required')->danger()->send();
            return;
        }

        $folder = CdeFolder::create([
            'company_id' => $this->record->company_id,
            'cde_project_id' => $this->record->id,
            'parent_id' => $this->newFolderParentId,
            'name' => trim($this->newFolderName),
            'description' => trim($this->newFolderDescription) ?: null,
            'created_by' => auth()->id(),
        ]);

        if (!empty(trim($this->newFolderDescription))) {
            CdeDocumentComment::create([
                'cde_folder_id' => $folder->id,
                'user_id' => auth()->id() ?? User::where('company_id', $this->record->company_id)->value('id') ?? 1,
                'comment' => trim($this->newFolderDescription),
                'is_resolved' => false,
            ]);
        }

        if ($this->newFolderParentId && !in_array($this->newFolderParentId, $this->expandedFolderIds, true)) {
            $this->expandedFolderIds[] = $this->newFolderParentId;
        }

        $this->showNewFolderModal = false;
        $this->newFolderName = '';
        $this->newFolderDescription = '';
        $this->currentFolderId = $folder->id;
        $this->updateFolderPath();
        Notification::make()->title('Folder created successfully')->success()->send();
    }

    public function openRenameFolderModal(int $folderId): void
    {
        $folder = CdeFolder::findOrFail($folderId);
        $this->renameFolderId = $folder->id;
        $this->renameFolderName = $folder->name;
        $this->renameFolderDescription = $folder->description ?? '';
        $this->showRenameFolderModal = true;
    }

    public function submitRenameFolder(): void
    {
        if (!$this->renameFolderId || empty(trim($this->renameFolderName))) {
            return;
        }

        $folder = CdeFolder::findOrFail($this->renameFolderId);
        $folder->update([
            'name' => trim($this->renameFolderName),
            'description' => trim($this->renameFolderDescription) ?: null,
        ]);
        $this->showRenameFolderModal = false;
        $this->updateFolderPath();
        Notification::make()->title('Folder updated successfully')->success()->send();
    }

    public function postFolderComment(): void
    {
        if (!$this->currentFolderId || empty(trim($this->newFolderCommentText))) {
            return;
        }

        CdeDocumentComment::create([
            'cde_folder_id' => $this->currentFolderId,
            'user_id' => auth()->id() ?? User::where('company_id', $this->record->company_id)->value('id') ?? 1,
            'comment' => trim($this->newFolderCommentText),
            'is_resolved' => false,
        ]);

        $folder = CdeFolder::find($this->currentFolderId);
        if ($folder && empty($folder->description)) {
            $folder->update(['description' => trim($this->newFolderCommentText)]);
        }

        $this->newFolderCommentText = '';
        Notification::make()->title('Folder comment added')->success()->send();
    }

    public function postProjectComment(): void
    {
        if (empty(trim($this->newCommentText))) {
            Notification::make()->title('Please enter a comment')->warning()->send();
            return;
        }

        CdeDocumentComment::create([
            'cde_document_id' => $this->commentTargetDocId ?: null,
            'cde_folder_id' => $this->commentTargetDocId ? null : $this->currentFolderId,
            'user_id' => auth()->id() ?? User::where('company_id', $this->record->company_id)->value('id') ?? 1,
            'comment' => trim($this->newCommentText),
            'is_resolved' => false,
        ]);

        $this->newCommentText = '';
        $this->showNewCommentBox = false;
        Notification::make()->title('Comment posted successfully')->success()->send();
    }

    public function replyToComment(int $parentId): void
    {
        if (empty(trim($this->replyCommentText))) {
            Notification::make()->title('Please enter a reply')->warning()->send();
            return;
        }

        $parent = CdeDocumentComment::findOrFail($parentId);

        CdeDocumentComment::create([
            'cde_document_id' => $parent->cde_document_id,
            'cde_folder_id' => $parent->cde_folder_id,
            'parent_id' => $parent->id,
            'user_id' => auth()->id() ?? User::where('company_id', $this->record->company_id)->value('id') ?? 1,
            'comment' => trim($this->replyCommentText),
            'is_resolved' => false,
        ]);

        $this->replyCommentText = '';
        $this->replyToCommentId = null;
        Notification::make()->title('Reply posted')->success()->send();
    }

    public function deleteComment(int $commentId): void
    {
        $comment = CdeDocumentComment::findOrFail($commentId);
        $comment->delete();
        Notification::make()->title('Comment deleted')->success()->send();
    }

    public function deleteFolder(int $folderId): void
    {
        $folder = CdeFolder::findOrFail($folderId);

        if (!$folder->canBeModifiedBy()) {
            Notification::make()->title('You do not have permission to delete this folder.')->danger()->send();
            return;
        }

        if ($folder->documents()->exists() || $folder->children()->exists()) {
            Notification::make()->title('Cannot delete folder')
                ->body('This folder contains documents or subfolders. Please move or delete them first.')
                ->danger()
                ->send();
            return;
        }

        $parentId = $folder->parent_id;
        $folder->delete();

        if ($this->currentFolderId === $folderId) {
            $this->currentFolderId = $parentId;
            $this->updateFolderPath();
        }

        Notification::make()->title('Folder deleted')->success()->send();
    }

    public function initializeAsiteStructure(): void
    {
        AsiteStructureService::initialize($this->record, true);

        $targetFolder = CdeFolder::where('cde_project_id', $this->record->id)
            ->where('name', 'like', '%01.1.2%')
            ->first();

        if ($targetFolder) {
            $this->currentFolderId = $targetFolder->id;
        }

        $this->updateFolderPath();
        $this->expandAncestorsOfCurrentFolder();
        Notification::make()->title('ASITE ISO 19650 folder structure initialized successfully')->success()->send();
    }

    // ── Batch Operations ──────────────────────────────────────────────
    public function batchDownload()
    {
        $docIds = $this->selectedDocIds;

        if (empty($docIds)) {
            // If none selected, take all in current folder
            $docIds = $this->getDocuments()->pluck('id')->toArray();
        }

        if (empty($docIds)) {
            Notification::make()->title('No documents available to download')->warning()->send();
            return;
        }

        $docs = CdeDocument::whereIn('id', $docIds)
            ->where('cde_project_id', $this->record->id)
            ->get();

        if ($docs->isEmpty()) {
            Notification::make()->title('Documents not found')->danger()->send();
            return;
        }

        if ($docs->count() === 1) {
            return $this->downloadDocument($docs->first()->id);
        }

        $zipFileName = 'Asite_' . ($this->record->code ?: 'Project') . '_' . now()->format('Ymd_His') . '.zip';
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $tempZipPath = $tempDir . '/' . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            Notification::make()->title('Could not create ZIP package')->danger()->send();
            return;
        }

        foreach ($docs as $doc) {
            $entryName = ($doc->document_number ?: 'DOC') . '.' . ($doc->file_type ?: 'bin');
            $entryName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $entryName);

            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                $content = Storage::disk('public')->get($doc->file_path);
                $zip->addFromString($entryName, $content);
            } else {
                // Add metadata sheet for files not stored on local disk
                $manifest = "=== ASITE CDE Document Manifest ===\n" .
                    "Document Reference: {$doc->document_number}\n" .
                    "Title: {$doc->title}\n" .
                    "Revision: {$doc->revision}\n" .
                    "Status / Suitability: {$doc->status}\n" .
                    "Discipline: {$doc->discipline}\n" .
                    "Created: {$doc->created_at}\n";
                $zip->addFromString(pathinfo($entryName, PATHINFO_FILENAME) . '_manifest.txt', $manifest);
            }
        }

        $zip->close();

        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function batchDelete(): void
    {
        if (empty($this->selectedDocIds)) {
            return;
        }

        CdeDocument::whereIn('id', $this->selectedDocIds)
            ->where('cde_project_id', $this->record->id)
            ->delete();

        $this->selectedDocIds = [];
        $this->selectAll = false;
        Notification::make()->title('Selected documents deleted')->success()->send();
    }

    public function batchFlag(): void
    {
        if (empty($this->selectedDocIds)) {
            return;
        }

        $docs = CdeDocument::whereIn('id', $this->selectedDocIds)->where('cde_project_id', $this->record->id)->get();
        foreach ($docs as $d) {
            $d->update(['is_flagged' => !$d->is_flagged]);
        }
        Notification::make()->title('Updated flag status for selected documents')->success()->send();
    }

    // ── Data Providers for View ───────────────────────────────────────
    public function getQuickFilterCounts(): array
    {
        $base = CdeDocument::where('cde_project_id', $this->record->id);

        $all = (clone $base)->count();
        $incomplete = (clone $base)->whereIn('status', ['wip', 'draft', 'under_review', 'revision', 'S0', 'S1', 'S2', 'S3'])->count();
        $overdue = (clone $base)->where('due_date', '<', now()->toDateString())->whereNotIn('status', ['approved', 'published', 'archived', 'S4', 'S7'])->count();
        $dueToday = (clone $base)->where('due_date', now()->toDateString())->count();
        $unreadComments = CdeDocumentComment::where(function ($q) {
            $q->whereHas('document', fn($d) => $d->where('cde_project_id', $this->record->id))
              ->orWhereHas('folder', fn($f) => $f->where('cde_project_id', $this->record->id));
        })->where('is_resolved', false)->count();

        return [
            'all' => $all > 99 ? '99+' : (string)$all,
            'incomplete' => $incomplete > 99 ? '99+' : (string)$incomplete,
            'overdue' => $overdue > 99 ? '99+' : (string)$overdue,
            'due_today' => (string)$dueToday,
            'unread_comments' => $unreadComments > 99 ? '99+' : (string)$unreadComments,
        ];
    }

    public function getFolderTree()
    {
        $query = CdeFolder::where('cde_project_id', $this->record->id)
            ->withCount('documents')
            ->orderBy('sort_order')
            ->orderBy('name');

        if (!empty($this->folderSearch)) {
            $s = '%' . trim($this->folderSearch) . '%';
            return $query->where('name', 'like', $s)->get();
        }

        // Return top-level folders
        return $query->whereNull('parent_id')->with('children.children')->get();
    }

    public function getCurrentFolder(): ?CdeFolder
    {
        if (!$this->currentFolderId) {
            return null;
        }
        return CdeFolder::with('comments.user')->find($this->currentFolderId);
    }

    public function getDocuments()
    {
        $query = CdeDocument::where('cde_project_id', $this->record->id)
            ->with(['uploadedBy', 'folder', 'comments']);

        // Folder filter
        if ($this->currentFolderId !== null) {
            $query->where('cde_folder_id', $this->currentFolderId);
        }

        // Quick filter
        if ($this->quickFilter === 'incomplete') {
            $query->whereIn('status', ['wip', 'draft', 'under_review', 'revision', 'S0', 'S1', 'S2', 'S3']);
        } elseif ($this->quickFilter === 'overdue') {
            $query->where('due_date', '<', now()->toDateString())
                  ->whereNotIn('status', ['approved', 'published', 'archived', 'S4', 'S7']);
        } elseif ($this->quickFilter === 'due_today') {
            $query->where('due_date', now()->toDateString());
        } elseif ($this->quickFilter === 'unread_comments') {
            $query->whereHas('comments', fn($q) => $q->where('is_resolved', false));
        }

        // Search text
        if (!empty($this->searchDoc)) {
            $s = '%' . trim($this->searchDoc) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('document_number', 'like', $s)
                  ->orWhere('title', 'like', $s)
                  ->orWhere('description', 'like', $s);
            });
        }

        // Dropdown Filters
        if (!empty($this->filterDiscipline)) {
            $query->where('discipline', $this->filterDiscipline);
        }
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }
        if (!empty($this->filterFileType)) {
            $query->where('file_type', $this->filterFileType);
        }

        // Sorting
        if ($this->sortBy === 'doc_ref') {
            $query->orderBy('document_number', $this->sortDirection);
        } elseif ($this->sortBy === 'date') {
            $query->orderBy('created_at', $this->sortDirection);
        } elseif ($this->sortBy === 'title') {
            $query->orderBy('title', $this->sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($this->perPage, ['*'], 'page', $this->currentPage);
    }

    public function getSelectedDocument(): ?CdeDocument
    {
        if (!$this->selectedDocId) {
            return null;
        }

        return CdeDocument::with(['uploadedBy', 'folder', 'versions.uploader', 'comments.user'])
            ->find($this->selectedDocId);
    }

    public function getAllProjectComments()
    {
        $query = CdeDocumentComment::where(function ($q) {
            $q->whereHas('document', fn($d) => $d->where('cde_project_id', $this->record->id))
              ->orWhereHas('folder', fn($f) => $f->where('cde_project_id', $this->record->id));
        })
        ->whereNull('parent_id')
        ->with(['document', 'folder', 'user', 'replies.user'])
        ->orderBy('created_at', 'desc');

        if ($this->commentFilterStatus === 'open') {
            $query->where('is_resolved', false);
        } elseif ($this->commentFilterStatus === 'resolved') {
            $query->where('is_resolved', true);
        }

        if (!empty($this->commentSearch)) {
            $s = '%' . trim($this->commentSearch) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('comment', 'like', $s)
                  ->orWhereHas('document', fn($d) => $d->where('document_number', 'like', $s)->orWhere('title', 'like', $s))
                  ->orWhereHas('folder', fn($f) => $f->where('name', 'like', $s));
            });
        }

        return $query->get();
    }

    // ── File Downloads & Shares ───────────────────────────────────────
    public function downloadDocument(int $docId)
    {
        $doc = CdeDocument::find($docId);
        if (!$doc) {
            Notification::make()->title('Document not found')->danger()->send();
            return;
        }

        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            return response()->streamDownload(
                fn() => print (Storage::disk('public')->get($doc->file_path)),
                ($doc->document_number ?: 'DOC') . '.' . ($doc->file_type ?: 'bin'),
                ['Content-Type' => Storage::disk('public')->mimeType($doc->file_path)]
            );
        }

        // If sample file without disk content, generate manifest file
        $content = "ASITE CDE Document\n" .
            "Doc Ref: {$doc->document_number}\n" .
            "Title: {$doc->title}\n" .
            "Revision: {$doc->revision}\n" .
            "Status: {$doc->status}\n" .
            "Discipline: {$doc->discipline}\n" .
            "Date: {$doc->created_at}\n";

        return response()->streamDownload(
            fn() => print ($content),
            ($doc->document_number ?: 'DOC') . '.txt',
            ['Content-Type' => 'text/plain']
        );
    }

    public function openShareModal(int $docId): void
    {
        $this->sharingDocumentId = $docId;
        $this->showShareModal = true;
    }

    public function shareWithUser(int $userId, string $permission = 'download'): void
    {
        if (!$this->sharingDocumentId) {
            return;
        }

        $existing = DocumentShare::where('cde_document_id', $this->sharingDocumentId)
            ->where('shared_with', $userId)->first();

        if ($existing) {
            $existing->update(['permission' => $permission, 'is_active' => true]);
            Notification::make()->title('Share permission updated.')->success()->send();
        } else {
            DocumentShare::create([
                'cde_document_id' => $this->sharingDocumentId,
                'shared_by' => auth()->id(),
                'shared_with' => $userId,
                'permission' => $permission,
                'is_active' => true,
            ]);
            Notification::make()->title('Document shared.')->success()->send();
        }
    }

    public function generateShareLink(int $docId, string $permission = 'download', int $expiryDays = 7): string
    {
        $share = DocumentShare::create([
            'cde_document_id' => $docId,
            'shared_by' => auth()->id(),
            'permission' => $permission,
            'share_token' => \Illuminate\Support\Str::random(40),
            'expires_at' => $expiryDays > 0 ? now()->addDays($expiryDays) : null,
            'is_active' => true,
        ]);

        $url = route('documents.public-share', ['token' => $share->share_token]);
        Notification::make()->title('Share link generated')
            ->body($url)
            ->success()->send();

        return $url;
    }

    // ── Pipeline & Matrix methods ─────────────────────────────────────
    public function getSubfolders(): \Illuminate\Database\Eloquent\Collection
    {
        return CdeFolder::where('cde_project_id', $this->record->id)
            ->where('parent_id', $this->currentFolderId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getStatusPipeline(): array
    {
        $docs = CdeDocument::where('cde_project_id', $this->record->id);

        return [
            ['label' => 'WIP', 'count' => (clone $docs)->whereIn('status', ['wip', 'S0'])->count(), 'bg' => '#f1f5f9', 'color' => '#475569'],
            ['label' => 'For Review', 'count' => (clone $docs)->whereIn('status', ['draft', 'S1', 'S2'])->count(), 'bg' => '#dbeafe', 'color' => '#2563eb'],
            ['label' => 'Under Review', 'count' => (clone $docs)->whereIn('status', ['under_review', 'S3'])->count(), 'bg' => '#fef3c7', 'color' => '#d97706'],
            ['label' => 'Approved', 'count' => (clone $docs)->whereIn('status', ['approved', 'S4', 'S6'])->count(), 'bg' => '#dcfce7', 'color' => '#16a34a'],
            ['label' => 'Published', 'count' => (clone $docs)->whereIn('status', ['published', 'S7'])->count(), 'bg' => '#f3e8ff', 'color' => '#7c3aed'],
        ];
    }

    public function getStats(): array
    {
        $p = $this->record;
        $totalDocs = $p->documents()->count();
        $totalFolders = $p->folders()->count();
        $recentUploads = $p->documents()->where('created_at', '>=', now()->subDays(7))->count();
        $transmittalCount = Transmittal::where('cde_project_id', $p->id)->count();
        $openRfis = Rfi::where('cde_project_id', $p->id)->whereNotIn('status', ['closed', 'void'])->count();
        $totalRfis = Rfi::where('cde_project_id', $p->id)->count();

        return [
            ['label' => 'Documents', 'value' => $totalDocs, 'sub' => $recentUploads . ' this week', 'sub_type' => 'info', 'primary' => true],
            ['label' => 'Folders', 'value' => $totalFolders, 'sub' => 'Organized'],
            ['label' => 'Transmittals', 'value' => $transmittalCount, 'sub' => 'File packages', 'sub_type' => 'info'],
            ['label' => 'Open RFIs', 'value' => $openRfis, 'sub' => $totalRfis . ' total', 'sub_type' => $openRfis > 0 ? 'warning' : 'success'],
        ];
    }

    public function getSubmissionMatrix(): array
    {
        $pid = $this->record->id;
        $docs = CdeDocument::where('cde_project_id', $pid)->get();

        $levels = [
            'S0' => ['label' => 'S0 WIP', 'color' => '#94a3b8'],
            'S1' => ['label' => 'S1 Coord', 'color' => '#3b82f6'],
            'S2' => ['label' => 'S2 Info', 'color' => '#0284c7'],
            'S3' => ['label' => 'S3 Review', 'color' => '#d97706'],
            'S4' => ['label' => 'S4 Appr', 'color' => '#16a34a'],
            'S7' => ['label' => 'S7 Handover', 'color' => '#7c3aed'],
        ];

        $disciplines = $docs->groupBy(fn($d) => $d->discipline ?: 'General');
        $matrix = [];

        foreach ($disciplines as $discipline => $disciplineDocs) {
            $row = ['discipline' => ucfirst($discipline), 'total' => $disciplineDocs->count()];
            foreach (array_keys($levels) as $status) {
                $row[$status] = $disciplineDocs->where('status', $status)->count();
            }
            $completed = $row['S4'] + $row['S7'];
            $row['completion_pct'] = $row['total'] > 0 ? round(($completed / $row['total']) * 100) : 0;
            $matrix[] = $row;
        }

        $total = $docs->count();
        $levelTotals = [];
        foreach (array_keys($levels) as $status) {
            $levelTotals[$status] = $docs->where('status', $status)->count();
        }

        return [
            'levels' => $levels,
            'matrix' => $matrix,
            'totals' => $levelTotals,
            'total_docs' => $total,
            'overall_completion' => $total > 0 ? round(((($levelTotals['S4'] ?? 0) + ($levelTotals['S7'] ?? 0)) / $total) * 100) : 0,
        ];
    }

    public function getDocumentHealth(): array
    {
        $pid = $this->record->id;

        $stale = CdeDocument::where('cde_project_id', $pid)
            ->whereNotIn('status', ['published', 'archived', 'approved', 'S4', 'S7'])
            ->where('updated_at', '<', now()->subDays(30))
            ->count();

        $reviewBacklog = CdeDocument::where('cde_project_id', $pid)
            ->whereIn('status', ['under_review', 'S3'])
            ->count();

        $needsRevision = CdeDocument::where('cde_project_id', $pid)
            ->whereIn('status', ['revision'])
            ->count();

        return [
            'stale' => $stale,
            'review_backlog' => $reviewBacklog,
            'needs_revision' => $needsRevision,
        ];
    }

    public function getTransmittals(): \Illuminate\Database\Eloquent\Collection
    {
        return Transmittal::where('cde_project_id', $this->record->id)
            ->with(['items.document', 'sender'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getRfis(): \Illuminate\Database\Eloquent\Collection
    {
        return Rfi::where('cde_project_id', $this->record->id)
            ->with(['assignedTo', 'raisedBy', 'document'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getSubmissionStats(): array
    {
        $stages = DocumentSubmission::$stages;
        $stats = [];
        foreach ($stages as $key => $label) {
            $subs = DocumentSubmission::where('cde_project_id', $this->record->id)->where('stage', $key)->get();
            $stats[$key] = [
                'label' => $label,
                'total' => $subs->count(),
                'pending' => $subs->where('status', 'pending')->count(),
                'submitted' => $subs->where('status', 'submitted')->count(),
                'approved' => $subs->where('status', 'approved')->count(),
            ];
        }
        return $stats;
    }

    public function getSubmissions()
    {
        return DocumentSubmission::where('cde_project_id', $this->record->id)
            ->with(['document', 'submitter', 'reviewer'])
            ->orderBy('stage')
            ->get();
    }

    public function addSubmission(array $data): void
    {
        DocumentSubmission::create([
            'cde_project_id' => $this->record->id,
            'company_id' => $this->record->company_id,
            'stage' => $data['stage'],
            'title' => $data['title'],
            'discipline' => $data['discipline'] ?? null,
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'cde_document_id' => $data['cde_document_id'] ?? null,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);
        Notification::make()->title('Deliverable added.')->success()->send();
    }

    // ── Table implementation for Filament HasTable compatibility ──────
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
                Tables\Columns\TextColumn::make('document_number')->label('Doc Ref')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('discipline')->badge(),
                Tables\Columns\TextColumn::make('revision')->badge()->color('warning'),
                Tables\Columns\TextColumn::make('status')->badge()->label('Suitability'),
                Tables\Columns\TextColumn::make('file_type')->label('Type')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d-M-Y')->label('Date'),
            ]);
    }
}
