<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'company_name',
        'tax_id',
        'notes',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
