<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'user_id',
        'action',
        'before_state',
        'after_state',
    ];

    protected $casts = [
        'before_state' => 'array',
        'after_state' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
