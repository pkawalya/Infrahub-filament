<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\ModuleNotificationService;

class InvoiceObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function updated(Invoice $invoice): void
    {
        if (!$invoice->isDirty('status'))
            return;

        $vars = [
            'invoice_number' => $invoice->invoice_number ?? '',
            'total_amount' => number_format($invoice->total_amount ?? 0, 2),
            'due_date' => $invoice->due_date?->format('M d, Y') ?? '',
            'project_name' => $invoice->project?->name ?? '',
            'client_name' => $invoice->client?->name ?? '',
        ];

        $url = url("/app/invoices/{$invoice->id}");

        match ($invoice->status) {
            'sent' => $this->notifications->notifyCompanyAdmins(
                'invoice-sent',
                $invoice->company_id,
                $vars,
                $url
            ),
            'paid' => $this->notifications->notifyCompanyAdmins(
                'invoice-paid',
                $invoice->company_id,
                $vars,
                $url
            ),
            'overdue' => $this->notifications->notifyCompanyAdmins(
                'invoice-overdue-alert',
                $invoice->company_id,
                $vars,
                $url
            ),
            default => null,
        };
    }
}
