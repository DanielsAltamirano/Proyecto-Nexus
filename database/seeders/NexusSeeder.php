<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Provider;
use App\Models\Product;
use App\Models\Batch;
use Spatie\Permission\Models\Role;

class NexusSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles definidos en el proyecto
        $roles = ['superadmin', 'admin', 'Personal de Mostrador', 'cliente', 'compras'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // 2. Sucursal matriz
        $branch = Branch::firstOrCreate(
            ['name' => 'Sucursal Central'],
            ['address' => 'Av. Tecnológico #345', 'phone' => '4434376782', 'is_warehouse' => false]
        );

        // 3. Usuarios base (Administrador y Personal de Mostrador)
        $admin = User::firstOrCreate(
            ['email' => 'admin@nexuserp.com'],
            [
                'name'      => 'Admin Nexus',
                'password'  => Hash::make('123'),
                'phone'     => '4431234567',
                'branch_id' => $branch->id,
            ]
        );
        $admin->syncRoles(['superadmin', 'admin']);

        $cajero = User::firstOrCreate(
            ['email' => 'mostrador@nexuserp.com'],
            [
                'name'      => 'Cajero Mostrador Nexus',
                'password'  => Hash::make('password123'),
                'phone'     => '4437654321',
                'branch_id' => $branch->id,
            ]
        );
        $cajero->syncRoles(['Personal de Mostrador']);

        // Generar 20 usuarios sintéticos adicionales con contraseñas hasheadas
        $syntheticUsers = User::factory(20)->create([
            'branch_id' => $branch->id,
            'password'  => Hash::make('Nexus2026!'),
        ]);

        foreach ($syntheticUsers as $index => $u) {
            $roleName = ($index % 4 === 0) ? 'admin' : 'Personal de Mostrador';
            $u->assignRole($roleName);
        }

        // 4. Categorías farmacéuticas
        $categories = [
            ['name' => 'Antibióticos y Antivirales', 'description' => 'Medicamentos de prescripción para infecciones'],
            ['name' => 'Psicotrópicos y Controlados', 'description' => 'Medicamentos controlados sujetos a validación de cédula médica'],
            ['name' => 'Analgésicos y Antiinflamatorios', 'description' => 'Medicamentos de libre venta para dolor y fiebre'],
            ['name' => 'Cardiología y Diabetes', 'description' => 'Tratamientos crónicos y metabólicos'],
        ];

        $categoryIds = [];
        foreach ($categories as $cat) {
            $created = Category::firstOrCreate(['name' => $cat['name']], ['description' => $cat['description']]);
            $categoryIds[] = $created->id;
        }

        // 5. Proveedor farmacéutico inicial
        $provider = Provider::firstOrCreate(
            ['name' => 'Distribuidora Farmacéutica Nexus SA'],
            [
                'razon_social' => 'Distribuidora Farmacéutica Nexus SA de CV',
                'rfc'          => 'DFN200101ABC',
                'phone'        => '4439876543',
                'address'      => 'Parque Industrial Morelia',
            ]
        );

        // 6. Generar 100 Medicamentos sintéticos (70 Normales y 30 Controlados) e Inventario inicial
        $normalProducts = Product::factory(70)->normal()->create([
            'category_id' => $categoryIds[2],
            'provider_id' => $provider->id,
        ]);

        $controlledProducts = Product::factory(30)->controlled()->create([
            'category_id' => $categoryIds[1],
            'provider_id' => $provider->id,
        ]);

        $allProducts = $normalProducts->concat($controlledProducts);

        foreach ($allProducts as $index => $product) {
            // Los últimos 5 medicamentos se crean con stock = 0 para pruebas de bloqueo de venta
            $stock = ($index >= 95) ? 0 : fake()->numberBetween(15, 200);
            $cost = round(((float) $product->price) * 0.70, 2);

            Batch::factory()->create([
                'product_id'    => $product->id,
                'branch_id'     => $branch->id,
                'stock'         => $stock,
                'cost_price'    => $cost,
                'selling_price' => (float) $product->price,
            ]);
        }
    }
}
