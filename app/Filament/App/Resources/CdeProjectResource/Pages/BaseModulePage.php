<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
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
}
