<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
    'product_id',
    'branch_id',
    'batch_number',
    'stock',
    'cost_price',
    'selling_price',
    'expiration_date',
    'rack',
    'shelf'
    ];

    // Scope: solo lotes vigentes (sin fecha de caducidad, o que caducan hoy o después)
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiration_date')
              ->orWhereDate('expiration_date', '>=', now()->toDateString());
        });
    }

    // Relación: Un lote pertenece a un medicamento
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relación: Un lote pertenece a una sucursal
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}