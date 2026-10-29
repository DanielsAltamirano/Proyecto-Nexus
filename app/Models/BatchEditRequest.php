<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchEditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'type',
        'user_id',
        'resolved_by',
        'status',
        'old_stock',
        'old_selling_price',
        'old_expiration_date',
        'requested_stock',
        'requested_selling_price',
        'requested_expiration_date',
        'reason',
        'resolution_notes'
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by')->withTrashed();
    }
}
