<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /**
     * Local scope to hide the superadmin role.
     */
    public function scopeWithoutSuperAdmins($query)
    {
        return $query->where('name', '!=', 'superadmin');
    }
}
