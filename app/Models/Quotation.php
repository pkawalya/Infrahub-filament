<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes, BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'client_id',
        'quotation_number',
        'reference',
        'title',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'issue_date',
        'valid_until',
        'accepted_at',
        'notes',
        'terms_and_conditions',
        'scope_of_work',
        'converted_invoice_id',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
        'accepted_at' => 'date',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'viewed' => 'Viewed',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        'invoiced' => 'Invoiced',
        'cancelled' => 'Cancelled',
    ];

    // ─── Relationships ───────────────────────────────────────────
    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function convertedInvoice()
    {
        return $this->belongsTo(Invoice::class, 'converted_invoice_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast() && !in_array($this->status, ['accepted', 'invoiced']);
    }

    public function canConvert(): bool
    {
        return in_array($this->status, ['accepted']) && !$this->converted_invoice_id;
    }

    /**
     * Recalculate totals from line items.
     */
    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum('amount');
        $taxAmount = $subtotal * ($this->tax_rate / 100);
        $total = $subtotal + $taxAmount - ($this->discount_amount ?? 0);

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => max(0, $total),
        ]);
    }

    /**
     * Convert this quotation to an invoice.
     */
    public function convertToInvoice(): Invoice
    {
        $invoice = Invoice::create([
            'company_id' => $this->company_id,
            'cde_project_id' => $this->cde_project_id,
            'quotation_id' => $this->id,
            'invoice_number' => 'INV-' . str_pad((string) (Invoice::where('company_id', $this->company_id)->count() + 1), 5, '0', STR_PAD_LEFT),
            'client_id' => $this->client_id,
            'subtotal' => $this->subtotal,
            'tax_rate' => $this->tax_rate,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'amount_paid' => 0,
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'notes' => $this->notes,
            'terms_and_conditions' => $this->terms_and_conditions,
            'created_by' => auth()->id(),
        ]);

        // Copy line items
        foreach ($this->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'amount' => $item->amount,
                'sort_order' => $item->sort_order,
            ]);
        }

        $this->update([
            'status' => 'invoiced',
            'converted_invoice_id' => $invoice->id,
        ]);

        return $invoice;
    }
}
