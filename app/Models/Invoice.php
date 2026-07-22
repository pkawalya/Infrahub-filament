<?php

namespace App\Models;

use App\Models\CdeActivityLog;
use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\HasHashedRouteKey;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes, BelongsToCompany, HasHashedRouteKey, LogsActivity;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'invoice_number',
        'work_order_id',
        'quotation_id',
        'client_id',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'status',
        'issue_date',
        'due_date',
        'notes',
        'created_by',
        'reminder_sent_at',
        'reminder_count',
        'terms_and_conditions',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'reminder_sent_at' => 'date',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'tax_rate' => 'decimal:2',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'partially_paid' => 'Partially Paid',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ];

    public static array $validTransitions = [
        'draft' => ['sent'],
        'sent' => ['partially_paid', 'paid', 'overdue', 'cancelled'],
        'partially_paid' => ['paid', 'sent'],
        'paid' => [],
        'overdue' => ['sent', 'partially_paid', 'paid'],
        'cancelled' => [],
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        if ($this->status === $newStatus) {
            return true;
        }

        $allowed = static::$validTransitions[$this->status] ?? [];

        return in_array($newStatus, $allowed, true);
    }

    public function transitionTo(string $newStatus): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            $allowed = static::$validTransitions[$this->status] ?? [];
            throw new \InvalidArgumentException(
                "Cannot transition invoice '{$this->invoice_number}' from '{$this->status}' to '{$newStatus}'. " .
                "Allowed transitions: " . implode(', ', $allowed)
            );
        }

        $fromStatus = $this->status;
        $result = $this->update(['status' => $newStatus]);

        if ($result) {
            CdeActivityLog::record(
                $this,
                'status_changed',
                "Invoice '{$this->invoice_number}' status changed from '{$fromStatus}' to '{$newStatus}'",
                ['from' => $fromStatus, 'to' => $newStatus],
            );
        }

        return $result;
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function items()
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }
    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cdeProject()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function getBalanceDueAttribute(): float
    {
        return $this->total_amount - $this->amount_paid;
    }
}
