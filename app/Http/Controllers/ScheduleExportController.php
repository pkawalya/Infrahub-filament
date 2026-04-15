<?php

namespace App\Http\Controllers;

use App\Models\CdeProject;
use App\Services\MsProjectService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScheduleExportController extends Controller
{
    public function exportMsProject(int $record): StreamedResponse
    {
        $project = CdeProject::findOrFail($record);

        // Ensure the authenticated user belongs to this project's company
        abort_unless(
            auth()->user()?->company_id === $project->company_id,
            403,
            'Access denied.'
        );

        $xml = app(MsProjectService::class)->export(
            projectId: $project->id,
            projectName: $project->name,
            projectStart: $project->start_date ?? null,
        );

        $filename = 'project-' . str($project->name)->slug() . '-' . now()->format('Ymd') . '.xml';

        return response()->streamDownload(
            function () use ($xml) {
                echo $xml;
            },
            $filename,
            [
                'Content-Type' => 'application/xml',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}
