<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscription extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'base_platform_price',
        'per_project_price',
        'module_prices',
        'included_projects',
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
        'module_prices' => 'array',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'base_platform_price' => 'decimal:2',
        'per_project_price' => 'decimal:2',
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
