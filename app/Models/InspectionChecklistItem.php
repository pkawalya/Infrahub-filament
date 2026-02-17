<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionChecklistItem extends Model
{
    protected $fillable = [
        'inspection_template_id',
        'item',
        'category',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(InspectionTemplate::class, 'inspection_template_id');
    }
}
