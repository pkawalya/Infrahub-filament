<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrderRequest extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'request_id',
        'title',
        'description',
        'client_id',
        'asset_id',
        'priority',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = ['approved_at' => 'datetime'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
