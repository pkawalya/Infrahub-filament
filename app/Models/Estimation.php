<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estimation extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'estimation_id',
        'client_id',
        'asset_id',
        'title',
        'description',
        'total_amount',
        'status',
        'valid_until',
        'notes',
        'created_by',
    ];

    protected $casts = ['total_amount' => 'decimal:2', 'valid_until' => 'date'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
    public function items()
    {
        return $this->hasMany(EstimationItem::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
