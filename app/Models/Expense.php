<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'title',
        'reference_number',
        'amount',
        'expense_date',
        'category',
        'status',
        'description',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public static array $statuses = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'paid' => 'Paid',
        'rejected' => 'Rejected',
    ];

    public function cdeProject()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
