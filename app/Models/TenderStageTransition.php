<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderStageTransition extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'from_stage_id',
        'to_stage_id',
        'required_permission',
        'requires_comment',
        'is_active',
    ];

    protected $casts = [
        'requires_comment' => 'boolean',
        'is_active'        => 'boolean',
    ];

    public function fromStage(): BelongsTo
    {
        return $this->belongsTo(TenderStage::class, 'from_stage_id');
    }

    public function toStage(): BelongsTo
    {
        return $this->belongsTo(TenderStage::class, 'to_stage_id');
    }
}
