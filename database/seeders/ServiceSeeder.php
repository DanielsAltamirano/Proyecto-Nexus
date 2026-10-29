<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear rol Doctor si no existe
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Doctor']);

        $services = [
            ['name' => 'Consulta Médica General', 'price' => 70],
            ['name' => 'Control prenatal', 'price' => 100],
            ['name' => 'Toma de Presión Arterial', 'price' => 30],
            ['name' => 'Toma de glucosa capilar', 'price' => 80],
            ['name' => 'Peso / Talla / IMC', 'price' => 30],
            ['name' => 'Aplicación intramuscular/subcutánea y en "Z"', 'price' => 30],
            ['name' => 'Aplicación intravenosa', 'price' => 70],
            ['name' => 'Retiro de puntos (menor de 5 puntos)', 'price' => 70],
            ['name' => 'Retiro de puntos (mayor a 6 puntos)', 'price' => 100],
            ['name' => 'Retiro de sonda vesical / nasogástrico', 'price' => 200],
            ['name' => 'Curación de sonda vesical/ nasogástrica', 'price' => 150],
            ['name' => 'Curación de heridas (leves)', 'price' => 100],
            ['name' => 'Curación extensa (incluye debridación)', 'price' => 150],
            ['name' => 'Nebulizaciones (por sesión)', 'price' => 70],
            ['name' => 'Revisión de DIU', 'price' => 200],
            ['name' => 'Colocación de DIU (sin material)', 'price' => 400],
            ['name' => 'Retiro de DIU', 'price' => 350],
            ['name' => 'Retiro de implante subdérmico (cuenta como sutura especial)', 'price' => 400],
            ['name' => 'Perforación de arete (par oído)', 'price' => 150],
            ['name' => 'Suturas (menor de 3 puntos)', 'price' => 250],
            ['name' => 'Suturas (de 4 a 6 puntos)', 'price' => 300],
            ['name' => 'Suturas (de 7 a 10 puntos)', 'price' => 350],
            ['name' => 'Suturas (mayor a 11 puntos)', 'price' => 450],
            ['name' => 'Sutura especial dos planos independientes de # puntos', 'price' => 500],
            ['name' => 'Lavado ótico por oído', 'price' => 100],
            ['name' => 'Extracción de uña (por uña)', 'price' => 200],
            ['name' => 'Aplicación de suero', 'price' => 400],
            ['name' => 'Receta justificante', 'price' => 100],
            ['name' => 'Receta de controlado', 'price' => 80],
            ['name' => 'Valoración con hoja de guardería', 'price' => 100],
            ['name' => 'Certificado medico', 'price' => 100],
            ['name' => 'Drenajes obsesos pequeños', 'price' => 300],
        ];

        foreach ($services as $service) {
            \App\Models\Service::firstOrCreate(['name' => $service['name']], ['price' => $service['price']]);
        }
    }
}
