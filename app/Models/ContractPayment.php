<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ContractPayment extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'contract_id',
        'company_id',
        'reference',
        'type',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public static array $types = [
        'payment' => 'Payment',
        'advance' => 'Advance',
        'retention_release' => 'Retention Release',
        'deduction' => 'Deduction',
    ];

    public static array $methods = [
        'bank_transfer' => 'Bank Transfer',
        'cheque' => 'Cheque',
        'cash' => 'Cash',
        'mobile_money' => 'Mobile Money',
        'other' => 'Other',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
