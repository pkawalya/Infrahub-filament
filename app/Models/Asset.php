<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'asset_id',
        'name',
        'category',
        'brand',
        'model_number',
        'serial_number',
        'location',
        'client_id',
        'purchase_date',
        'purchase_cost',
        'warranty_expires_at',
        'status',
        'condition',
        'image',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expires_at' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public static array $statuses = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'maintenance' => 'Under Maintenance',
        'retired' => 'Retired',
    ];
}
