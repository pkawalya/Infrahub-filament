<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;

class FinancialPrintController extends Controller
{
    public function printInvoice(Invoice $invoice)
    {
        // SECURITY: Verify the invoice belongs to the user's company
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }
        if (!$user->isSuperAdmin() && $invoice->company_id !== $user->company_id) {
            abort(403, 'You do not have access to this invoice.');
        }

        $invoice->load(['client', 'cdeProject', 'workOrder', 'items', 'payments.recorder', 'creator']);
        $company = $invoice->company ?? $user->company;

        return view('print.invoice', compact('invoice', 'company'));
    }

    public function printReceipt(InvoicePayment $payment)
    {
        // SECURITY: Verify the receipt belongs to the user's company
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $payment->load(['invoice.client', 'invoice.company', 'recorder']);

        if (!$user->isSuperAdmin() && ($payment->invoice?->company_id !== $user->company_id)) {
            abort(403, 'You do not have access to this receipt.');
        }

        $company = $payment->invoice?->company ?? $user->company;

        return view('print.receipt', compact('payment', 'company'));
    }
}
