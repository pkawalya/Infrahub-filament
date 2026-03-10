<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentCertificate extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'contract_id',
        'certificate_number',
        'type',
        'status',
        'period_from',
        'period_to',
        'gross_value_to_date',
        'previous_certified',
        'this_certificate_gross',
        'variations_amount',
        'materials_on_site',
        'retention_deduction',
        'retention_release',
        'advance_recovery',
        'other_deductions',
        'deduction_description',
        'net_payable',
        'vat_amount',
        'total_payable',
        'prepared_by',
        'checked_by',
        'certified_by',
        'submitted_date',
        'certified_date',
        'paid_date',
        'notes',
        'rejection_reason',
        'attachments',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'submitted_date' => 'date',
        'certified_date' => 'date',
        'paid_date' => 'date',
        'gross_value_to_date' => 'decimal:2',
        'previous_certified' => 'decimal:2',
        'this_certificate_gross' => 'decimal:2',
        'variations_amount' => 'decimal:2',
        'materials_on_site' => 'decimal:2',
        'retention_deduction' => 'decimal:2',
        'retention_release' => 'decimal:2',
        'advance_recovery' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_payable' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'attachments' => 'array',
    ];

    public static array $types = [
        'interim' => 'Interim Payment Certificate (IPC)',
        'final' => 'Final Account',
        'retention_release' => 'Retention Release',
        'advance' => 'Advance Payment',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'certified' => 'Certified',
        'paid' => 'Paid',
        'rejected' => 'Rejected',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    public function preparedByUser()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }
    public function checkedByUser()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
    public function certifiedByUser()
    {
        return $this->belongsTo(User::class, 'certified_by');
    }

    /**
     * Auto-compute net payable from line items.
     */
    public function computeNetPayable(): void
    {
        $this->this_certificate_gross = $this->gross_value_to_date - $this->previous_certified;
        $this->net_payable = $this->this_certificate_gross
            + $this->variations_amount
            + $this->materials_on_site
            - $this->retention_deduction
            + $this->retention_release
            - $this->advance_recovery
            - $this->other_deductions;
        $this->total_payable = $this->net_payable + $this->vat_amount;
    }
}
