<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegisterSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'start_time',
        'end_time',
        'starting_cash',
        'expected_cash',
        'actual_cash',
        'difference',
        'cash_sales',
        'card_sales',
        'transfer_sales',
        'total_sales',
        'status',
        'notes',
        'doctor_paid_at',
        'doctor_payment_amount',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'doctor_paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function dailyExpenses()
    {
        return $this->hasMany(DailyExpense::class);
    }

    public function audits()
    {
        return $this->hasMany(CashRegisterAudit::class, 'cash_register_session_id');
    }
}
