<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\Module;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCdeProject extends ViewRecord
{
    protected static string $resource = CdeProjectResource::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Overview';
    protected string $view = 'filament.app.pages.view-cde-project';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getStats(): array
    {
        $record = $this->record;

        return [
            'tasks_total' => $record->tasks()->count(),
            'tasks_open' => $record->tasks()->whereNotIn('status', ['done', 'cancelled'])->count(),
            'tasks_overdue' => $record->tasks()->where('due_date', '<', now())->whereNotIn('status', ['done', 'cancelled'])->count(),
            'documents' => $record->documents()->count(),
            'docs_this_week' => $record->documents()->where('created_at', '>=', now()->startOfWeek())->count(),
            'folders' => $record->folders()->count(),
            'rfis' => $record->rfis()->count(),
            'incidents' => $record->safetyIncidents()->count(),
            'incidents_open' => $record->safetyIncidents()->whereNotIn('status', ['closed', 'resolved'])->count(),
        ];
    }

    public function getRecentTasks(): \Illuminate\Support\Collection
    {
        return $this->record->tasks()
            ->with('assignee')
            ->whereNotIn('status', ['done', 'cancelled'])
            ->orderByRaw("CASE WHEN due_date < NOW() THEN 0 ELSE 1 END")
            ->orderBy('due_date')
            ->limit(5)
            ->get();
    }

    public function getRecentDocuments(): \Illuminate\Support\Collection
    {
        return $this->record->documents()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getEnabledModulesList(): array
    {
        $enabled = $this->record->getEnabledModules();
        $modules = Module::$availableModules;

        return collect($enabled)
            ->map(fn($code) => $modules[$code] ?? null)
            ->filter()
            ->values()
            ->toArray();
    }
}
