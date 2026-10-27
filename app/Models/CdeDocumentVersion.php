<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CdeDocumentVersion extends Model
{
    protected $fillable = [
        'cde_document_id',
        'version_number',
        'file_path',
        'file_type',
        'file_size',
        'change_description',
        'uploaded_by',
    ];

    public function document()
    {
        return $this->belongsTo(CdeDocument::class, 'cde_document_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
