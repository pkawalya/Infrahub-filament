<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'parent_id',
        'name',
        'description',
        'color',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the project that owns the folder.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the parent folder.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProjectFolder::class, 'parent_id');
    }

    /**
     * Get the child folders.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProjectFolder::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get all descendants (recursive).
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (recursive).
     */
    public function ancestors(): BelongsTo
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * Get the documents in this folder.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class, 'folder_id');
    }

    /**
     * Get the user who created the folder.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the breadcrumb path for this folder.
     */
    public function getBreadcrumbAttribute(): array
    {
        $breadcrumb = [];
        $folder = $this;

        while ($folder) {
            array_unshift($breadcrumb, [
                'id' => $folder->id,
                'name' => $folder->name,
            ]);
            $folder = $folder->parent;
        }

        return $breadcrumb;
    }

    /**
     * Get the full path as a string.
     */
    public function getFullPathAttribute(): string
    {
        return collect($this->breadcrumb)->pluck('name')->implode(' / ');
    }

    /**
     * Get count of all items (documents + subfolders).
     */
    public function getItemCountAttribute(): int
    {
        return $this->documents()->count() + $this->children()->count();
    }

    /**
     * Check if folder has any children (folders or documents).
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists() || $this->documents()->exists();
    }

    /**
     * Get nested folder options for select dropdown.
     */
    public static function getNestedOptions(int $projectId, ?int $excludeId = null, ?int $parentId = null, string $prefix = ''): array
    {
        $options = [];

        $folders = static::where('project_id', $projectId)
            ->where('parent_id', $parentId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        foreach ($folders as $folder) {
            $options[$folder->id] = $prefix . $folder->name;

            // Get children recursively
            $childOptions = static::getNestedOptions($projectId, $excludeId, $folder->id, $prefix . '— ');
            $options = $options + $childOptions;
        }

        return $options;
    }
}
