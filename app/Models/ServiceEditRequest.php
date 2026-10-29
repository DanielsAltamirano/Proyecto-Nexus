<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceEditRequest extends Model
{
    protected $fillable = [
        'service_id',
        'user_id',
        'type',
        'requested_changes',
        'reason',
        'status',
        'resolved_by',
    ];

    protected $casts = [
        'requested_changes' => 'array',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by')->withTrashed();
    }
}
