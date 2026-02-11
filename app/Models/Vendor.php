<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
        'tax_id',
        'category',
        'payment_terms',
        'notes',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
