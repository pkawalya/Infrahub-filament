<?php

namespace App\Observers;

use App\Models\SafetyIncident;
use App\Services\ModuleNotificationService;

class SafetyIncidentObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(SafetyIncident $incident): void
    {
        $severity = $incident->severity ?? 'unknown';
        $slug = in_array($severity, ['critical', 'high']) ? 'safety-incident-critical' : 'safety-incident-reported';

        $vars = [
            'incident_title' => $incident->title ?? 'Safety Incident',
            'severity' => ucfirst($severity),
            'location' => $incident->location ?? '',
            'reported_by' => auth()->user()?->name ?? 'System',
            'project_name' => $incident->project?->name ?? '',
            'incident_date' => $incident->incident_date?->format('M d, Y') ?? now()->format('M d, Y'),
        ];

        $url = url("/app/safety-incidents/{$incident->id}");

        // Critical/high severity → notify company admins + project team
        if (in_array($severity, ['critical', 'high'])) {
            $this->notifications->notifyCompanyAdmins($slug, $incident->company_id, $vars, $url);
        }

        // Always notify project team
        if ($incident->cde_project_id) {
            $this->notifications->notifyProjectTeam(
                $slug,
                $incident->cde_project_id,
                $vars,
                $url,
                auth()->id()
            );
        }
    }
}
