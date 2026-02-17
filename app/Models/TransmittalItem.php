<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransmittalItem extends Model
{
    protected $fillable = [
        'transmittal_id',
        'cde_document_id',
        'description',
        'copies',
    ];

    public function transmittal()
    {
        return $this->belongsTo(Transmittal::class);
    }

    public function document()
    {
        return $this->belongsTo(CdeDocument::class, 'cde_document_id');
    }
}
