<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'empresa',
        'telefono',
        'email',
        'direccion',
    ];

    protected static function booted(): void
    {
        static::deleting(function () {
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->hasRole('Personal de Mostrador') || !$user->hasRole(['admin', 'superadmin'])) {
                    abort(403, 'Acceso denegado: El rol Personal de Mostrador no tiene permisos para realizar eliminaciones lógicas (SoftDeletes).');
                }
            }
        });
    }
}
