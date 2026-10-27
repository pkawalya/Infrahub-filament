<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CdeDocumentComment extends Model
{
    protected $fillable = [
        'cde_document_id',
        'cde_folder_id',
        'user_id',
        'comment',
        'parent_id',
        'is_resolved',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(CdeDocument::class, 'cde_document_id');
    }

    public function folder()
    {
        return $this->belongsTo(CdeFolder::class, 'cde_folder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(CdeDocumentComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(CdeDocumentComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}
