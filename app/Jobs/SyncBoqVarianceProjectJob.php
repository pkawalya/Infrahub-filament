<?php

namespace App\Jobs;

use App\Models\CdeProject;
use App\Services\BoqVarianceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Full BOQ variance sync for a specific project.
 * Dispatched by the "Sync Variance" button or by the scheduler.
 */
class SyncBoqVarianceProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 300; // 5 min max

    public function __construct(
        public int $projectId,
    ) {
        $this->onQueue('boq');
    }

    public function handle(BoqVarianceService $service): void
    {
        $stats = $service->syncProject($this->projectId);

        Log::info("BOQ variance project sync complete for project #{$this->projectId}", $stats);
    }

    public function tags(): array
    {
        return ['boq-variance', 'full-sync', "project:{$this->projectId}"];
    }
}
