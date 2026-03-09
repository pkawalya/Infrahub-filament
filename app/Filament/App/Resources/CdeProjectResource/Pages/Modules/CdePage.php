<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\CdeDocument;
use App\Models\CdeFolder;
use App\Models\CdeProject;
use App\Models\DocumentShare;
use App\Models\DocumentSubmission;
use App\Models\Transmittal;
use App\Models\TransmittalItem;
use App\Models\Rfi;
use App\Models\User;
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


    public ?int $currentFolderId = null;
    public string $folderPath = 'Root';
    public bool $showShareModal = false;
    public ?int $sharingDocumentId = null;
    public string $activeDocTab = 'files';

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
                ->size('xs')
                ->modalWidth('2xl')
                ->schema([
                    Forms\Components\FileUpload::make('file')
                        ->label('Select File')
                        ->directory('cde-uploads/' . $this->record->id)
                        ->maxSize(51200) // 50MB
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'application/vnd.ms-excel',
                            'application/vnd.ms-powerpoint',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                            'image/svg+xml',
                            'application/zip',
                            'application/x-rar-compressed',
                            'text/plain',
                            'text/csv',
                            'application/dxf',
                            'application/dwg',
                            'image/vnd.dwg',
                        ])
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
                ->size('xs')
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
                ->size('xs')
                ->visible(fn() => $this->currentFolderId !== null)
                ->action(function (): void {
                    if ($this->currentFolderId) {
                        $folder = CdeFolder::find($this->currentFolderId);
                        $this->navigateToFolder($folder?->parent_id);
                    }
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
                    ])->columns(2),
                ])
                ->action(function (array $data): void {
                    Rfi::create([
                        'company_id' => $this->record->company_id,
                        'cde_project_id' => $this->record->id,
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

            Action::make('addRequiredSubmission')
                ->label('Add Required Submission')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info')
                ->size('xs')
                ->modalWidth('xl')
                ->schema([
                    Section::make('Required Deliverable')->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Document / Report Title')
                            ->placeholder('e.g. Site Investigation Report')
                            ->required()->maxLength(255),
                        Forms\Components\Select::make('stage')
                            ->label('Project Stage')
                            ->options(DocumentSubmission::$stages)
                            ->required(),
                        Forms\Components\Select::make('discipline')
                            ->options(DocumentSubmission::$disciplines)
                            ->nullable()
                            ->searchable(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date')
                            ->nullable(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description / Requirements')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
                ])
                ->action(function (array $data): void {
                    $this->addSubmission($data);
                }),

            Action::make('submitDocumentAction')
                ->label('Submit Document')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->size('xs')
                ->modalWidth('lg')
                ->schema([
                    Forms\Components\FileUpload::make('file_path')
                        ->label('Upload Document')
                        ->directory('submission-uploads/' . ($this->record->id ?? 0))
                        ->disk('public')
                        ->maxSize(51200) // 50MB
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'application/vnd.ms-excel',
                            'application/vnd.ms-powerpoint',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                            'application/zip',
                            'text/plain',
                            'text/csv',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('review_notes')
                        ->label('Notes (optional)')
                        ->rows(2),
                ])
                ->action(function (array $data, array $arguments): void {
                    $submissionId = $arguments['submissionId'] ?? null;
                    if ($submissionId) {
                        $this->submitDeliverable((int) $submissionId, $data);
                    }
                })
                ->hidden(),
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

    // ── ISO 19650 Status Pipeline ─────────────────────────────────────
    public function getStatusPipeline(): array
    {
        $docs = CdeDocument::where('cde_project_id', $this->record->id);

        return [
            ['label' => 'WIP', 'count' => (clone $docs)->where('status', 'S0')->count(), 'bg' => '#f1f5f9', 'color' => '#475569'],
            ['label' => 'For Review', 'count' => (clone $docs)->whereIn('status', ['S1', 'S2'])->count(), 'bg' => '#dbeafe', 'color' => '#2563eb'],
            ['label' => 'Under Review', 'count' => (clone $docs)->where('status', 'S3')->count(), 'bg' => '#fef3c7', 'color' => '#d97706'],
            ['label' => 'Approved', 'count' => (clone $docs)->whereIn('status', ['S4', 'S6'])->count(), 'bg' => '#dcfce7', 'color' => '#16a34a'],
            ['label' => 'Published', 'count' => (clone $docs)->where('status', 'S7')->count(), 'bg' => '#f3e8ff', 'color' => '#7c3aed'],
        ];
    }

    // ── Stats ──────────────────────────────────────────────────────────
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
                'label' => 'Transmittals',
                'value' => $transmittalCount,
                'sub' => 'File packages',
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#8b5cf6" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>',
                'icon_bg' => '#f5f3ff'
            ],
            [
                'label' => 'Open RFIs',
                'value' => $openRfis,
                'sub' => $totalRfis . ' total',
                'sub_type' => $openRfis > 0 ? 'warning' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>',
                'icon_bg' => '#fffbeb'
            ],
        ];
    }

    /**
     * Submission matrix — tracks documents per discipline × status level.
     * Shows how many documents each discipline has submitted to each gate.
     */
    public function getSubmissionMatrix(): array
    {
        $pid = $this->record->id;
        $docs = CdeDocument::where('cde_project_id', $pid)->get();

        // Status levels ordered by progression
        $levels = [
            'wip' => ['label' => 'WIP', 'color' => '#94a3b8'],
            'draft' => ['label' => 'Draft', 'color' => '#475569'],
            'under_review' => ['label' => 'Under Review', 'color' => '#d97706'],
            'approved' => ['label' => 'Approved', 'color' => '#16a34a'],
            'revision' => ['label' => 'Revision', 'color' => '#ef4444'],
            'published' => ['label' => 'Published', 'color' => '#7c3aed'],
            'archived' => ['label' => 'Archived', 'color' => '#64748b'],
        ];

        // Group by discipline
        $disciplines = $docs->groupBy(fn($d) => $d->discipline ?: 'General');
        $matrix = [];

        foreach ($disciplines as $discipline => $disciplineDocs) {
            $row = ['discipline' => $discipline, 'total' => $disciplineDocs->count()];
            foreach (array_keys($levels) as $status) {
                $row[$status] = $disciplineDocs->where('status', $status)->count();
            }
            // Calculate completion — docs at approved/published/archived
            $completed = $row['approved'] + $row['published'] + $row['archived'];
            $row['completion_pct'] = $row['total'] > 0 ? round(($completed / $row['total']) * 100) : 0;
            $row['needs_action'] = $row['wip'] + $row['draft'] + $row['revision'];
            $matrix[] = $row;
        }

        // Sort by completion (lowest first, so needs-attention is at top)
        usort($matrix, fn($a, $b) => $a['completion_pct'] <=> $b['completion_pct']);

        // Overall summary
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
            'overall_completion' => $total > 0
                ? round((($levelTotals['approved'] + $levelTotals['published'] + $levelTotals['archived']) / $total) * 100)
                : 0,
        ];
    }

    /**
     * Document health indicators.
     */
    public function getDocumentHealth(): array
    {
        $pid = $this->record->id;

        // Stale documents — haven't been updated in 30+ days and not in final state
        $stale = CdeDocument::where('cde_project_id', $pid)
            ->whereNotIn('status', ['published', 'archived', 'approved'])
            ->where('updated_at', '<', now()->subDays(30))
            ->count();

        // Review backlog — documents waiting for review
        $reviewBacklog = CdeDocument::where('cde_project_id', $pid)
            ->where('status', 'under_review')
            ->count();

        // Revision required — documents needing correction
        $needsRevision = CdeDocument::where('cde_project_id', $pid)
            ->where('status', 'revision')
            ->count();

        // Recent uploads (7 days)
        $recentUploads = CdeDocument::where('cde_project_id', $pid)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Recent status changes (7 days)
        $recentUpdates = CdeDocument::where('cde_project_id', $pid)
            ->where('updated_at', '>=', now()->subDays(7))
            ->where('created_at', '<', now()->subDays(1)) // exclude brand-new
            ->count();

        return [
            'stale' => $stale,
            'review_backlog' => $reviewBacklog,
            'needs_revision' => $needsRevision,
            'recent_uploads' => $recentUploads,
            'recent_updates' => $recentUpdates,
        ];
    }

    public function getTransmittals(): \Illuminate\Database\Eloquent\Collection
    {
        return Transmittal::where('cde_project_id', $this->record->id)
            ->with(['sender:id,name', 'items.document:id,document_number,title'])
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();
    }

    public function getRfis(): \Illuminate\Database\Eloquent\Collection
    {
        return Rfi::where('cde_project_id', $this->record->id)
            ->with(['submitter:id,name', 'assignee:id,name'])
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();
    }

    public function answerRfi(int $rfiId, string $answer): void
    {
        $rfi = Rfi::find($rfiId);
        if ($rfi) {
            $rfi->update([
                'answer' => $answer,
                'status' => 'answered',
                'answered_at' => now(),
            ]);
            Notification::make()->title('RFI answered.')->success()->send();
        }
    }

    public function closeRfi(int $rfiId): void
    {
        $rfi = Rfi::find($rfiId);
        if ($rfi) {
            $rfi->update(['status' => 'closed']);
            Notification::make()->title('RFI closed.')->success()->send();
        }
    }

    public function reopenRfi(int $rfiId): void
    {
        $rfi = Rfi::find($rfiId);
        if ($rfi) {
            $rfi->update(['status' => 'open', 'answer' => null, 'answered_at' => null]);
            Notification::make()->title('RFI reopened.')->warning()->send();
        }
    }

    // ─── Sharing & Download ──────────────────────────────────────────
    public function downloadDocument(int $docId)
    {
        $doc = CdeDocument::find($docId);
        if (!$doc || !$doc->file_path || !Storage::disk('public')->exists($doc->file_path)) {
            Notification::make()->title('File not found')->danger()->send();
            return;
        }
        return response()->streamDownload(
            fn() => print (Storage::disk('public')->get($doc->file_path)),
            $doc->title . '.' . $doc->file_type,
            ['Content-Type' => Storage::disk('public')->mimeType($doc->file_path)]
        );
    }

    public function openShareModal(int $docId): void
    {
        $this->sharingDocumentId = $docId;
        $this->showShareModal = true;
    }

    public function shareWithUser(int $userId, string $permission = 'download'): void
    {
        if (!$this->sharingDocumentId)
            return;

        // Prevent duplicates
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
            ]);
            Notification::make()->title('Document shared successfully.')->success()->send();
        }
    }

    public function generateShareLink(int $docId, string $permission = 'download', ?int $expiryDays = 7): void
    {
        $token = DocumentShare::generateToken();
        DocumentShare::create([
            'cde_document_id' => $docId,
            'shared_by' => auth()->id(),
            'share_token' => $token,
            'permission' => $permission,
            'expires_at' => $expiryDays ? now()->addDays($expiryDays) : null,
        ]);

        $link = config('app.url') . '/share/doc/' . $token;
        $this->dispatch('copy-to-clipboard', text: $link);
        Notification::make()->title('Share link copied to clipboard!')->body($link)->success()->send();
    }

    public function revokeShare(int $shareId): void
    {
        $share = DocumentShare::find($shareId);
        if ($share) {
            $share->update(['is_active' => false]);
            Notification::make()->title('Share access revoked.')->warning()->send();
        }
    }

    public function getDocumentShares(int $docId): \Illuminate\Database\Eloquent\Collection
    {
        return DocumentShare::where('cde_document_id', $docId)
            ->where('is_active', true)
            ->with(['sharedWith:id,name,email', 'sharedBy:id,name'])
            ->latest()
            ->get();
    }

    // ── Required Submissions / Deliverables ─────────────────────────────

    /**
     * Get submissions for the current project, optionally filtered by stage.
     */
    public function getSubmissions(?string $stage = null): \Illuminate\Database\Eloquent\Collection
    {
        $q = DocumentSubmission::where('cde_project_id', $this->record->id)
            ->with(['submitter:id,name', 'reviewer:id,name'])
            ->orderByRaw("FIELD(status, 'rejected','overdue','pending','submitted','approved','waived')")
            ->orderBy('due_date');

        if ($stage) {
            $q->where('stage', $stage);
        }

        return $q->get();
    }

    /**
     * Stage-by-stage submission stats.
     */
    public function getSubmissionStats(): array
    {
        $pid = $this->record->id;
        $all = DocumentSubmission::where('cde_project_id', $pid)->get();

        $stats = [];
        foreach (DocumentSubmission::$stages as $key => $label) {
            $stageDocs = $all->where('stage', $key);
            $total = $stageDocs->count();
            $submitted = $stageDocs->whereIn('status', ['submitted', 'approved'])->count();
            $approved = $stageDocs->where('status', 'approved')->count();
            $overdue = $stageDocs->filter(fn($d) => $d->isOverdue())->count();
            $rejected = $stageDocs->where('status', 'rejected')->count();

            $stats[] = [
                'key' => $key,
                'label' => $label,
                'total' => $total,
                'submitted' => $submitted,
                'approved' => $approved,
                'overdue' => $overdue,
                'rejected' => $rejected,
                'completion' => $total > 0 ? round(($approved / $total) * 100) : 0,
            ];
        }

        return $stats;
    }

    /**
     * Add a required submission.
     */
    public function addSubmission(array $data): void
    {
        DocumentSubmission::create([
            'company_id' => $this->record->company_id,
            'cde_project_id' => $this->record->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'discipline' => $data['discipline'] ?? null,
            'stage' => $data['stage'],
            'due_date' => $data['due_date'] ?? null,
            'status' => 'pending',
        ]);

        Notification::make()->title('Required submission added.')->success()->send();
    }

    /**
     * Upload file and mark as submitted.
     */
    public function submitDeliverable(int $submissionId, array $data): void
    {
        $sub = DocumentSubmission::where('cde_project_id', $this->record->id)->find($submissionId);
        if (!$sub || !in_array($sub->status, ['pending', 'rejected']))
            return;

        $updates = [
            'status' => 'submitted',
            'submitted_at' => now(),
            'submitted_by' => auth()->id(),
        ];

        if (isset($data['file_path']) && $data['file_path']) {
            $filePath = $data['file_path'];
            if (is_array($filePath)) {
                $filePath = reset($filePath);
            }
            $updates['file_path'] = $filePath;
            $updates['file_name'] = basename($filePath);
            $updates['file_size'] = Storage::disk('public')->exists($filePath)
                ? Storage::disk('public')->size($filePath) : null;
        }

        if (isset($data['review_notes'])) {
            $updates['review_notes'] = $data['review_notes'];
        }

        $sub->update($updates);
        Notification::make()->title('Document submitted successfully.')->success()->send();
    }

    /**
     * Approve or reject a submission.
     */
    public function reviewSubmission(int $submissionId, string $decision, ?string $reason = null): void
    {
        if (!in_array($decision, ['approved', 'rejected'])) {
            return;
        }

        $sub = DocumentSubmission::where('cde_project_id', $this->record->id)->find($submissionId);
        if (!$sub || $sub->status !== 'submitted')
            return;

        $sub->update([
            'status' => $decision,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'rejection_reason' => $decision === 'rejected' ? $reason : null,
        ]);

        $msg = $decision === 'approved' ? 'Submission approved.' : 'Submission rejected.';
        Notification::make()->title($msg)->{$decision === 'approved' ? 'success' : 'danger'}()->send();
    }

    /**
     * Download the uploaded submission file.
     */
    public function downloadSubmission(int $submissionId)
    {
        $sub = DocumentSubmission::where('cde_project_id', $this->record->id)->find($submissionId);
        if (!$sub || !$sub->file_path) {
            Notification::make()->title('No file available.')->warning()->send();
            return null;
        }

        // Prevent directory traversal
        $safePath = ltrim($sub->file_path, '/');
        if (str_contains($safePath, '..') || str_starts_with($safePath, '/')) {
            Notification::make()->title('Invalid file path.')->danger()->send();
            return null;
        }

        if (!Storage::disk('public')->exists($safePath)) {
            Notification::make()->title('File not found on disk.')->danger()->send();
            return null;
        }

        return Storage::disk('public')->download($safePath, $sub->file_name ?? basename($safePath));
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
                Tables\Columns\TextColumn::make('document_number')->toggleable()
                    ->label('Doc #')->searchable()->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-document-text'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50)->toggleable(),
                Tables\Columns\TextColumn::make('discipline')->badge()->color('info')->toggleable(),
                Tables\Columns\TextColumn::make('revision')->badge()->color('warning')->toggleable(),
                Tables\Columns\TextColumn::make('status')->badge()->toggleable()
                    ->color(fn(?string $state) => match ($state) {
                        'S0' => 'gray', 'S1' => 'info', 'S2' => 'primary',
                        'S3' => 'warning', 'S4' => 'success', 'S6' => 'success', 'S7' => 'success',
                        default => 'gray',
                    })
                    ->label('Suitability'),
                Tables\Columns\TextColumn::make('file_type')->label('Type')->badge()->color('gray')->toggleable(),
                Tables\Columns\TextColumn::make('file_size')->toggleable()
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
                Tables\Columns\TextColumn::make('uploadedBy.name')->label('Uploaded By')->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M d, Y H:i')->sortable()->label('Uploaded')->toggleable(),
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
            ->recordActions([
                \Filament\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->tooltip('Download Document')
                    ->action(function (CdeDocument $record) {
                        return $this->downloadDocument($record->id);
                    }),

                \Filament\Actions\Action::make('share')
                    ->label('Share')
                    ->icon('heroicon-o-share')
                    ->color('info')
                    ->tooltip('Share Document')
                    ->modalWidth('lg')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Share with Team Member')
                            ->options(fn() => User::where('company_id', $this->record->company_id)->where('id', '!=', auth()->id())->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Select::make('permission')
                            ->label('Permission Level')
                            ->options(DocumentShare::$permissions)
                            ->default('download')
                            ->required(),
                        Forms\Components\Toggle::make('generate_link')
                            ->label('Also generate a public share link?')
                            ->default(false),
                        Forms\Components\Select::make('expiry_days')
                            ->label('Link Expires In')
                            ->options([
                                1 => '1 day',
                                3 => '3 days',
                                7 => '7 days',
                                30 => '30 days',
                                0 => 'Never',
                            ])
                            ->default(7)
                            ->visible(fn(Forms\Get|\Filament\Schemas\Components\Utilities\Get $get) => $get('generate_link')),
                    ])
                    ->action(function (array $data, CdeDocument $record): void {
                        if (!empty($data['user_id'])) {
                            $this->shareWithUser((int) $data['user_id'], $data['permission']);
                        }
                        if (!empty($data['generate_link'])) {
                            $this->generateShareLink($record->id, $data['permission'], (int) ($data['expiry_days'] ?? 7));
                        }
                        if (empty($data['user_id']) && empty($data['generate_link'])) {
                            Notification::make()->title('Please select a user or enable link sharing.')->warning()->send();
                        }
                    }),

                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('copyLink')
                        ->label('Copy Public Link')
                        ->icon('heroicon-o-link')
                        ->color('gray')
                        ->action(function (CdeDocument $record): void {
                            $this->generateShareLink($record->id, 'download', 7);
                        }),

                    \Filament\Actions\Action::make('manageShares')
                        ->label('Manage Shares')
                        ->icon('heroicon-o-user-group')
                        ->color('gray')
                        ->action(function (CdeDocument $record): void {
                            $this->openShareModal($record->id);
                        }),

                    \Filament\Actions\Action::make('revise')
                        ->label('New Revision')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->schema([
                            Forms\Components\FileUpload::make('file')
                                ->label('Updated File')
                                ->directory('cde-uploads/' . $this->record->id)
                                ->maxSize(51200)
                                ->acceptedFileTypes([
                                    'application/pdf',
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                    'application/vnd.ms-excel',
                                    'application/vnd.ms-powerpoint',
                                    'image/jpeg',
                                    'image/png',
                                    'image/gif',
                                    'image/webp',
                                    'image/svg+xml',
                                    'application/zip',
                                    'application/x-rar-compressed',
                                    'text/plain',
                                    'text/csv',
                                    'application/dxf',
                                    'application/dwg',
                                    'image/vnd.dwg',
                                ])
                                ->required(),
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

                    // ── Document Workflow Actions ──────────────────────────
                    \Filament\Actions\Action::make('submitForReview')
                        ->label('Submit for Review')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->visible(fn(CdeDocument $record) => in_array($record->status, ['wip', 'draft', 'revision', 'S0']))
                        ->requiresConfirmation()
                        ->modalHeading('Submit for Review')
                        ->modalDescription('This document will be submitted for review. The status will change to "Under Review".')
                        ->action(function (CdeDocument $record): void {
                            $oldStatus = $record->status;
                            $record->update(['status' => 'under_review']);
                            \App\Models\CdeActivityLog::record(
                                $record,
                                'submitted',
                                "Document {$record->document_number} submitted for review",
                                ['status' => ['from' => $oldStatus, 'to' => 'under_review']],
                            );
                            Notification::make()->title('Document submitted for review')->success()->send();
                        }),

                    \Filament\Actions\Action::make('approveDocument')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(CdeDocument $record) => in_array($record->status, ['under_review', 'S3', 'S4']))
                        ->schema([
                            Forms\Components\Textarea::make('comments')
                                ->label('Approval Comments')
                                ->rows(2)
                                ->placeholder('Optional comments...'),
                        ])
                        ->action(function (array $data, CdeDocument $record): void {
                            $record->update(['status' => 'approved']);
                            $desc = "Document {$record->document_number} approved";
                            if (!empty($data['comments'])) {
                                $desc .= ": {$data['comments']}";
                            }
                            \App\Models\CdeActivityLog::record(
                                $record,
                                'approved',
                                $desc,
                                ['status' => ['from' => 'under_review', 'to' => 'approved']],
                            );
                            Notification::make()->title('Document approved')->success()->send();
                        }),

                    \Filament\Actions\Action::make('rejectDocument')
                        ->label('Reject / Revise')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(CdeDocument $record) => in_array($record->status, ['under_review', 'S3', 'S4']))
                        ->schema([
                            Forms\Components\Textarea::make('reason')
                                ->label('Rejection Reason')
                                ->rows(3)
                                ->required(),
                        ])
                        ->action(function (array $data, CdeDocument $record): void {
                            $record->update(['status' => 'revision']);
                            \App\Models\CdeActivityLog::record(
                                $record,
                                'rejected',
                                "Document {$record->document_number} rejected: {$data['reason']}",
                                ['status' => ['from' => 'under_review', 'to' => 'revision'], 'reason' => $data['reason']],
                            );
                            Notification::make()->title('Document sent back for revision')->warning()->send();
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
                ]),
            ])
            ->toolbarActions([
                $this->exportCsvAction('documents', fn() => CdeDocument::query()->where('cde_project_id', $this->record->id)->with(['uploadedBy', 'folder']), [
                    'document_number' => 'Doc #',
                    'title' => 'Title',
                    'discipline' => 'Discipline',
                    'revision' => 'Revision',
                    'status' => 'Suitability',
                    'file_type' => 'File Type',
                    'file_size' => 'File Size',
                    'uploadedBy.name' => 'Uploaded By',
                    'folder.name' => 'Folder',
                    'created_at' => 'Uploaded At',
                ]),
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Documents')
            ->emptyStateDescription('Upload documents or create folders to organize your project files.')
            ->emptyStateIcon('heroicon-o-document-plus');
    }
}
