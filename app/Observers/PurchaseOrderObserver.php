<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\ModuleNotificationService;

class PurchaseOrderObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function updated(PurchaseOrder $po): void
    {
        if (!$po->isDirty('status'))
            return;

        $vars = [
            'po_number' => $po->po_number ?? '',
            'total_amount' => number_format($po->total_amount ?? 0, 2),
            'supplier' => $po->supplier?->name ?? '',
            'project_name' => $po->cdeProject?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/purchase-orders/{$po->id}");

        match ($po->status) {
            'submitted' => $this->notifications->notifyCompanyAdmins(
                'po-submitted',
                $po->company_id,
                $vars,
                $url
            ),
            'approved' => $this->notifyCreator($po, 'po-approved', $vars, $url),
            'rejected' => $this->notifyCreator($po, 'po-rejected', array_merge($vars, [
                'rejection_reason' => $po->rejection_reason ?? '',
            ]), $url),
            'received' => $this->notifyCreator($po, 'po-received', $vars, $url),
            default => null,
        };
    }

    protected function notifyCreator(PurchaseOrder $po, string $slug, array $vars, string $url): void
    {
        $creator = User::find($po->created_by);
        if ($creator && $creator->id !== auth()->id()) {
            $this->notifications->notifyUser($slug, $creator, $vars, $url);
        }
    }
}
