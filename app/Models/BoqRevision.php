<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoqRevision extends Model
{
    protected $fillable = ['boq_id', 'revision_number', 'change_description', 'snapshot', 'created_by'];

    protected $casts = ['snapshot' => 'array'];

    public function boq()
    {
        return $this->belongsTo(Boq::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
