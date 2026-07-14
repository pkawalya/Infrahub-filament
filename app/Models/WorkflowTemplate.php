<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class WorkflowTemplate extends Model
{
    use BelongsToCompany;

    protected $table = 'workflow_templates';

    protected $fillable = [
        'company_id',
        'module_type',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($template) {
            if ($template->is_active) {
                static::where('company_id', $template->company_id)
                    ->where('module_type', $template->module_type)
                    ->where('id', '!=', $template->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    public function steps()
    {
        return $this->hasMany(WorkflowStep::class, 'workflow_template_id')->orderBy('step_sequence', 'asc');
    }

    public function instances()
    {
        return $this->hasMany(WorkflowInstance::class, 'workflow_template_id');
    }
}
