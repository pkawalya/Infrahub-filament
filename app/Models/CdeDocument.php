<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CdeDocument extends Model
{
    use SoftDeletes, BelongsToCompany;

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
        'file_path',
        'file_size',
        'file_type',
        'uploaded_by',
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
}
