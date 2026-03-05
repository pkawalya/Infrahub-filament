<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $severity
 * @property string $alert_type
 * @property bool $is_acknowledged
 * @property float $budgeted_value
 * @property float $actual_value
 * @property float $variance_percent
 */
class BoqVarianceAlert extends Model
{
    use BelongsToCompany;

    public static array $severities = [
        'low' => 'Low (5-10%)',
        'medium' => 'Medium (10-20%)',
        'high' => 'High (20-50%)',
        'critical' => 'Critical (50%+)',
    ];

    public static array $alertTypes = [
        'overrun' => 'Cost Overrun',
        'underrun' => 'Cost Underrun',
        'quantity_exceeded' => 'Quantity Exceeded',
    ];

    protected $fillable = [
        'company_id',
        'boq_id',
        'boq_item_id',
        'cde_project_id',
        'severity',
        'alert_type',
        'title',
        'message',
        'budgeted_value',
        'actual_value',
        'variance_percent',
        'is_acknowledged',
        'acknowledged_by',
        'acknowledged_at',
    ];

    protected $casts = [
        'budgeted_value' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'variance_percent' => 'decimal:2',
        'is_acknowledged' => 'boolean',
        'acknowledged_at' => 'datetime',
    ];

    // ── Relationships ──

    public function boq()
    {
        return $this->belongsTo(Boq::class);
    }

    public function boqItem()
    {
        return $this->belongsTo(BoqItem::class);
    }

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    // ── Helpers ──

    public function acknowledge(int $userId): void
    {
        $this->update([
            'is_acknowledged' => true,
            'acknowledged_by' => $userId,
            'acknowledged_at' => now(),
        ]);
    }

    /**
     * Determine the severity level from a variance percentage.
     */
    public static function severityFromPercent(float $percent): string
    {
        $abs = abs($percent);
        if ($abs >= 50)
            return 'critical';
        if ($abs >= 20)
            return 'high';
        if ($abs >= 10)
            return 'medium';
        return 'low';
    }

    /**
     * Severity badge color for Filament.
     */
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            default => 'gray',
        };
    }

    // ── Scopes ──

    public function scopeUnacknowledged($q)
    {
        return $q->where('is_acknowledged', false);
    }

    public function scopeForProject($q, int $projectId)
    {
        return $q->where('cde_project_id', $projectId);
    }

    public function scopeCritical($q)
    {
        return $q->whereIn('severity', ['high', 'critical']);
    }
}
