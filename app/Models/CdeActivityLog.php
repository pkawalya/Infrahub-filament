<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CdeActivityLog extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'loggable_type',
        'loggable_id',
        'action',
        'description',
        'changes',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public static array $actions = [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'viewed' => 'Viewed',
        'downloaded' => 'Downloaded',
        'uploaded' => 'Uploaded',
        'shared' => 'Shared',
        'status_changed' => 'Status Changed',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'submitted' => 'Submitted',
        'commented' => 'Commented',
    ];

    public function loggable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an activity log entry.
     */
    public static function record(
        Model $model,
        string $action,
        ?string $description = null,
        ?array $changes = null,
    ): static {
        return static::create([
            'company_id' => $model->company_id ?? auth()->user()?->company_id,
            'loggable_type' => $model->getMorphClass(),
            'loggable_id' => $model->getKey(),
            'action' => $action,
            'description' => $description,
            'changes' => $changes,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);
    }
}
