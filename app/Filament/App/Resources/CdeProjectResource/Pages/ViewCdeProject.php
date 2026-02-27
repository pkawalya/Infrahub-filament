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
        $pid = $this->record->id;
        $now = now();
        $weekStart = $now->copy()->startOfWeek();

        // Single query for all task stats
        $taskStats = \DB::selectOne("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status NOT IN ('done','cancelled') THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status NOT IN ('done','cancelled') AND due_date < ? THEN 1 ELSE 0 END) as overdue
            FROM tasks WHERE cde_project_id = ?
        ", [$now, $pid]);

        // Single query for document/folder/rfi stats
        $docStats = \DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM cde_documents WHERE cde_project_id = ?) as documents,
                (SELECT COUNT(*) FROM cde_documents WHERE cde_project_id = ? AND created_at >= ?) as docs_this_week,
                (SELECT COUNT(*) FROM cde_folders WHERE cde_project_id = ?) as folders,
                (SELECT COUNT(*) FROM rfis WHERE cde_project_id = ?) as rfis
        ", [$pid, $pid, $weekStart, $pid, $pid]);

        // Single query for incident stats
        $incidentStats = \DB::selectOne("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status NOT IN ('closed','resolved') THEN 1 ELSE 0 END) as open
            FROM safety_incidents WHERE cde_project_id = ?
        ", [$pid]);

        return [
            'tasks_total' => (int) $taskStats->total,
            'tasks_open' => (int) $taskStats->open,
            'tasks_overdue' => (int) $taskStats->overdue,
            'documents' => (int) $docStats->documents,
            'docs_this_week' => (int) $docStats->docs_this_week,
            'folders' => (int) $docStats->folders,
            'rfis' => (int) $docStats->rfis,
            'incidents' => (int) $incidentStats->total,
            'incidents_open' => (int) $incidentStats->open,
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
