<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes, BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'vendor_id',
        'contract_number',
        'title',
        'type',
        'status',
        'start_date',
        'end_date',
        'original_value',
        'revised_value',
        'amount_paid',
        'retainage_percent',
        'retainage_held',
        'retainage_released',
        'description',
        'scope',
        'scope_of_work',
        'terms',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'original_value' => 'decimal:2',
        'revised_value' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'retainage_percent' => 'decimal:2',
        'retainage_held' => 'decimal:2',
        'retainage_released' => 'decimal:2',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'completed' => 'Completed',
        'terminated' => 'Terminated',
        'suspended' => 'Suspended',
    ];

    /**
     * All projects this contract is linked to (many-to-many).
     */
    public function projects()
    {
        return $this->belongsToMany(CdeProject::class, 'contract_project', 'contract_id', 'cde_project_id')
            ->withPivot('budget_allocation', 'notes')
            ->withTimestamps();
    }

    /**
     * Backwards-compatible helper: returns the first linked project.
     * Use projects() for the full list.
     */
    public function primaryProject()
    {
        return $this->projects()->oldest('contract_project.id')->first();
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function payments()
    {
        return $this->hasMany(ContractPayment::class)->orderByDesc('payment_date');
    }
    public function boqs()
    {
        return $this->hasMany(Boq::class);
    }

    /** Balance remaining = revised - paid */
    public function getBalanceAttribute(): float
    {
        return (float) ($this->revised_value ?? $this->original_value ?? 0) - (float) ($this->amount_paid ?? 0);
    }

    /** Payment progress percentage */
    public function getPaymentProgressAttribute(): int
    {
        $base = (float) ($this->revised_value ?? $this->original_value ?? 0);
        return $base > 0 ? (int) round(((float) ($this->amount_paid ?? 0) / $base) * 100) : 0;
    }

    /** Days elapsed as percentage of contract duration */
    public function getDurationProgressAttribute(): ?int
    {
        if (!$this->start_date || !$this->end_date)
            return null;
        $total = $this->start_date->diffInDays($this->end_date);
        if ($total <= 0)
            return 100;
        $elapsed = $this->start_date->diffInDays(now());
        return min(100, max(0, (int) round(($elapsed / $total) * 100)));
    }
}
