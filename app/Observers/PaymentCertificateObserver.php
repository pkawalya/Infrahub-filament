<?php

namespace App\Observers;

use App\Models\PaymentCertificate;
use App\Models\User;
use App\Services\ModuleNotificationService;

class PaymentCertificateObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function updated(PaymentCertificate $cert): void
    {
        if (!$cert->isDirty('status'))
            return;

        $vars = [
            'cert_number' => $cert->certificate_number,
            'cert_type' => PaymentCertificate::$types[$cert->type] ?? $cert->type,
            'net_payable' => number_format($cert->net_payable, 2),
            'total_payable' => number_format($cert->total_payable, 2),
            'project_name' => $cert->project?->name ?? '',
            'period' => ($cert->period_from?->format('M d') ?? '') . ' - ' . ($cert->period_to?->format('M d, Y') ?? ''),
        ];

        $url = url("/app/payment-certificates/{$cert->id}");

        match ($cert->status) {
            'submitted' => $this->notifications->notifyCompanyAdmins(
                'payment-cert-submitted',
                $cert->company_id,
                $vars,
                $url
            ),
            'certified' => $this->notifyPreparer($cert, 'payment-cert-certified', $vars, $url),
            'paid' => $this->notifyPreparer($cert, 'payment-cert-paid', $vars, $url),
            'rejected' => $this->notifyPreparer($cert, 'payment-cert-rejected', array_merge($vars, [
                'rejection_reason' => $cert->rejection_reason ?? '',
            ]), $url),
            default => null,
        };
    }

    protected function notifyPreparer(PaymentCertificate $cert, string $slug, array $vars, string $url): void
    {
        $preparer = $cert->prepared_by ? User::find($cert->prepared_by) : null;
        if ($preparer && $preparer->id !== auth()->id()) {
            $this->notifications->notifyUser($slug, $preparer, $vars, $url);
        }
    }
}
