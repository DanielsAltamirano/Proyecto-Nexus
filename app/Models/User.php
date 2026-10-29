<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'branch_id',
        'points',
        'visits',
        'barcode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Local scope to hide superadmin users.
     */
    public function scopeWithoutSuperAdmins($query)
    {
        return $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'superadmin');
        });
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->barcode)) {
                // Generate a 12-digit random barcode (e.g., EAN-12 format)
                $barcode = null;
                do {
                    $barcode = str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
                } while (self::where('barcode', $barcode)->exists());
                
                $user->barcode = $barcode;
            }
        });
    }

    /**
     * Relación: Un usuario (staff) pertenece a una sucursal (opcional para superadmins).
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * Relación: Un usuario puede tener muchas compras asociadas a su tarjeta de lealtad.
     */
    public function purchases()
    {
        return $this->hasMany(Sale::class, 'client_id');
    }

    /**
     * Determina si el usuario tiene acceso global a los inventarios
     * (Admins, Superadmins, o usuarios en sucursales Central/Global/Matriz)
     */
    public function hasGlobalInventoryAccess(): bool
    {
        if ($this->hasRole(['superadmin', 'admin', 'compras'])) {
            return true;
        }

        if ($this->hasRole(['Personal de Mostrador', 'cliente'])) {
            return false;
        }

        if ($this->branch) {
            $branchName = strtolower($this->branch->name);
            if (str_contains($branchName, 'central') || str_contains($branchName, 'global') || str_contains($branchName, 'matriz')) {
                return true;
            }
        }

        return false;
    }
}
