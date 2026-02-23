<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;

class FinancialPrintController extends Controller
{
    public function printInvoice(Invoice $invoice)
    {
        $invoice->load(['client', 'cdeProject', 'workOrder', 'items', 'payments.recorder', 'creator']);
        $company = $invoice->company ?? auth()->user()?->company;

        return view('print.invoice', compact('invoice', 'company'));
    }

    public function printReceipt(InvoicePayment $payment)
    {
        $payment->load(['invoice.client', 'invoice.company', 'recorder']);
        $company = $payment->invoice?->company ?? auth()->user()?->company;

        return view('print.receipt', compact('payment', 'company'));
    }
}
