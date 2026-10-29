<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'morning_goal',
        'afternoon_goal',
        'is_shared_goal',
    ];

    protected $casts = [
        'morning_goal'   => 'decimal:2',
        'afternoon_goal' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }
}
