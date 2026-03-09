<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\Module;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Base class for project module pages.
 * Each module page extends this and specifies its module code.
 */
abstract class BaseModulePage extends Page
{
    use InteractsWithRecord;

    protected static string $resource = CdeProjectResource::class;

    /**
     * The module code this page represents (matches Module::$availableModules keys).
     */
    protected static string $moduleCode = '';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        // Verify that this module is enabled for the project
        if (static::$moduleCode && !$this->record->hasModule(static::$moduleCode)) {
            abort(403, 'This module is not enabled for this project.');
        }
    }

    /**
     * Check if this module page should be visible for a given project.
     */
    public static function canAccess(array $parameters = []): bool
    {
        if (!static::$moduleCode) {
            return true;
        }

        $record = $parameters['record'] ?? null;

        if ($record instanceof CdeProject) {
            return $record->hasModule(static::$moduleCode);
        }

        return true;
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? static::getNavigationLabel();
    }

    public function getBreadcrumbs(): array
    {
        return [
            CdeProjectResource::getUrl() => 'Projects',
            CdeProjectResource::getUrl('view', ['record' => $this->record]) => $this->record->name,
            static::getNavigationLabel(),
        ];
    }

    /**
     * Quick-create header actions scoped to the project's active modules.
     */
    protected function getHeaderActions(): array
    {
        $r = $this->record;

        // Define quick-create actions mapped to module codes
        $quickActions = [];

        if ($r->hasModule('task_workflow')) {
            $quickActions[] = Action::make('quickTask')
                ->label('Task')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('primary')
                ->url(fn() => route('filament.app.resources.cde-projects.module-task-workflow', ['record' => $r->id]));
        }

        if ($r->hasModule('cde')) {
            $quickActions[] = Action::make('quickDocument')
                ->label('Document')
                ->icon('heroicon-o-document-plus')
                ->color('info')
                ->url(fn() => route('filament.app.resources.cde-projects.module-cde', ['record' => $r->id]));
        }

        if ($r->hasModule('cde')) {
            $quickActions[] = Action::make('quickRfi')
                ->label('RFI')
                ->icon('heroicon-o-question-mark-circle')
                ->color('warning')
                ->url(fn() => route('filament.app.resources.cde-projects.module-rfi-submittals', ['record' => $r->id]));
        }

        if ($r->hasModule('core')) {
            $quickActions[] = Action::make('quickWorkOrder')
                ->label('Work Order')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('success')
                ->url(fn() => route('filament.app.resources.cde-projects.module-core', ['record' => $r->id]));
        }

        if ($r->hasModule('financials')) {
            $quickActions[] = Action::make('quickInvoice')
                ->label('Invoice')
                ->icon('heroicon-o-banknotes')
                ->color('danger')
                ->url(fn() => route('filament.app.resources.cde-projects.module-financials', ['record' => $r->id]));
        }

        if ($r->hasModule('sheq')) {
            $quickActions[] = Action::make('quickIncident')
                ->label('Incident')
                ->icon('heroicon-o-shield-exclamation')
                ->color('danger')
                ->url(fn() => route('filament.app.resources.cde-projects.module-sheq', ['record' => $r->id]));
        }

        if (empty($quickActions)) {
            return [];
        }

        return [
            ActionGroup::make($quickActions)
                ->label('Quick Create')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->button()
                ->size('sm'),
        ];
    }
}
