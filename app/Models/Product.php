<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    'sku', 
    'name', 
    'price',
    'type', 
    'description', 
    'active_ingredient',
    'presentation',
    'prescription_required',
    'is_controlled',
    'category_id', 
    'provider_id',
    'is_promo_4x3',
    'is_promo_volumen',
    'promo_volumen_min_qty',
    'promo_volumen_price',
    'promo_4x3_days',
    'promo_4x3_shifts',
    'promo_4x3_branches',
    'promo_volumen_days',
    'promo_volumen_shifts',
    'promo_volumen_branches',
    ];

    protected $casts = [
        'is_promo_4x3' => 'boolean',
        'is_promo_volumen' => 'boolean',
        'promo_volumen_min_qty' => 'integer',
        'promo_volumen_price' => 'decimal:2',
        'promo_4x3_days' => 'array',
        'promo_4x3_shifts' => 'array',
        'promo_4x3_branches' => 'array',
        'promo_volumen_days' => 'array',
        'promo_volumen_shifts' => 'array',
        'promo_volumen_branches' => 'array',
    ];

    // Relación: Un medicamento pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación: Un medicamento pertenece a un proveedor
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    // Relación: Un medicamento tiene muchos lotes
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    // Relación: Un medicamento tiene muchas ventas
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Regla de Control de Acceso:
     * Bloquear las eliminaciones lógicas (SoftDeletes) para cualquier usuario con el rol
     * 'Personal de Mostrador' (Cajero); únicamente 'admin' o 'superadmin' pueden hacerlo.
     */
    protected static function booted(): void
    {
        static::deleting(function (Product $product) {
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->hasRole('Personal de Mostrador') || !$user->hasRole(['admin', 'superadmin'])) {
                    abort(403, 'Acceso denegado: El rol Personal de Mostrador no tiene permisos para realizar eliminaciones lógicas (SoftDeletes). Solo un Administrador puede hacerlo.');
                }
            }
        });
    }
}