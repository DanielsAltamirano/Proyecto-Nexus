<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Actualizamos o creamos el rol de admin
        Role::firstOrCreate(['name' => 'admin']);

        // Creamos el rol de superadmin
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);

        // Creamos o mantenemos el rol de Mostrador
        Role::firstOrCreate(['name' => 'Personal de Mostrador']);

        // Creamos el rol de cliente
        Role::firstOrCreate(['name' => 'cliente']);

        // Creamos el rol de compras
        Role::firstOrCreate(['name' => 'compras']);

        // Asignamos el rol superadmin al usuario especificado
        $user = \App\Models\User::where('email', 'daniels@opentech.mx')->first();
        if ($user) {
            $user->assignRole($superadmin);
        }
    }
}

// php artisan db:seed --class=RoleSeeder