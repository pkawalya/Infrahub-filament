<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\ProjectSuggestion;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;

class SuggestionBoxPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'suggestion_box';
    protected static ?string $title = 'Suggestion Box';
    protected string $view = 'filament.app.pages.modules.suggestion-box';
    protected static ?string $navigationLabel = 'Suggestions';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-light-bulb';

    protected function pid(): int
    {
        return $this->record->id;
    }

    protected function cid(): int
    {
        return $this->record->company_id;
    }

    protected function isManagerOrAdmin(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin()
            || $this->record->manager_id === $user?->id
            || $user?->user_type === 'company_admin';
    }

    public function getStats(): array
    {
        $pid = $this->pid();

        $stats = DB::selectOne("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
                SUM(CASE WHEN status = 'implemented' THEN 1 ELSE 0 END) as implemented,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN is_anonymous = 1 THEN 1 ELSE 0 END) as anonymous,
                SUM(upvotes) as total_votes,
                SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent_count,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week
            FROM project_suggestions WHERE cde_project_id = ?
        ", [now()->startOfWeek(), $pid]);

        $total = (int) $stats->total;
        $new = (int) $stats->new_count;
        $implemented = (int) $stats->implemented;

        return [
            [
                'label' => 'Total Suggestions',
                'value' => $total,
                'sub' => (int) $stats->anonymous . ' anonymous · ' . (int) $stats->this_week . ' this week',
                'sub_type' => 'neutral',
                'icon_svg' => Blade::render('<x-heroicon-o-light-bulb class="w-5 h-5 text-amber-600 dark:text-amber-400" />'),
                'icon_bg' => '#fef3c7',
                'primary' => true,
            ],
            [
                'label' => 'Awaiting Review',
                'value' => $new,
                'sub' => ((int) $stats->urgent_count > 0 ? (int) $stats->urgent_count . ' urgent · ' : '') . ($new > 0 ? 'Needs attention' : 'All caught up!'),
                'sub_type' => $new > 0 ? 'negative' : 'positive',
                'icon_svg' => Blade::render('<x-heroicon-o-inbox-stack class="w-5 h-5 text-blue-600 dark:text-blue-400" />'),
                'icon_bg' => '#dbeafe',
                'primary' => false,
            ],
            [
                'label' => 'Implemented',
                'value' => $implemented,
                'sub' => $total > 0 ? round(($implemented / $total) * 100) . '% action rate' : 'No suggestions yet',
                'sub_type' => 'positive',
                'icon_svg' => Blade::render('<x-heroicon-o-check-badge class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />'),
                'icon_bg' => '#d1fae5',
                'primary' => false,
            ],
            [
                'label' => 'Team Engagement',
                'value' => (int) $stats->total_votes,
                'sub' => 'Total upvotes · ' . (int) $stats->in_progress . ' in progress',
                'sub_type' => 'neutral',
                'icon_svg' => Blade::render('<x-heroicon-o-hand-thumb-up class="w-5 h-5 text-violet-600 dark:text-violet-400" />'),
                'icon_bg' => '#ede9fe',
                'primary' => false,
            ],
        ];
    }

    public function getHeaderActions(): array
    {
        $pid = $this->pid();
        $cid = $this->cid();

        return [
            Actions\Action::make('submitSuggestion')
                ->label('Submit Suggestion')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalHeading('Share Your Suggestion')
                ->modalDescription('Your feedback helps improve this project. You can submit anonymously — your identity will not be revealed to anyone.')
                ->modalIcon('heroicon-o-light-bulb')
                ->form([
                    Forms\Components\Select::make('category')
                        ->label('Category')
                        ->options(ProjectSuggestion::$categories)
                        ->default('general')
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('priority')
                        ->label('Priority')
                        ->options(ProjectSuggestion::$priorities)
                        ->default('normal')
                        ->required()
                        ->native(false)
                        ->helperText('Mark as urgent only if it affects safety or immediate operations.'),

                    Forms\Components\Textarea::make('content')
                        ->label('Your Suggestion')
                        ->placeholder('Describe your idea, concern, or improvement suggestion...')
                        ->rows(5)
                        ->required()
                        ->maxLength(2000)
                        ->helperText('Be specific about what you\'d like to see changed and why.'),

                ])
                ->action(function (array $data) use ($pid, $cid) {
                    ProjectSuggestion::create([
                        'company_id' => $cid,
                        'cde_project_id' => $pid,
                        'author_id' => null,  // Always anonymous
                        'is_anonymous' => true,
                        'category' => $data['category'],
                        'priority' => $data['priority'],
                        'content' => $data['content'],
                        'status' => 'new',
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Suggestion submitted!')
                        ->body('Your anonymous suggestion has been recorded. Your identity is never stored.')
                        ->send();
                })
                ->modalSubmitActionLabel('Submit Suggestion'),
        ];
    }

    public function table(Table $table): Table
    {
        $pid = $this->pid();
        $isManagerOrAdmin = $this->isManagerOrAdmin();

        // Only admins and project managers can see the submissions table
        if (!$isManagerOrAdmin) {
            return $table
                ->query(ProjectSuggestion::query()->whereRaw('0=1'))
                ->columns([])
                ->emptyStateHeading('Table not available')
                ->emptyStateDescription('Only project managers and administrators can view submitted suggestions.');
        }

        return $table
            ->query(
                ProjectSuggestion::query()
                    ->where('cde_project_id', $pid)
                    ->with(['author', 'responder'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('priority')
                    ->label('')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'urgent' => '🔴',
                        'high' => '🟠',
                        'normal' => '🟢',
                        'low' => '⚪',
                        default => '🟢',
                    })
                    ->tooltip(fn($record) => ucfirst($record->priority ?? 'normal') . ' priority')
                    ->alignCenter()
                    ->grow(false),

                Tables\Columns\TextColumn::make('author_display')
                    ->label('From')
                    ->getStateUsing(fn($record) => $record->author_display)
                    ->icon(fn($record) => $record->is_anonymous ? 'heroicon-o-eye-slash' : 'heroicon-o-user')
                    ->color(fn($record) => $record->is_anonymous ? 'gray' : 'primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'safety' => 'danger',
                        'process' => 'info',
                        'equipment' => 'warning',
                        'communication' => 'primary',
                        'work_conditions' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ProjectSuggestion::$categories[$state] ?? $state),

                Tables\Columns\TextColumn::make('content')
                    ->label('Suggestion')
                    ->limit(80)
                    ->tooltip(fn($record) => $record->content)
                    ->wrap(),

                Tables\Columns\TextColumn::make('upvotes')
                    ->label('👍')
                    ->badge()
                    ->color(fn(int $state) => $state > 5 ? 'success' : ($state > 0 ? 'primary' : 'gray'))
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'new' => 'info',
                        'reviewed' => 'warning',
                        'in_progress' => 'primary',
                        'implemented' => 'success',
                        'dismissed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ProjectSuggestion::$statuses[$state] ?? $state),

                Tables\Columns\TextColumn::make('admin_response')
                    ->label('Response')
                    ->limit(50)
                    ->placeholder('—')
                    ->tooltip(fn($record) => $record->admin_response),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ProjectSuggestion::$statuses),
                Tables\Filters\SelectFilter::make('category')
                    ->options(ProjectSuggestion::$categories),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(ProjectSuggestion::$priorities),
            ])
            ->recordActions(
                array_merge(
                    // Everyone can upvote
                    [
                        Actions\Action::make('upvote')
                            ->label(fn($record) => '👍 ' . $record->upvotes)
                            ->color('gray')
                            ->size('sm')
                            ->action(function ($record) {
                                $record->increment('upvotes');

                                Notification::make()
                                    ->success()
                                    ->title('Upvoted!')
                                    ->body('Thanks for your feedback.')
                                    ->duration(2000)
                                    ->send();
                            }),
                    ],
                    // Manager/Admin actions
                    $isManagerOrAdmin ? [
                        Actions\Action::make('respond')
                            ->label('Respond')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->color('primary')
                            ->form([
                                Forms\Components\Select::make('status')
                                    ->label('Update Status')
                                    ->options(ProjectSuggestion::$statuses)
                                    ->required()
                                    ->default(fn($record) => $record->status)
                                    ->native(false),
                                Forms\Components\Textarea::make('admin_response')
                                    ->label('Your Response')
                                    ->placeholder('Share your response to this suggestion...')
                                    ->rows(3)
                                    ->required()
                                    ->default(fn($record) => $record->admin_response),
                            ])
                            ->action(function ($record, array $data) {
                                $record->update([
                                    'status' => $data['status'],
                                    'admin_response' => $data['admin_response'],
                                    'responded_by' => auth()->id(),
                                    'responded_at' => now(),
                                ]);

                                Notification::make()
                                    ->success()
                                    ->title('Response saved')
                                    ->send();
                            }),
                        Actions\DeleteAction::make()
                            ->label('Remove'),
                    ] : []
                )
            )
            ->emptyStateHeading('No suggestions yet')
            ->emptyStateDescription('Be the first to share a suggestion! Click "Submit Suggestion" above to get started.')
            ->emptyStateIcon('heroicon-o-light-bulb');
    }
}
