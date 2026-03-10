<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrawingRevision extends Model
{
    protected $fillable = [
        'drawing_id',
        'revision_code',
        'revision_description',
        'file_path',
        'file_name',
        'file_size',
        'status',
        'revision_date',
        'revised_by',
    ];

    protected $casts = [
        'revision_date' => 'date',
    ];

    public function drawing()
    {
        return $this->belongsTo(Drawing::class);
    }
    public function revisedByUser()
    {
        return $this->belongsTo(User::class, 'revised_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return round($bytes / (1 << (10 * $pow)), 1) . ' ' . $units[$pow];
    }
}
