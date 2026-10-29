<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalExpense extends Model
{
    use HasFactory;

    protected $table = 'operational_expenses';

    protected $fillable = [
        'concepto',
        'monto',
        'fecha',
        'user_id',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
