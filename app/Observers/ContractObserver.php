<?php

namespace App\Observers;

use App\Models\CdeActivityLog;
use App\Models\Contract;
use App\Models\User;
use App\Services\ModuleNotificationService;

class ContractObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(Contract $contract): void
    {
        $creator = User::find($contract->created_by);
        if ($creator) {
            $this->notifications->notifyUser('contract-created', $creator, [
                'contract_number' => $contract->contract_number ?? '',
                'contract_title' => $contract->title ?? '',
                'type' => $contract->type ?? '',
                'original_value' => number_format($contract->original_value ?? 0, 2),
            ], url("/app/contracts/{$contract->id}"));
        }
    }

    public function updated(Contract $contract): void
    {
        if (!$contract->isDirty('status')) {
            return;
        }

        $vars = [
            'contract_number' => $contract->contract_number ?? '',
            'contract_title' => $contract->title ?? '',
            'status' => Contract::$statuses[$contract->status] ?? ucfirst(str_replace('_', ' ', $contract->status)),
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/contracts/{$contract->id}");

        CdeActivityLog::record(
            $contract,
            'status_changed',
            "Contract '{$contract->contract_number}' status changed to '{$contract->status}'",
            ['from' => $contract->getOriginal('status'), 'to' => $contract->status],
        );

        $creator = User::find($contract->created_by);
        if ($creator && (int) $creator->id !== (int) auth()->id()) {
            $this->notifications->notifyUser("contract-{$contract->status}", $creator, $vars, $url);
        }
    }

    public function deleted(Contract $contract): void
    {
        $creator = User::find($contract->created_by);
        if ($creator) {
            $this->notifications->notifyUser('contract-deleted', $creator, [
                'contract_number' => $contract->contract_number ?? '',
                'contract_title' => $contract->title ?? '',
            ]);
        }
    }
}
