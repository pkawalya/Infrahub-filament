<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CdeFolder extends Model
{
    protected $fillable = ['company_id', 'cde_project_id', 'name', 'parent_id', 'description', 'sort_order', 'suitability_code'];

    public function project()
    {
        return $this->belongsTo(CdeProject::class);
    }
    public function parent()
    {
        return $this->belongsTo(CdeFolder::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(CdeFolder::class, 'parent_id');
    }
    public function documents()
    {
        return $this->hasMany(CdeDocument::class, 'cde_folder_id');
    }
}
