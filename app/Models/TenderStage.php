<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenderStage extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'color',
        'icon',
        'description',
        'sort_order',
        'is_default',
        'is_terminal',
        'is_active',
    ];

    protected $casts = [
        'sort_order'  => 'integer',
        'is_default'  => 'boolean',
        'is_terminal' => 'boolean',
        'is_active'   => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class, 'tender_stage_id');
    }

    public function outgoingTransitions(): HasMany
    {
        return $this->hasMany(TenderStageTransition::class, 'from_stage_id');
    }

    public function incomingTransitions(): HasMany
    {
        return $this->hasMany(TenderStageTransition::class, 'to_stage_id');
    }

    // ─── Helpers ─────────────────────────────────────────────

    /**
     * Get all stages this stage can transition TO.
     */
    public function allowedNextStages()
    {
        return self::whereIn('id',
            $this->outgoingTransitions()->where('is_active', true)->pluck('to_stage_id')
        )->where('is_active', true)->orderBy('sort_order')->get();
    }

    /**
     * Get the default stage for a company.
     */
    public static function getDefault(?int $companyId = null): ?self
    {
        $companyId = $companyId ?? auth()->user()?->company_id;
        return static::where('company_id', $companyId)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    public static array $defaultColors = [
        'gray', 'info', 'primary', 'warning', 'success', 'danger',
    ];
}
