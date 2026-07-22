<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CdeDocument extends Model
{
    use SoftDeletes, BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'cde_folder_id',
        'document_number',
        'title',
        'description',
        'discipline',
        'type',
        'status',
        'revision',
        'previous_version_id',
        'version_notes',
        'file_path',
        'file_size',
        'file_type',
        'uploaded_by',
    ];

    /**
     * Document workflow statuses (ISO 19650 inspired).
     */
    public static array $statuses = [
        'wip' => 'Work in Progress',
        'draft' => 'Draft',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'revision' => 'Needs Revision',
        'published' => 'Published',
        'archived' => 'Archived',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class);
    }
    public function folder()
    {
        return $this->belongsTo(CdeFolder::class, 'cde_folder_id');
    }
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    public function previousVersion()
    {
        return $this->belongsTo(CdeDocument::class, 'previous_version_id');
    }
    public function nextVersions()
    {
        return $this->hasMany(CdeDocument::class, 'previous_version_id');
    }
    public function shares()
    {
        return $this->hasMany(DocumentShare::class, 'cde_document_id');
    }
    public function activeShares()
    {
        return $this->shares()->where('is_active', true);
    }
    public function rfis()
    {
        return $this->hasMany(Rfi::class, 'cde_document_id');
    }
    public function submittals()
    {
        return $this->hasMany(Submittal::class, 'cde_document_id');
    }
    public function documentSubmissions()
    {
        return $this->hasMany(DocumentSubmission::class, 'cde_document_id');
    }
    public function safetyIncidents()
    {
        return $this->hasMany(SafetyIncident::class, 'cde_document_id');
    }
    public function safetyInspections()
    {
        return $this->hasMany(SafetyInspection::class, 'cde_document_id');
    }
    public function snagItems()
    {
        return $this->hasMany(SnagItem::class, 'cde_document_id');
    }
    public function ncrs()
    {
        return $this->hasMany(Ncr::class, 'cde_document_id');
    }

    public static array $validTransitions = [
        'wip' => ['draft', 'under_review'],
        'draft' => ['under_review'],
        'under_review' => ['approved', 'revision'],
        'revision' => ['under_review'],
        'approved' => ['published', 'archived'],
        'published' => ['archived'],
        'archived' => [],
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        if ($this->status === $newStatus) {
            return true;
        }

        $allowed = static::$validTransitions[$this->status] ?? [];

        return in_array($newStatus, $allowed, true);
    }

    public function transitionTo(string $newStatus): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition document '{$this->document_number}' from '{$this->status}' to '{$newStatus}'. " .
                "Allowed transitions: " . implode(', ', static::$validTransitions[$this->status] ?? [])
            );
        }

        return $this->update(['status' => $newStatus]);
    }

    public function canBeModifiedBy(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return true;
        }

        return (int) $this->uploaded_by === (int) $user->id;
    }

    public function formattedSize(): string
    {
        if (!$this->file_size)
            return '—';
        if ($this->file_size < 1024)
            return $this->file_size . ' B';
        if ($this->file_size < 1048576)
            return round($this->file_size / 1024, 1) . ' KB';
        return round($this->file_size / 1048576, 1) . ' MB';
    }
}
