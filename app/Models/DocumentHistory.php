<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentHistory extends Model
{
    use HasFactory;

    protected $table = 'document_history';

    protected $fillable = [
        'document_id',
        'version_id',
        'user_id',
        'action',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Available actions for history tracking.
     */
    public const ACTIONS = [
        'created' => 'Document Created',
        'uploaded' => 'New Version Uploaded',
        'viewed' => 'Document Viewed',
        'downloaded' => 'Document Downloaded',
        'renamed' => 'Document Renamed',
        'moved' => 'Document Moved',
        'locked' => 'Document Locked',
        'unlocked' => 'Document Unlocked',
        'archived' => 'Document Archived',
        'restored' => 'Document Restored',
        'deleted' => 'Document Deleted',
        'status_changed' => 'Status Changed',
        'description_updated' => 'Description Updated',
    ];

    /**
     * Get the document this history entry belongs to.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(ProjectDocument::class, 'document_id');
    }

    /**
     * Get the version associated with this history entry.
     */
    public function version(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'version_id');
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the action label.
     */
    public function getActionLabelAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    /**
     * Get the icon for the action.
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created' => 'heroicon-o-plus-circle',
            'uploaded' => 'heroicon-o-arrow-up-tray',
            'viewed' => 'heroicon-o-eye',
            'downloaded' => 'heroicon-o-arrow-down-tray',
            'renamed' => 'heroicon-o-pencil',
            'moved' => 'heroicon-o-arrows-right-left',
            'locked' => 'heroicon-o-lock-closed',
            'unlocked' => 'heroicon-o-lock-open',
            'archived' => 'heroicon-o-archive-box',
            'restored' => 'heroicon-o-arrow-uturn-left',
            'deleted' => 'heroicon-o-trash',
            'status_changed' => 'heroicon-o-arrow-path',
            'description_updated' => 'heroicon-o-document-text',
            default => 'heroicon-o-clock',
        };
    }

    /**
     * Get the color for the action.
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created', 'uploaded', 'restored' => 'success',
            'viewed', 'downloaded' => 'info',
            'renamed', 'moved', 'status_changed', 'description_updated' => 'warning',
            'locked' => 'primary',
            'unlocked' => 'secondary',
            'archived', 'deleted' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get formatted time ago.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
