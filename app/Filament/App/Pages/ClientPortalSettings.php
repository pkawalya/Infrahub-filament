<?php

namespace App\Filament\App\Pages;

use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use BackedEnum;
use UnitEnum;

class ClientPortalSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?string $title = 'Client Portal';
    protected static ?string $navigationLabel = 'Client Portal';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.app.pages.client-portal-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isCompanyAdmin());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function mount(): void
    {
        $company = auth()->user()?->company;
        if (!$company) {
            return;
        }

        $settings = $company->settings['client_portal'] ?? [];

        $this->form->fill([
            'welcome_message' => $settings['welcome_message'] ?? 'Welcome to your project portal. Here you can track project progress, view documents, and review invoices.',
            'show_projects' => $settings['show_projects'] ?? true,
            'show_documents' => $settings['show_documents'] ?? true,
            'show_invoices' => $settings['show_invoices'] ?? true,
            'show_project_progress' => $settings['show_project_progress'] ?? true,
            'show_project_team' => $settings['show_project_team'] ?? false,
            'show_project_timeline' => $settings['show_project_timeline'] ?? true,
            'show_project_budget' => $settings['show_project_budget'] ?? false,
            'allowed_document_statuses' => $settings['allowed_document_statuses'] ?? ['approved'],
            'support_email' => $settings['support_email'] ?? $company->email,
            'support_phone' => $settings['support_phone'] ?? $company->phone,
            'portal_notice' => $settings['portal_notice'] ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Welcome & Branding')
                    ->description('Customize the greeting clients see when they log in.')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Forms\Components\Textarea::make('data.welcome_message')
                            ->label('Welcome Message')
                            ->rows(3)
                            ->helperText('Displayed on the client dashboard. Use this to guide clients on what they can do.')
                            ->placeholder('Welcome to your project portal...'),
                        Forms\Components\Textarea::make('data.portal_notice')
                            ->label('Portal Notice / Announcement')
                            ->rows(2)
                            ->helperText('Optional banner message shown at the top of the client dashboard. Leave empty to hide.')
                            ->placeholder('e.g. Office closure: Dec 24–Jan 2'),
                    ]),

                Section::make('Visibility Controls')
                    ->description('Choose what sections clients can see in their portal. Toggle off any section you don\'t want clients to access.')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Toggle::make('data.show_projects')
                                ->label('Projects')
                                ->helperText('Clients can see their assigned projects.')
                                ->default(true),
                            Forms\Components\Toggle::make('data.show_documents')
                                ->label('Documents')
                                ->helperText('Clients can view approved project documents.')
                                ->default(true),
                            Forms\Components\Toggle::make('data.show_invoices')
                                ->label('Invoices')
                                ->helperText('Clients can see sent invoices and payment status.')
                                ->default(true),
                            Forms\Components\Toggle::make('data.show_project_progress')
                                ->label('Project Progress')
                                ->helperText('Show task completion percentage on project cards.')
                                ->default(true),
                        ]),
                    ]),

                Section::make('Project Detail Visibility')
                    ->description('Control what clients see when viewing individual project details.')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Toggle::make('data.show_project_timeline')
                                ->label('Timeline / Dates')
                                ->helperText('Show project start and end dates.')
                                ->default(true),
                            Forms\Components\Toggle::make('data.show_project_team')
                                ->label('Project Team')
                                ->helperText('Show the list of team members assigned to the project.')
                                ->default(false),
                            Forms\Components\Toggle::make('data.show_project_budget')
                                ->label('Budget')
                                ->helperText('Show the project budget amount. Off by default for confidentiality.')
                                ->default(false),
                        ]),
                    ])
                    ->collapsed(),

                Section::make('Document Access')
                    ->description('Control which document statuses are visible to clients.')
                    ->icon('heroicon-o-folder-open')
                    ->schema([
                        Forms\Components\CheckboxList::make('data.allowed_document_statuses')
                            ->label('Document Statuses Visible to Clients')
                            ->options([
                                'approved' => 'Approved — Default, safe to share',
                                'published' => 'Published — Final deliverables',
                                'shared' => 'Shared — Explicitly shared for client review',
                            ])
                            ->default(['approved'])
                            ->helperText('Only documents with these statuses will appear in the client portal. Start with "Approved" only.'),
                    ])
                    ->collapsed(),

                Section::make('Support Information')
                    ->description('Contact details shown to clients if they need help.')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('data.support_email')
                                ->label('Support Email')
                                ->email()
                                ->placeholder('support@company.com'),
                            Forms\Components\TextInput::make('data.support_phone')
                                ->label('Support Phone')
                                ->placeholder('+256 xxx xxx xxx'),
                        ]),
                    ])
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $company = auth()->user()?->company;

        if (!$company) {
            Notification::make()
                ->title('Error')
                ->body('No company found for your account.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();

        // Merge into existing company settings JSON
        $settings = $company->settings ?? [];
        $settings['client_portal'] = [
            'welcome_message' => $data['welcome_message'] ?? '',
            'portal_notice' => $data['portal_notice'] ?? '',
            'show_projects' => (bool) ($data['show_projects'] ?? true),
            'show_documents' => (bool) ($data['show_documents'] ?? true),
            'show_invoices' => (bool) ($data['show_invoices'] ?? true),
            'show_project_progress' => (bool) ($data['show_project_progress'] ?? true),
            'show_project_team' => (bool) ($data['show_project_team'] ?? false),
            'show_project_timeline' => (bool) ($data['show_project_timeline'] ?? true),
            'show_project_budget' => (bool) ($data['show_project_budget'] ?? false),
            'allowed_document_statuses' => $data['allowed_document_statuses'] ?? ['approved'],
            'support_email' => $data['support_email'] ?? '',
            'support_phone' => $data['support_phone'] ?? '',
        ];

        $company->update(['settings' => $settings]);

        Notification::make()
            ->title('Client Portal Settings Saved! ✨')
            ->body('Changes will take effect immediately for all client users.')
            ->success()
            ->send();
    }
}
