<?php

namespace App\Console\Commands;

use App\Models\DocumentSubmission;
use App\Models\Ncr;
use App\Models\SafetyInspection;
use App\Models\SnagItem;
use App\Models\CdeActivityLog;
use App\Services\ModuleNotificationService;
use Illuminate\Console\Command;

class EscalateOverdueItems extends Command
{
    protected $signature = 'sheq:escalate-overdue';

    protected $description = 'Escalate overdue NCRs, document submissions, inspections, and snags';

    public function handle(ModuleNotificationService $notifications): int
    {
        $this->info('Checking overdue items...');

        $this->escalateOverdueNcrs($notifications);
        $this->escalateOverdueSubmissions($notifications);
        $this->escalateOverdueInspections($notifications);
        $this->escalateOverdueSnags($notifications);

        $this->info('Done.');

        return Command::SUCCESS;
    }

    protected function escalateOverdueNcrs(ModuleNotificationService $notifications): void
    {
        $overdueNcrs = Ncr::where('due_date', '<', now())
            ->whereIn('status', ['open', 'investigating', 'corrective_action'])
            ->with(['project', 'assignee', 'reporter'])
            ->get();

        if ($overdueNcrs->isEmpty()) {
            return;
        }

        $this->info("Found {$overdueNcrs->count()} overdue NCR(s)");

        foreach ($overdueNcrs as $ncr) {
            $vars = [
                'ncr_number' => $ncr->ncr_number ?? '',
                'ncr_title' => $ncr->title ?? '',
                'status' => Ncr::$statuses[$ncr->status] ?? $ncr->status,
                'due_date' => $ncr->due_date?->format('M d, Y') ?? 'Unknown',
                'project_name' => $ncr->project?->name ?? '',
            ];

            $url = url("/app/ncrs/{$ncr->id}");

            // Notify assignee
            if ($ncr->assignee) {
                $notifications->notifyUser('ncr-overdue', $ncr->assignee, $vars, $url);
            }

            // Notify project team
            if ($ncr->cde_project_id) {
                $notifications->notifyProjectTeam('ncr-overdue', $ncr->cde_project_id, $vars, $url, $ncr->assignee?->id);
            }

            CdeActivityLog::record(
                $ncr,
                'escalated',
                "NCR '{$ncr->ncr_number}' escalated as overdue (due: {$ncr->due_date?->format('M d, Y')})",
            );
        }
    }

    protected function escalateOverdueSubmissions(ModuleNotificationService $notifications): void
    {
        $overdue = DocumentSubmission::where('due_date', '<', now())
            ->whereIn('status', ['pending', 'submitted'])
            ->with(['project', 'submitter'])
            ->get();

        if ($overdue->isEmpty()) {
            return;
        }

        $this->info("Found {$overdue->count()} overdue document submission(s)");

        foreach ($overdue as $submission) {
            $submission->update(['status' => 'overdue']);

            $vars = [
                'submission_title' => $submission->title ?? '',
                'due_date' => $submission->due_date?->format('M d, Y') ?? 'Unknown',
                'project_name' => $submission->project?->name ?? '',
            ];

            $url = url("/app/document-submissions/{$submission->id}");

            if ($submission->submitter) {
                $notifications->notifyUser('document-submission-overdue', $submission->submitter, $vars, $url);
            }

            if ($submission->cde_project_id) {
                $notifications->notifyProjectTeam('document-submission-overdue', $submission->cde_project_id, $vars, $url);
            }

            CdeActivityLog::record(
                $submission,
                'escalated',
                "Document submission '{$submission->title}' marked as overdue",
            );
        }
    }

    protected function escalateOverdueInspections(ModuleNotificationService $notifications): void
    {
        $overdue = SafetyInspection::where('scheduled_date', '<', now())
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->with(['project', 'inspector'])
            ->get();

        if ($overdue->isEmpty()) {
            return;
        }

        $this->info("Found {$overdue->count()} overdue inspection(s)");

        foreach ($overdue as $inspection) {
            $vars = [
                'inspection_title' => $inspection->title ?? '',
                'scheduled_date' => $inspection->scheduled_date?->format('M d, Y') ?? 'Unknown',
                'project_name' => $inspection->project?->name ?? '',
            ];

            $url = url("/app/safety-inspections/{$inspection->id}");

            if ($inspection->inspector) {
                $notifications->notifyUser('inspection-overdue', $inspection->inspector, $vars, $url);
            }

            if ($inspection->cde_project_id) {
                $notifications->notifyProjectTeam('inspection-overdue', $inspection->cde_project_id, $vars, $url, $inspection->inspector?->id);
            }

            CdeActivityLog::record(
                $inspection,
                'escalated',
                "Inspection '{$inspection->inspection_number}' overdue (scheduled: {$inspection->scheduled_date?->format('M d, Y')})",
            );
        }
    }

    protected function escalateOverdueSnags(ModuleNotificationService $notifications): void
    {
        $overdue = SnagItem::where('due_date', '<', now())
            ->whereIn('status', ['open', 'in_progress'])
            ->with(['project', 'assignee'])
            ->get();

        if ($overdue->isEmpty()) {
            return;
        }

        $this->info("Found {$overdue->count()} overdue snag(s)");

        foreach ($overdue as $snag) {
            $vars = [
                'snag_number' => $snag->snag_number ?? '',
                'snag_title' => $snag->title ?? '',
                'due_date' => $snag->due_date?->format('M d, Y') ?? 'Unknown',
                'severity' => ucfirst($snag->severity ?? ''),
                'project_name' => $snag->project?->name ?? '',
            ];

            $url = url("/app/snag-items/{$snag->id}");

            if ($snag->assignee) {
                $notifications->notifyUser('snag-overdue', $snag->assignee, $vars, $url);
            }

            if ($snag->cde_project_id) {
                $notifications->notifyProjectTeam('snag-overdue', $snag->cde_project_id, $vars, $url, $snag->assignee?->id);
            }

            CdeActivityLog::record(
                $snag,
                'escalated',
                "Snag '{$snag->snag_number}' overdue (due: {$snag->due_date?->format('M d, Y')})",
            );
        }
    }
}
