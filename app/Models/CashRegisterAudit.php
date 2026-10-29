<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegisterAudit extends Model
{
    protected $fillable = [
        'user_id',
        'cash_register_session_id',
        'branch_id',
        'event',
        'actual_cash',
        'difference',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function session()
    {
        return $this->belongsTo(CashRegisterSession::class, 'cash_register_session_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }
}
