<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Subscription extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'billing_cycle',
        'max_users',
        'max_projects',
        'max_storage_gb',
        'included_modules',
        'features',
        'is_active',
        'is_popular',
        'sort_order',
    ];

    protected $casts = [
        'included_modules' => 'array',
        'features' => 'array',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($sub) => $sub->slug = $sub->slug ?? Str::slug($sub->name));
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
