<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use BelongsToCompany;

    protected $table = 'feedback';
    protected $fillable = ['company_id', 'work_order_id', 'client_id', 'rating', 'comment'];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
