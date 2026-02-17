<?php

namespace App\Models\Concerns;

use App\Models\CdeActivityLog;

/**
 * Auto-log model create/update/delete events to the CDE Activity Log.
 *
 * Usage: Add  `use LogsActivity;` to any model that has a `company_id` field.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            CdeActivityLog::record($model, 'created', class_basename($model) . ' created');
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']); // Not interesting to log

            if (empty($changes)) {
                return;
            }

            $description = class_basename($model) . ' updated: ' . implode(', ', array_keys($changes));

            CdeActivityLog::record(
                $model,
                'updated',
                $description,
                [
                    'before' => collect($model->getOriginal())
                        ->only(array_keys($changes))
                        ->toArray(),
                    'after' => $changes,
                ],
            );
        });

        static::deleted(function ($model) {
            CdeActivityLog::record($model, 'deleted', class_basename($model) . ' deleted');
        });
    }
}
