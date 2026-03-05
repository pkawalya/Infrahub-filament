<?php

namespace App\Observers;

use App\Jobs\SyncBoqVarianceByProductJob;
use App\Models\MaterialIssuance;

/**
 * Observes MaterialIssuance lifecycle events.
 * When an issuance is marked as 'issued', dispatches a queued job
 * to recalculate BOQ variance for all matching products.
 */
class MaterialIssuanceObserver
{
    public function updated(MaterialIssuance $issuance): void
    {
        if (!$issuance->wasChanged('status') || $issuance->status !== 'issued') {
            return;
        }

        $this->dispatchVarianceSync($issuance);
    }

    public function created(MaterialIssuance $issuance): void
    {
        if ($issuance->status !== 'issued') {
            return;
        }

        $this->dispatchVarianceSync($issuance);
    }

    private function dispatchVarianceSync(MaterialIssuance $issuance): void
    {
        $projectId = $issuance->cde_project_id;
        if (!$projectId)
            return;

        $productIds = $issuance->items()->pluck('product_id')->unique()->filter();
        if ($productIds->isEmpty())
            return;

        foreach ($productIds as $productId) {
            SyncBoqVarianceByProductJob::dispatch($productId, $projectId);
        }
    }
}
