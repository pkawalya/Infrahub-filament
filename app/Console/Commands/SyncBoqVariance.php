<?php

namespace App\Console\Commands;

use App\Jobs\SyncBoqVarianceProjectJob;
use App\Models\CdeProject;
use Illuminate\Console\Command;

class SyncBoqVariance extends Command
{
    protected $signature = 'boq:sync-variance
                            {--project= : Specific project ID to sync}
                            {--all : Sync all active projects with BOQs}
                            {--sync : Run synchronously instead of queuing}';

    protected $description = 'Dispatch BOQ variance sync jobs for projects';

    public function handle(): int
    {
        $projectId = $this->option('project');
        $runSync = $this->option('sync');

        if ($projectId) {
            if ($runSync) {
                $this->info("Running BOQ variance sync for project #{$projectId} synchronously…");
                $service = app(\App\Services\BoqVarianceService::class);
                $stats = $service->syncProject((int) $projectId);
                $this->table(['Metric', 'Count'], [
                    ['Items Synced', $stats['items_synced']],
                    ['Alerts Created', $stats['alerts_created']],
                    ['Alerts Resolved', $stats['alerts_resolved']],
                ]);
            } else {
                SyncBoqVarianceProjectJob::dispatch((int) $projectId);
                $this->info("✓ Dispatched BOQ variance sync job for project #{$projectId}");
            }
            return 0;
        }

        if ($this->option('all')) {
            $projectIds = CdeProject::whereHas('boqs')->pluck('id');
            $this->info("Found {$projectIds->count()} project(s) with BOQs.");

            foreach ($projectIds as $pid) {
                if ($runSync) {
                    $this->line("  → Syncing project #{$pid}…");
                    $service = app(\App\Services\BoqVarianceService::class);
                    $service->syncProject($pid);
                } else {
                    SyncBoqVarianceProjectJob::dispatch($pid);
                }
            }

            $this->info(
                $runSync
                ? '✓ All projects synced.'
                : "✓ Dispatched {$projectIds->count()} variance sync job(s) to queue."
            );
            return 0;
        }

        $this->error('Please specify --project=ID or --all');
        return 1;
    }
}
