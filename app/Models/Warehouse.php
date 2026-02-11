<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'address',
        'city',
        'state',
        'country',
        'manager_id',
        'is_active',
        'is_default',
    ];

    protected $casts = ['is_active' => 'boolean', 'is_default' => 'boolean'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class);
    }
}
