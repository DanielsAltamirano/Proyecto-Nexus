<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'maps_url',
    ];

    public function goal()
    {
        return $this->hasOne(\App\Models\BranchGoal::class);
    }

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
