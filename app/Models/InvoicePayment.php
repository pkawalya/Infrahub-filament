<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use LogsActivity;
    protected $fillable = [
        'invoice_id',
        'cde_project_id',
        'amount',
        'payment_method',
        'reference',
        'payment_date',
        'notes',
        'recorded_by',
    ];

    protected $casts = ['payment_date' => 'date', 'amount' => 'decimal:2'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function cdeProject()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
}
