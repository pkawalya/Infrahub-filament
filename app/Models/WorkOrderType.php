<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class WorkOrderType extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'name', 'color', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
