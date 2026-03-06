<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — {{ $payment->invoice->invoice_number ?? 'Payment' }}</title>
    @php $brandColor = $company->primary_color ?? '#059669'; @endphp
    <style>
        @page {
            size: auto;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
            font-size: 13px;
            line-height: 1.6;
            background: #fff;
        }

        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 32px;
            border-bottom: 3px solid
                {{ $brandColor }}
            ;
            padding-bottom: 24px;
        }

        .receipt-header h1 {
            font-size: 28px;
            color:
                {{ $brandColor }}
            ;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 800;
        }

        .receipt-header .company-name {
            font-size: 18px;
            color: #374151;
            margin-top: 4px;
            font-weight: 600;
        }

        .receipt-header .rec-meta {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
        }

        .receipt-body {
            padding: 20px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .detail-row .label {
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .detail-row .value {
            font-weight: 600;
            color: #111827;
        }

        .detail-row.amount {
            background: #ecfdf5;
            border-radius: 8px;
            padding: 14px 16px;
            border: none;
            margin-top: 16px;
        }

        .detail-row.amount .value {
            font-size: 22px;
            font-weight: 800;
            color: #059669;
        }

        .receipt-stamp {
            text-align: center;
            margin: 32px 0;
        }

        .receipt-stamp .stamp {
            display: inline-block;
            border: 3px solid
                {{ $brandColor }}
            ;
            border-radius: 12px;
            padding: 8px 32px;
            color:
                {{ $brandColor }}
            ;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            transform: rotate(-5deg);
        }

        .receipt-footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
            text-align: center;
            color: #9ca3af;
            font-size: 11px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .receipt-container {
                padding: 15mm 12mm 20mm 12mm;
                max-width: 100%;
            }

            .receipt-stamp .stamp {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="no-print"
        style="background: #f3f4f6; padding: 12px; text-align: center; border-bottom: 1px solid #e5e7eb;">
        <button onclick="window.print()"
            style="background: {{ $brandColor }}; color: white; border: none; padding: 10px 28px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; margin-right: 8px;">🖨️
            Print Receipt</button>
        <button onclick="window.history.back()"
            style="background: #6b7280; color: white; border: none; padding: 10px 28px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px;">←
            Back</button>
    </div>

    <div class="receipt-container">
        <div class="receipt-header">
            @if($company->getLogoUrl())
                <img src="{{ $company->getLogoUrl() }}" alt="{{ $company->name }}"
                    style="max-height:48px;max-width:200px;margin-bottom:12px;">
            @endif
            <h1 style="color: {{ $brandColor }};">Payment Receipt</h1>
            <div class="company-name">{{ $company->name ?? config('app.name') }}</div>
            <div class="rec-meta">
                @if($company->address ?? false){{ $company->address }} · @endif
                @if($company->phone ?? false){{ $company->phone }} · @endif
                @if($company->email ?? false){{ $company->email }}@endif
            </div>
        </div>

        <div class="receipt-body">
            <div class="detail-row">
                <span class="label">Receipt Date</span>
                <span class="value">{{ $payment->payment_date?->format('M d, Y') ?? now()->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Invoice #</span>
                <span class="value">{{ $payment->invoice->invoice_number ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Client</span>
                <span class="value">{{ $payment->invoice->client->name ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Payment Method</span>
                <span class="value">{{ ucwords(str_replace('_', ' ', $payment->payment_method ?? '—')) }}</span>
            </div>
            @if($payment->reference)
                <div class="detail-row">
                    <span class="label">Reference #</span>
                    <span class="value">{{ $payment->reference }}</span>
                </div>
            @endif
            <div class="detail-row">
                <span class="label">Recorded By</span>
                <span class="value">{{ $payment->recorder->name ?? '—' }}</span>
            </div>
            <div class="detail-row amount">
                <span class="label" style="font-size: 14px; color: #059669;">Amount Paid</span>
                <span class="value">{{ \App\Support\CurrencyHelper::format($payment->amount) }}</span>
            </div>

            @if($payment->invoice)
                <div
                    style="margin-top: 24px; padding: 16px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div
                        style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; font-weight: 700; margin-bottom: 8px;">
                        Invoice Summary</div>
                    <div class="detail-row" style="border-bottom: 1px solid #e5e7eb;">
                        <span class="label">Invoice Total</span>
                        <span
                            class="value">{{ \App\Support\CurrencyHelper::format($payment->invoice->total_amount) }}</span>
                    </div>
                    <div class="detail-row" style="border-bottom: 1px solid #e5e7eb;">
                        <span class="label">Total Paid</span>
                        <span class="value"
                            style="color: #059669;">{{ \App\Support\CurrencyHelper::format($payment->invoice->amount_paid) }}</span>
                    </div>
                    <div class="detail-row" style="border-bottom: none;">
                        <span class="label">Balance Due</span>
                        <span class="value"
                            style="color: {{ $payment->invoice->balance_due > 0 ? '#dc2626' : '#059669' }};">{{ \App\Support\CurrencyHelper::format($payment->invoice->balance_due) }}</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="receipt-stamp">
            <span class="stamp">✓ Paid</span>
        </div>

        @if($payment->notes)
            <div
                style="background: #f9fafb; border-radius: 8px; padding: 16px; margin-bottom: 20px; border: 1px solid #e5e7eb;">
                <div
                    style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; font-weight: 700; margin-bottom: 6px;">
                    Notes</div>
                <p style="color: #374151; font-size: 12px;">{{ $payment->notes }}</p>
            </div>
        @endif

        <div class="receipt-footer">
            <p><strong>Thank you for your payment!</strong></p>
            <p>{{ $company->name ?? config('app.name') }}</p>
        </div>
    </div>
</body>

</html>