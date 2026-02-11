<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'version_number',
        'major_version',
        'minor_version',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'file_hash',
        'change_notes',
        'change_type',
        'is_current',
        'uploaded_by',
    ];

    protected $casts = [
        'major_version' => 'integer',
        'minor_version' => 'integer',
        'file_size' => 'integer',
        'is_current' => 'boolean',
    ];

    /**
     * Get the document this version belongs to.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(ProjectDocument::class, 'document_id');
    }

    /**
     * Get the user who uploaded this version.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get formatted file size.
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * Get the download URL.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('documents.download', ['version' => $this->id]);
    }

    /**
     * Get the preview URL (for images and PDFs).
     */
    public function getPreviewUrlAttribute(): ?string
    {
        $previewableMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (in_array($this->mime_type, $previewableMimes)) {
            return route('documents.preview', ['version' => $this->id]);
        }

        return null;
    }

    /**
     * Check if this version can be previewed.
     */
    public function canPreview(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'text/plain',
        ]);
    }

    /**
     * Get the file extension.
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->original_filename, PATHINFO_EXTENSION);
    }

    /**
     * Calculate the next version number.
     */
    public static function getNextVersion(int $documentId, bool $major = false): array
    {
        $latestVersion = static::where('document_id', $documentId)
            ->orderByDesc('major_version')
            ->orderByDesc('minor_version')
            ->first();

        if (!$latestVersion) {
            return [
                'major' => 1,
                'minor' => 0,
                'string' => '1.0',
            ];
        }

        if ($major) {
            return [
                'major' => $latestVersion->major_version + 1,
                'minor' => 0,
                'string' => ($latestVersion->major_version + 1) . '.0',
            ];
        }

        return [
            'major' => $latestVersion->major_version,
            'minor' => $latestVersion->minor_version + 1,
            'string' => $latestVersion->major_version . '.' . ($latestVersion->minor_version + 1),
        ];
    }

    /**
     * Mark this version as current and unset others.
     */
    public function makeCurrent(): void
    {
        // Unset all other versions as current
        static::where('document_id', $this->document_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        // Set this as current
        $this->update(['is_current' => true]);

        // Update parent document
        $this->document->update([
            'current_version_id' => $this->id,
            'version_count' => static::where('document_id', $this->document_id)->count(),
        ]);
    }

    /**
     * Get file contents.
     */
    public function getContents(): ?string
    {
        if (Storage::disk('local')->exists($this->file_path)) {
            return Storage::disk('local')->get($this->file_path);
        }

        return null;
    }

    /**
     * Delete the physical file.
     */
    public function deleteFile(): bool
    {
        if (Storage::disk('local')->exists($this->file_path)) {
            return Storage::disk('local')->delete($this->file_path);
        }

        return false;
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // When a version is deleted, also delete the physical file
        static::deleting(function (DocumentVersion $version) {
            $version->deleteFile();
        });
    }
}
