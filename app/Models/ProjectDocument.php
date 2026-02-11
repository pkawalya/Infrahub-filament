<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProjectDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'folder_id',
        'title',
        'document_number',
        'description',
        'file_type',
        'mime_type',
        'current_version_id',
        'version_count',
        'status',
        'is_locked',
        'locked_by',
        'locked_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'version_count' => 'integer',
    ];

    /**
     * File type categories for filtering.
     */
    public const FILE_TYPES = [
        'pdf' => ['application/pdf'],
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
        'document' => ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'spreadsheet' => ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'presentation' => ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'archive' => ['application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'],
        'text' => ['text/plain', 'text/csv'],
    ];

    /**
     * Get the project that owns the document.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the folder containing the document.
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(ProjectFolder::class, 'folder_id');
    }

    /**
     * Get all versions of this document.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'document_id')->orderByDesc('created_at');
    }

    /**
     * Get the current/latest version.
     */
    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }

    /**
     * Get the document history/audit trail.
     */
    public function history(): HasMany
    {
        return $this->hasMany(DocumentHistory::class, 'document_id')->orderByDesc('created_at');
    }

    /**
     * Get the user who created the document.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the document.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who locked the document.
     */
    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Determine file type from mime type.
     */
    public static function determineFileType(string $mimeType): string
    {
        foreach (self::FILE_TYPES as $type => $mimes) {
            if (in_array($mimeType, $mimes)) {
                return $type;
            }
        }
        return 'other';
    }

    /**
     * Get human-readable file size.
     */
    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->currentVersion) {
            return '0 B';
        }

        return $this->currentVersion->file_size_formatted;
    }

    /**
     * Get the file extension icon.
     */
    public function getFileIconAttribute(): string
    {
        return match ($this->file_type) {
            'pdf' => 'heroicon-o-document-text',
            'image' => 'heroicon-o-photo',
            'document' => 'heroicon-o-document',
            'spreadsheet' => 'heroicon-o-table-cells',
            'presentation' => 'heroicon-o-presentation-chart-bar',
            'archive' => 'heroicon-o-archive-box',
            'text' => 'heroicon-o-document-text',
            default => 'heroicon-o-document',
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'active' => 'success',
            'archived' => 'warning',
            'superseded' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Lock the document for editing.
     */
    public function lock(?int $userId = null): bool
    {
        if ($this->is_locked) {
            return false;
        }

        $this->update([
            'is_locked' => true,
            'locked_by' => $userId ?? auth()->id(),
            'locked_at' => now(),
        ]);

        $this->logHistory('locked', 'Document locked for editing');

        return true;
    }

    /**
     * Unlock the document.
     */
    public function unlock(): bool
    {
        if (!$this->is_locked) {
            return false;
        }

        $this->update([
            'is_locked' => false,
            'locked_by' => null,
            'locked_at' => null,
        ]);

        $this->logHistory('unlocked', 'Document unlocked');

        return true;
    }

    /**
     * Check if document can be edited by user.
     */
    public function canEdit(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        if (!$this->is_locked) {
            return true;
        }

        // Check if user is the one who locked it
        if ($this->locked_by === $userId) {
            return true;
        }

        // Super admins can always edit
        $user = User::find($userId);
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
            return true;
        }

        return false;
    }

    /**
     * Log a history entry.
     */
    public function logHistory(string $action, ?string $description = null, ?array $metadata = null, ?int $versionId = null): DocumentHistory
    {
        return $this->history()->create([
            'version_id' => $versionId,
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Check for duplicate files by hash.
     */
    public static function findDuplicate(int $projectId, string $fileHash): ?DocumentVersion
    {
        return DocumentVersion::whereHas('document', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->where('file_hash', $fileHash)->first();
    }

    /**
     * Generate next document number for project.
     */
    public static function generateDocumentNumber(int $projectId): string
    {
        $count = static::where('project_id', $projectId)->count() + 1;
        return 'DOC-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Archive the document.
     */
    public function archive(): void
    {
        $this->update(['status' => 'archived']);
        $this->logHistory('archived', 'Document archived');
    }

    /**
     * Restore from archive.
     */
    public function restore(): void
    {
        $this->update(['status' => 'active']);
        $this->logHistory('restored', 'Document restored from archive');
    }
}
