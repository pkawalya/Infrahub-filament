<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ServicePart extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'sku',
        'type',
        'cost',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = ['cost' => 'decimal:2', 'price' => 'decimal:2', 'is_active' => 'boolean'];
}
