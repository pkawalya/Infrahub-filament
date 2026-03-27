<?php

namespace App\Jobs;

use App\Services\BoqVarianceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Sync BOQ variance for a specific product within a project.
 * Dispatched automatically by MaterialIssuance / MaterialRequisitionItem observers.
 */
class SyncBoqVarianceByProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public int $productId,
        public int $projectId,
    ) {
        $this->onQueue('boq');
    }

    public function handle(BoqVarianceService $service): void
    {
        $service->syncByProduct($this->productId, $this->projectId);

        Log::info("BOQ variance synced for product #{$this->productId} in project #{$this->projectId}");
    }

    public function tags(): array
    {
        return ['boq-variance', "product:{$this->productId}", "project:{$this->projectId}"];
    }

    public function failed(\Throwable $e): void
    {
        Log::error('BOQ variance by-product sync job failed', [
            'job'        => static::class,
            'product_id' => $this->productId,
            'project_id' => $this->projectId,
            'error'      => $e->getMessage(),
            'trace'      => $e->getTraceAsString(),
        ]);
    }
}
