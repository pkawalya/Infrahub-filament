<?php

namespace App\Observers;

use App\Jobs\SyncBoqVarianceByProductJob;
use App\Models\MaterialRequisitionItem;

/**
 * Observes MaterialRequisitionItem updates.
 * When quantity_issued changes, dispatches a queued variance sync job.
 */
class MaterialRequisitionItemObserver
{
    public function updated(MaterialRequisitionItem $item): void
    {
        if (!$item->wasChanged('quantity_issued')) {
            return;
        }

        $requisition = $item->requisition;
        if (!$requisition || !$requisition->cde_project_id || !$item->product_id) {
            return;
        }

        SyncBoqVarianceByProductJob::dispatch($item->product_id, $requisition->cde_project_id);
    }
}
