<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Ejecuta NexusSeeder para poblar roles, usuarios (con contraseñas hasheadas),
     * medicamentos (normales y controlados) e inventario inicial (>= 100 registros).
     */
    public function run(): void
    {
        $this->call([
            NexusSeeder::class,
        ]);
    }
}
