<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_branch_id',
        'to_branch_id',
        'created_by',
        'received_by',
        'status',
        'notes',
        'sent_at',
        'received_at',
    ];

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id')->withTrashed();
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id')->withTrashed();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by')->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(TransferItem::class);
    }

    public function logs()
    {
        return $this->hasMany(TransferLog::class);
    }
}
