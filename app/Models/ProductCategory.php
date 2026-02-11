<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'name', 'slug', 'description', 'parent_id', 'sort_order'];

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
