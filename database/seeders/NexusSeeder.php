<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        // 1. Roles del sistema
        $roles = ['superadmin', 'admin', 'Personal de Mostrador', 'cliente', 'compras'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // 2. Sucursales de Nexus ERP
        $centralBranch = Branch::updateOrCreate(
            ['name' => 'Sucursal Central'],
            [
                'address'      => 'Av. Tecnológico #345, Col. Centro',
                'phone'        => '4434376782',
                'is_warehouse' => false,
            ]
        );

        $norteBranch = Branch::updateOrCreate(
            ['name' => 'Sucursal Norte'],
            [
                'address'      => 'Av. Camelinas #1200, Col. Bosques',
                'phone'        => '4432109876',
                'is_warehouse' => false,
            ]
        );

        $surBranch = Branch::updateOrCreate(
            ['name' => 'Sucursal Sur'],
            [
                'address'      => 'Calzada Ventura Puente #450',
                'phone'        => '4433123456',
                'is_warehouse' => false,
            ]
        );

        $bodega = Branch::updateOrCreate(
            ['name' => 'Bodega'],
            [
                'address'      => 'Parque Industrial Nave 4, Módulo B',
                'phone'        => '4435551234',
                'is_warehouse' => true,
            ]
        );

        // 3. Usuarios Principales y Superadmin
        // Usuario Superadmin Daniels Altamirano
        $daniels = User::where('email', 'daniels@opentech.mx')->first();
        if ($daniels) {
            $daniels->branch_id = $centralBranch->id;
            $daniels->save();
            $daniels->syncRoles(['superadmin', 'admin']);
        } else {
            $daniels = User::create([
                'name'      => 'Daniels Altamirano',
                'email'     => 'daniels@opentech.mx',
                'password'  => Hash::make('admin123'),
                'phone'     => '4439998877',
                'branch_id' => $centralBranch->id,
            ]);
            $daniels->syncRoles(['superadmin', 'admin']);
        }

        // Admin Nexus
        $admin = User::firstOrCreate(
            ['email' => 'admin@nexuserp.com'],
            [
                'name'      => 'Admin Nexus',
                'password'  => Hash::make('123'),
                'phone'     => '4431234567',
                'branch_id' => $centralBranch->id,
            ]
        );
        $admin->branch_id = $centralBranch->id;
        $admin->save();
        $admin->syncRoles(['superadmin', 'admin']);

        // Cajero Mostrador
        $cajero = User::firstOrCreate(
            ['email' => 'mostrador@nexuserp.com'],
            [
                'name'      => 'Cajero Mostrador Nexus',
                'password'  => Hash::make('password123'),
                'phone'     => '4437654321',
                'branch_id' => $centralBranch->id,
            ]
        );
        $cajero->branch_id = $centralBranch->id;
        $cajero->save();
        $cajero->syncRoles(['Personal de Mostrador']);

        // 4. Categorías farmacéuticas completas
        $categoriesData = [
            'Antibióticos y Antivirales'      => 'Medicamentos de prescripción para combatir infecciones bacterianas y virales',
            'Psicotrópicos y Controlados'    => 'Medicamentos controlados (Fracción II y III) sujetos a validación de cédula médica obligatoria',
            'Analgésicos y Antiinflamatorios' => 'Alivio del dolor, inflamación articular y fiebre',
            'Cardiología y Presión Arterial' => 'Tratamiento de hipertensión, insuficiencia cardíaca y dislipidemias',
            'Diabetes y Metabolismo'         => 'Hipoglucemiantes, insulinas y control metabólico integral',
            'Gastroenterología y Antiácidos' => 'Inhibidores de bomba de protones, procinéticos y digestivos',
            'Respiratorios y Antihistamínicos'=> 'Tratamiento de asma, alergias, tos y afecciones bronquiales',
            'Vitaminas y Suplementos'        => 'Complejos multivitamínicos, minerales y coadyuvantes nutricionales',
            'Material de Curación y Botiquín'=> 'Insumos médicos, gasas, antisépticos y material de primeros auxilios',
            'Dermatología y Cuidado Personal'=> 'Tratamientos tópicos, cicatrizantes y salud dérmica',
        ];

        $categoryModels = [];
        foreach ($categoriesData as $catName => $catDesc) {
            $cat = Category::firstOrCreate(['name' => $catName], ['description' => $catDesc]);
            $categoryModels[$catName] = $cat;
        }

        // 5. Proveedores farmacéuticos
        $providersData = [
            ['name' => 'Distribuidora Farmacéutica Nexus SA de CV', 'razon_social' => 'Distribuidora Farmacéutica Nexus SA de CV', 'rfc' => 'DFN200101ABC', 'phone' => '4439876543', 'address' => 'Parque Industrial Morelia'],
            ['name' => 'Nadro Farmacéutica SA de CV',              'razon_social' => 'Nadro Nacional Droguerías SA de CV',       'rfc' => 'NAD850403K92', 'phone' => '5552345678', 'address' => 'Calzada de Tlalpan #2000, CDMX'],
            ['name' => 'Marzam Distribuciones Médicas',             'razon_social' => 'Casa Marzam SA de CV',                     'rfc' => 'MAR740915TR3', 'phone' => '5558901234', 'address' => 'Av. Insurgentes Sur #1450, CDMX'],
            ['name' => 'Laboratorios Pisa Farmacéuticos',          'razon_social' => 'Laboratorios Pisa SA de CV',               'rfc' => 'PIS650123AB1', 'phone' => '3336789012', 'address' => 'Av. España #1840, Guadalajara'],
            ['name' => 'Medifarma del Bajío SA de CV',              'razon_social' => 'Medifarma Distribuciones del Bajío SA',    'rfc' => 'MBJ120304XYZ', 'phone' => '4771239876', 'address' => 'Blvd. Aeropuerto #500, León'],
        ];

        $providerModels = [];
        foreach ($providersData as $pData) {
            $p = Provider::firstOrCreate(['name' => $pData['name']], $pData);
            $providerModels[] = $p;
        }

        // 6. Limpieza preventiva de lotes huérfanos sin producto activo
        // Si hay productos en la papelera (soft-deleted), los restauramos para que el catálogo no pierda historial
        Product::onlyTrashed()->restore();

        // 7. Catálogo realista de medicamentos reconocidos
        $catalog = [
            // Analgésicos
            [
                'sku' => 'NX-750100800101', 'name' => 'Paracetamol 500mg Tabletas', 'category' => 'Analgésicos y Antiinflamatorios',
                'active_ingredient' => 'Paracetamol 500mg', 'presentation' => 'Caja con 20 tabletas', 'type' => 'GEN',
                'price' => 38.50, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante A', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800102', 'name' => 'Tempra 500mg (Paracetamol)', 'category' => 'Analgésicos y Antiinflamatorios',
                'active_ingredient' => 'Paracetamol 500mg', 'presentation' => 'Caja con 20 tabletas masticables', 'type' => 'PAT',
                'price' => 115.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante A', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800103', 'name' => 'Ibuprofeno 400mg Cápsulas', 'category' => 'Analgésicos y Antiinflamatorios',
                'active_ingredient' => 'Ibuprofeno 400mg', 'presentation' => 'Caja con 10 cápsulas de gel blando', 'type' => 'GEN',
                'price' => 54.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante A', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800104', 'name' => 'Motrin 600mg (Ibuprofeno)', 'category' => 'Analgésicos y Antiinflamatorios',
                'active_ingredient' => 'Ibuprofeno 600mg', 'presentation' => 'Caja con 45 tabletas', 'type' => 'PAT',
                'price' => 245.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante A', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800105', 'name' => 'Ketorolaco 10mg Tabletas', 'category' => 'Analgésicos y Antiinflamatorios',
                'active_ingredient' => 'Ketorolaco Trometamina 10mg', 'presentation' => 'Caja con 10 tabletas', 'type' => 'GEN',
                'price' => 48.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante A', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800106', 'name' => 'Ketorolaco Sublingual 30mg', 'category' => 'Analgésicos y Antiinflamatorios',
                'active_ingredient' => 'Ketorolaco Trometamina 30mg', 'presentation' => 'Caja con 4 tabletas sublinguales', 'type' => 'GEN',
                'price' => 89.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante A', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800107', 'name' => 'Naproxeno Sódico 550mg', 'category' => 'Analgésicos y Antiinflamatorios',
                'active_ingredient' => 'Naproxeno 550mg', 'presentation' => 'Caja con 12 tabletas', 'type' => 'GEN',
                'price' => 72.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante A', 'shelf' => 'Nivel 4'
            ],
            [
                'sku' => 'NX-750100800108', 'name' => 'Flanax 550mg (Naproxeno)', 'category' => 'Analgésicos y Antiinflamatorios',
                'active_ingredient' => 'Naproxeno Sódico 550mg', 'presentation' => 'Caja con 20 tabletas', 'type' => 'PAT',
                'price' => 210.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante A', 'shelf' => 'Nivel 4'
            ],
            [
                'sku' => 'NX-750100800109', 'name' => 'Meloxicam 15mg Tabletas', 'category' => 'Analgésicos y Antiinflamatorios',
                'active_ingredient' => 'Meloxicam 15mg', 'presentation' => 'Caja con 10 tabletas', 'type' => 'GEN',
                'price' => 65.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante A', 'shelf' => 'Nivel 5'
            ],
            [
                'sku' => 'NX-750100800110', 'name' => 'Celecoxib 200mg Cápsulas', 'category' => 'Analgésicos y Antiinflamatorios',
                'active_ingredient' => 'Celecoxib 200mg', 'presentation' => 'Caja con 10 cápsulas', 'type' => 'GEN',
                'price' => 140.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante A', 'shelf' => 'Nivel 5'
            ],

            // Antibióticos y Antivirales
            [
                'sku' => 'NX-750100800201', 'name' => 'Amoxicilina 500mg Cápsulas', 'category' => 'Antibióticos y Antivirales',
                'active_ingredient' => 'Amoxicilina Trihidrato 500mg', 'presentation' => 'Caja con 12 cápsulas', 'type' => 'GEN',
                'price' => 78.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante B', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800202', 'name' => 'Amoxicilina / Ác. Clavulánico 875/125mg', 'category' => 'Antibióticos y Antivirales',
                'active_ingredient' => 'Amoxicilina / Clavulanato', 'presentation' => 'Caja con 14 tabletas recubiertas', 'type' => 'GEN',
                'price' => 195.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante B', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800203', 'name' => 'Augmentin 875/125mg', 'category' => 'Antibióticos y Antivirales',
                'active_ingredient' => 'Amoxicilina / Clavulanato Potásico', 'presentation' => 'Caja con 14 tabletas', 'type' => 'PAT',
                'price' => 480.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante B', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800204', 'name' => 'Azitromicina 500mg Tabletas', 'category' => 'Antibióticos y Antivirales',
                'active_ingredient' => 'Azitromicina Dihidrato 500mg', 'presentation' => 'Caja con 3 tabletas', 'type' => 'GEN',
                'price' => 120.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante B', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800205', 'name' => 'Ciprofloxacino 500mg Tabletas', 'category' => 'Antibióticos y Antivirales',
                'active_ingredient' => 'Ciprofloxacino Clorhidrato 500mg', 'presentation' => 'Caja con 14 tabletas', 'type' => 'GEN',
                'price' => 95.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante B', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800206', 'name' => 'Cefalexina 500mg Cápsulas', 'category' => 'Antibióticos y Antivirales',
                'active_ingredient' => 'Cefalexina Monohidrato 500mg', 'presentation' => 'Caja con 20 cápsulas', 'type' => 'GEN',
                'price' => 110.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante B', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800207', 'name' => 'Ceftriaxona 1g Solución Inyectable IM/IV', 'category' => 'Antibióticos y Antivirales',
                'active_ingredient' => 'Ceftriaxona Sódica 1g', 'presentation' => 'Frasco ámpula con diluyente 3.5ml', 'type' => 'GEN',
                'price' => 135.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante B', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800208', 'name' => 'Aciclovir 400mg Tabletas', 'category' => 'Antibióticos y Antivirales',
                'active_ingredient' => 'Aciclovir 400mg', 'presentation' => 'Caja con 35 tabletas', 'type' => 'GEN',
                'price' => 145.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante B', 'shelf' => 'Nivel 4'
            ],
            [
                'sku' => 'NX-750100800209', 'name' => 'Oseltamivir 75mg Cápsulas', 'category' => 'Antibióticos y Antivirales',
                'active_ingredient' => 'Fosfato de Oseltamivir 75mg', 'presentation' => 'Caja con 10 cápsulas', 'type' => 'GEN',
                'price' => 380.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante B', 'shelf' => 'Nivel 4'
            ],
            [
                'sku' => 'NX-750100800210', 'name' => 'Levofloxacino 500mg Tabletas', 'category' => 'Antibióticos y Antivirales',
                'active_ingredient' => 'Levofloxacino Hemihidrato 500mg', 'presentation' => 'Caja con 7 tabletas', 'type' => 'GEN',
                'price' => 160.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante B', 'shelf' => 'Nivel 5'
            ],

            // Psicotrópicos y Controlados (Fracción II y III - CÉDULA MÉDICA OBLIGATORIA)
            [
                'sku' => 'NX-750100800301', 'name' => 'Clonazepam 2mg Tabletas', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Clonazepam 2mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'GEN',
                'price' => 95.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800302', 'name' => 'Rivotril 2mg (Clonazepam)', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Clonazepam 2mg', 'presentation' => 'Caja con 30 tabletas ranuradas', 'type' => 'PAT',
                'price' => 320.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800303', 'name' => 'Alprazolam 0.5mg Tabletas', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Alprazolam 0.5mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'GEN',
                'price' => 88.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800304', 'name' => 'Tafil 0.5mg (Alprazolam)', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Alprazolam 0.5mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'PAT',
                'price' => 380.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800305', 'name' => 'Diazepam 10mg Tabletas', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Diazepam 10mg', 'presentation' => 'Caja con 20 tabletas', 'type' => 'GEN',
                'price' => 75.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800306', 'name' => 'Valium 10mg (Diazepam)', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Diazepam 10mg', 'presentation' => 'Caja con 20 tabletas', 'type' => 'PAT',
                'price' => 290.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800307', 'name' => 'Metilfenidato 10mg Tabletas', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Clorhidrato de Metilfenidato 10mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'GEN',
                'price' => 210.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800308', 'name' => 'Ritalin 10mg (Metilfenidato)', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Clorhidrato de Metilfenidato 10mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'PAT',
                'price' => 640.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800309', 'name' => 'Tramadol 50mg Cápsulas', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Clorhidrato de Tramadol 50mg', 'presentation' => 'Caja con 20 cápsulas', 'type' => 'GEN',
                'price' => 110.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800310', 'name' => 'Tramadol / Paracetamol 37.5/325mg', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Tramadol / Paracetamol', 'presentation' => 'Caja con 20 tabletas', 'type' => 'GEN',
                'price' => 165.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800311', 'name' => 'Zolpidem 10mg Tabletas', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Tartrato de Zolpidem 10mg', 'presentation' => 'Caja con 10 tabletas', 'type' => 'GEN',
                'price' => 135.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800312', 'name' => 'Pregabalina 75mg Cápsulas', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Pregabalina 75mg', 'presentation' => 'Caja con 14 cápsulas', 'type' => 'GEN',
                'price' => 180.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 4'
            ],
            [
                'sku' => 'NX-750100800313', 'name' => 'Lyrica 75mg (Pregabalina)', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Pregabalina 75mg', 'presentation' => 'Caja con 28 cápsulas', 'type' => 'PAT',
                'price' => 790.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 4'
            ],
            [
                'sku' => 'NX-750100800314', 'name' => 'Lorazepam 2mg Tabletas', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Lorazepam 2mg', 'presentation' => 'Caja con 40 tabletas', 'type' => 'GEN',
                'price' => 140.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 4'
            ],
            [
                'sku' => 'NX-750100800315', 'name' => 'Buprenorfina 0.2mg Sublingual', 'category' => 'Psicotrópicos y Controlados',
                'active_ingredient' => 'Clorhidrato de Buprenorfina 0.2mg', 'presentation' => 'Caja con 10 tabletas sublinguales', 'type' => 'GEN',
                'price' => 260.00, 'is_controlled' => true, 'prescription_required' => true, 'rack' => 'Vitrina Controlados', 'shelf' => 'Nivel 5'
            ],

            // Cardiología y Presión Arterial
            [
                'sku' => 'NX-750100800401', 'name' => 'Losartán 50mg Tabletas', 'category' => 'Cardiología y Presión Arterial',
                'active_ingredient' => 'Losartán Potásico 50mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'GEN',
                'price' => 65.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante C', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800402', 'name' => 'Cozaar 50mg (Losartán)', 'category' => 'Cardiología y Presión Arterial',
                'active_ingredient' => 'Losartán Potásico 50mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'PAT',
                'price' => 450.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante C', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800403', 'name' => 'Telmisartán 80mg Tabletas', 'category' => 'Cardiología y Presión Arterial',
                'active_ingredient' => 'Telmisartán 80mg', 'presentation' => 'Caja con 28 tabletas', 'type' => 'GEN',
                'price' => 130.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante C', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800404', 'name' => 'Micardis 80mg (Telmisartán)', 'category' => 'Cardiología y Presión Arterial',
                'active_ingredient' => 'Telmisartán 80mg', 'presentation' => 'Caja con 28 tabletas', 'type' => 'PAT',
                'price' => 620.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante C', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800405', 'name' => 'Enalapril 10mg Tabletas', 'category' => 'Cardiología y Presión Arterial',
                'active_ingredient' => 'Maleato de Enalapril 10mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'GEN',
                'price' => 45.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante C', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800406', 'name' => 'Amlodipino 5mg Tabletas', 'category' => 'Cardiología y Presión Arterial',
                'active_ingredient' => 'Besilato de Amlodipino 5mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'GEN',
                'price' => 58.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante C', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800407', 'name' => 'Atorvastatina 20mg Tabletas', 'category' => 'Cardiología y Presión Arterial',
                'active_ingredient' => 'Atorvastatina Cálcica 20mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'GEN',
                'price' => 95.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante C', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800408', 'name' => 'Lipitor 20mg (Atorvastatina)', 'category' => 'Cardiología y Presión Arterial',
                'active_ingredient' => 'Atorvastatina 20mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'PAT',
                'price' => 840.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante C', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800409', 'name' => 'Rosuvastatina 10mg Tabletas', 'category' => 'Cardiología y Presión Arterial',
                'active_ingredient' => 'Rosuvastatina Cálcica 10mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'GEN',
                'price' => 165.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante C', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800410', 'name' => 'Aspirina Protect 100mg', 'category' => 'Cardiología y Presión Arterial',
                'active_ingredient' => 'Ácido Acetilsalicílico 100mg', 'presentation' => 'Caja con 84 tabletas con capa entérica', 'type' => 'PAT',
                'price' => 185.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante C', 'shelf' => 'Nivel 4'
            ],

            // Diabetes y Metabolismo
            [
                'sku' => 'NX-750100800501', 'name' => 'Metformina 850mg Tabletas', 'category' => 'Diabetes y Metabolismo',
                'active_ingredient' => 'Clorhidrato de Metformina 850mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'GEN',
                'price' => 52.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante D', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800502', 'name' => 'Glucophage 850mg (Metformina)', 'category' => 'Diabetes y Metabolismo',
                'active_ingredient' => 'Metformina 850mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'PAT',
                'price' => 220.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante D', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800503', 'name' => 'Glibenclamida 5mg Tabletas', 'category' => 'Diabetes y Metabolismo',
                'active_ingredient' => 'Glibenclamida 5mg', 'presentation' => 'Caja con 50 tabletas', 'type' => 'GEN',
                'price' => 40.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante D', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800504', 'name' => 'Insulina Glargina 100 UI/ml Solución', 'category' => 'Diabetes y Metabolismo',
                'active_ingredient' => 'Insulina Glargina', 'presentation' => 'Pluma precargada descartable SoloStar 3ml', 'type' => 'PAT',
                'price' => 580.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Refrigerador', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800505', 'name' => 'Dapagliflozina 10mg Tabletas', 'category' => 'Diabetes y Metabolismo',
                'active_ingredient' => 'Dapagliflozina 10mg', 'presentation' => 'Caja con 28 tabletas', 'type' => 'GEN',
                'price' => 450.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante D', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800506', 'name' => 'Forxiga 10mg (Dapagliflozina)', 'category' => 'Diabetes y Metabolismo',
                'active_ingredient' => 'Dapagliflozina 10mg', 'presentation' => 'Caja con 28 tabletas', 'type' => 'PAT',
                'price' => 1120.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante D', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800507', 'name' => 'Sitagliptina 100mg Tabletas', 'category' => 'Diabetes y Metabolismo',
                'active_ingredient' => 'Fosfato de Sitagliptina 100mg', 'presentation' => 'Caja con 28 tabletas', 'type' => 'GEN',
                'price' => 380.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante D', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800508', 'name' => 'Januvia 100mg (Sitagliptina)', 'category' => 'Diabetes y Metabolismo',
                'active_ingredient' => 'Sitagliptina 100mg', 'presentation' => 'Caja con 28 tabletas', 'type' => 'PAT',
                'price' => 1250.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante D', 'shelf' => 'Nivel 3'
            ],

            // Gastroenterología y Antiácidos
            [
                'sku' => 'NX-750100800601', 'name' => 'Omeprazol 20mg Cápsulas', 'category' => 'Gastroenterología y Antiácidos',
                'active_ingredient' => 'Omeprazol Magnésico 20mg', 'presentation' => 'Caja con 14 cápsulas', 'type' => 'GEN',
                'price' => 42.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante E', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800602', 'name' => 'Losec 20mg (Omeprazol)', 'category' => 'Gastroenterología y Antiácidos',
                'active_ingredient' => 'Omeprazol 20mg', 'presentation' => 'Caja con 14 tabletas MUPS', 'type' => 'PAT',
                'price' => 280.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante E', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800603', 'name' => 'Pantoprazol 40mg Tabletas', 'category' => 'Gastroenterología y Antiácidos',
                'active_ingredient' => 'Pantoprazol Sódico 40mg', 'presentation' => 'Caja con 14 grageas', 'type' => 'GEN',
                'price' => 90.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante E', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800604', 'name' => 'Butilhioscina 10mg Grageas', 'category' => 'Gastroenterología y Antiácidos',
                'active_ingredient' => 'Bromuro de Butilhioscina 10mg', 'presentation' => 'Caja con 10 grageas', 'type' => 'GEN',
                'price' => 45.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante E', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800605', 'name' => 'Buscapina 10mg (Butilhioscina)', 'category' => 'Gastroenterología y Antiácidos',
                'active_ingredient' => 'Bromuro de Butilhioscina 10mg', 'presentation' => 'Caja con 24 grageas', 'type' => 'PAT',
                'price' => 160.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante E', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800606', 'name' => 'Trimebutina 200mg Tabletas', 'category' => 'Gastroenterología y Antiácidos',
                'active_ingredient' => 'Maleato de Trimebutina 200mg', 'presentation' => 'Caja con 20 tabletas', 'type' => 'GEN',
                'price' => 110.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante E', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800607', 'name' => 'Pepto-Bismol Suspensión 236ml', 'category' => 'Gastroenterología y Antiácidos',
                'active_ingredient' => 'Subsalicilato de Bismuto 262mg/15ml', 'presentation' => 'Frasco con 236 ml', 'type' => 'PAT',
                'price' => 98.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante E', 'shelf' => 'Nivel 3'
            ],

            // Respiratorios y Antihistamínicos
            [
                'sku' => 'NX-750100800701', 'name' => 'Salbutamol Aerosol 100mcg', 'category' => 'Respiratorios y Antihistamínicos',
                'active_ingredient' => 'Sulfato de Salbutamol 100mcg/dosis', 'presentation' => 'Inhalador con 200 dosis', 'type' => 'GEN',
                'price' => 85.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante F', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800702', 'name' => 'Ventolin 100mcg (Salbutamol)', 'category' => 'Respiratorios y Antihistamínicos',
                'active_ingredient' => 'Salbutamol 100mcg/disparo', 'presentation' => 'Inhalador presurizado 200 dosis', 'type' => 'PAT',
                'price' => 310.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante F', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800703', 'name' => 'Loratadina 10mg Tabletas', 'category' => 'Respiratorios y Antihistamínicos',
                'active_ingredient' => 'Loratadina 10mg', 'presentation' => 'Caja con 10 tabletas', 'type' => 'GEN',
                'price' => 35.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante F', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800704', 'name' => 'Cetirizina 10mg Tabletas', 'category' => 'Respiratorios y Antihistamínicos',
                'active_ingredient' => 'Diclorhidrato de Cetirizina 10mg', 'presentation' => 'Caja con 10 tabletas', 'type' => 'GEN',
                'price' => 48.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante F', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800705', 'name' => 'Montelukast 10mg Tabletas', 'category' => 'Respiratorios y Antihistamínicos',
                'active_ingredient' => 'Montelukast Sódico 10mg', 'presentation' => 'Caja con 30 tabletas recubiertas', 'type' => 'GEN',
                'price' => 190.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante F', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800706', 'name' => 'Singulair 10mg (Montelukast)', 'category' => 'Respiratorios y Antihistamínicos',
                'active_ingredient' => 'Montelukast 10mg', 'presentation' => 'Caja con 30 tabletas', 'type' => 'PAT',
                'price' => 880.00, 'is_controlled' => false, 'prescription_required' => true, 'rack' => 'Estante F', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800707', 'name' => 'Ambroxol Jarabe Infantil 120ml', 'category' => 'Respiratorios y Antihistamínicos',
                'active_ingredient' => 'Clorhidrato de Ambroxol 15mg/5ml', 'presentation' => 'Frasco con 120 ml con vaso dosificador', 'type' => 'GEN',
                'price' => 55.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante F', 'shelf' => 'Nivel 4'
            ],

            // Vitaminas y Suplementos
            [
                'sku' => 'NX-750100800801', 'name' => 'Complejo B Forte Tabletas', 'category' => 'Vitaminas y Suplementos',
                'active_ingredient' => 'Tiamina, Piridoxina, Cianocobalamina', 'presentation' => 'Caja con 30 grageas', 'type' => 'GEN',
                'price' => 85.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante G', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800802', 'name' => 'Bedoyecta Tri Solución Inyectable', 'category' => 'Vitaminas y Suplementos',
                'active_ingredient' => 'Vitaminas B1, B6, B12', 'presentation' => 'Caja con 5 jeringas prellenadas', 'type' => 'PAT',
                'price' => 295.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante G', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800803', 'name' => 'Redoxon 1g Vitamina C Efervescente', 'category' => 'Vitaminas y Suplementos',
                'active_ingredient' => 'Ácido Ascórbico 1000mg', 'presentation' => 'Tubo con 10 tabletas efervescentes sabor naranja', 'type' => 'PAT',
                'price' => 125.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante G', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800804', 'name' => 'Vitamina D3 4000 UI Cápsulas', 'category' => 'Vitaminas y Suplementos',
                'active_ingredient' => 'Colecalciferol 4000 UI', 'presentation' => 'Frasco con 30 cápsulas blandas', 'type' => 'GEN',
                'price' => 160.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante G', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800805', 'name' => 'Ácido Fólico 5mg Tabletas', 'category' => 'Vitaminas y Suplementos',
                'active_ingredient' => 'Ácido Fólico 5mg', 'presentation' => 'Frasco con 50 tabletas', 'type' => 'GEN',
                'price' => 38.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante G', 'shelf' => 'Nivel 3'
            ],

            // Material de Curación y Botiquín
            [
                'sku' => 'NX-750100800901', 'name' => 'Alcohol Desnaturalizado 70% 500ml', 'category' => 'Material de Curación y Botiquín',
                'active_ingredient' => 'Alcohol Etílico 70° G.L.', 'presentation' => 'Botella plástica con 500 ml', 'type' => 'GEN',
                'price' => 36.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante H', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800902', 'name' => 'Agua Oxigenada 250ml', 'category' => 'Material de Curación y Botiquín',
                'active_ingredient' => 'Peróxido de Hidrógeno al 3%', 'presentation' => 'Botella con 250 ml', 'type' => 'GEN',
                'price' => 24.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante H', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800903', 'name' => 'Gasas Estériles 10x10cm Sobre c/5', 'category' => 'Material de Curación y Botiquín',
                'active_ingredient' => 'Algodón hidrófilo tejido', 'presentation' => 'Paquete con 10 sobres estériles', 'type' => 'GEN',
                'price' => 45.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante H', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800904', 'name' => 'Venda Elástica 10cm x 5m', 'category' => 'Material de Curación y Botiquín',
                'active_ingredient' => 'Tejido elástico algodón/poliéster', 'presentation' => 'Pieza individual con broches', 'type' => 'GEN',
                'price' => 28.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante H', 'shelf' => 'Nivel 2'
            ],
            [
                'sku' => 'NX-750100800905', 'name' => 'Jeringas Desechables 5ml Aguja 21G c/10', 'category' => 'Material de Curación y Botiquín',
                'active_ingredient' => 'Polipropileno grado médico estéril', 'presentation' => 'Bolsa con 10 jeringas individuales', 'type' => 'GEN',
                'price' => 42.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante H', 'shelf' => 'Nivel 3'
            ],
            [
                'sku' => 'NX-750100800906', 'name' => 'Termómetro Digital Clínico Infrarrojo', 'category' => 'Material de Curación y Botiquín',
                'active_ingredient' => 'Dispositivo médico electrónico', 'presentation' => 'Empaque individual con batería incluida', 'type' => 'PAT',
                'price' => 290.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Vitrina Mostrador', 'shelf' => 'Nivel 1'
            ],
            [
                'sku' => 'NX-750100800907', 'name' => 'Cubrebocas Tricapa Plisado Caja c/50', 'category' => 'Material de Curación y Botiquín',
                'active_ingredient' => 'Polipropileno termosellado 3 capas', 'presentation' => 'Caja con 50 cubrebocas termosellados', 'type' => 'GEN',
                'price' => 65.00, 'is_controlled' => false, 'prescription_required' => false, 'rack' => 'Estante H', 'shelf' => 'Nivel 4'
            ],
        ];

        // 8. Insertar o actualizar medicamentos del catálogo con sus lotes
        $insertedCount = 0;
        foreach ($catalog as $index => $item) {
            $cat = $categoryModels[$item['category']] ?? $categoryModels['Analgésicos y Antiinflamatorios'];
            $prov = $providerModels[$index % count($providerModels)];

            $product = Product::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'name'                  => $item['name'],
                    'price'                 => $item['price'],
                    'type'                  => $item['type'],
                    'description'           => "Medicamento " . ($item['is_controlled'] ? 'Controlado (Sujeto a Cédula Médica)' : 'de libre venta') . " - {$item['presentation']}",
                    'active_ingredient'     => $item['active_ingredient'],
                    'presentation'          => $item['presentation'],
                    'prescription_required' => $item['prescription_required'],
                    'is_controlled'         => $item['is_controlled'],
                    'category_id'           => $cat->id,
                    'provider_id'           => $prov->id,
                ]
            );

            // Generar lotes para Sucursal Central
            // Algunos con stock 0 para pruebas de bloqueo de venta
            // Algunos con stock bajo (3 unidades)
            // La gran mayoría con stock saludable (25 a 150 unidades)
            $stock = match ($index) {
                0, 15 => 0, // 2 productos intencionalmente sin stock
                1, 16 => 3, // 2 productos con stock bajo
                default => 25 + (($index * 7) % 110),
            };

            $cost = round($item['price'] * 0.68, 2);
            $lotNumber = 'LOT-2026-' . str_pad($index + 101, 4, '0', STR_PAD_LEFT);
            $expDate = now()->addMonths(12 + (($index * 3) % 24))->toDateString();

            Batch::updateOrCreate(
                [
                    'product_id'   => $product->id,
                    'branch_id'    => $centralBranch->id,
                    'batch_number' => $lotNumber,
                ],
                [
                    'stock'           => $stock,
                    'cost_price'      => $cost,
                    'selling_price'   => $item['price'],
                    'expiration_date' => $expDate,
                    'rack'            => $item['rack'],
                    'shelf'           => $item['shelf'],
                ]
            );

            // Además, para algunos productos creamos un segundo lote o existencias en Bodega Central
            if ($index % 3 === 0) {
                Batch::updateOrCreate(
                    [
                        'product_id'   => $product->id,
                        'branch_id'    => $bodega->id,
                        'batch_number' => 'BOD-' . str_pad($index + 500, 4, '0', STR_PAD_LEFT),
                    ],
                    [
                        'stock'           => 150,
                        'cost_price'      => $cost,
                        'selling_price'   => $item['price'],
                        'expiration_date' => now()->addMonths(24)->toDateString(),
                        'rack'            => 'Bodega Rack A',
                        'shelf'           => 'Tarima ' . (($index % 5) + 1),
                    ]
                );
            }

            $insertedCount++;
        }

        // 9. Completar medicamentos sintéticos con Factory hasta superar los 110 medicamentos
        $remainingNeeded = max(15, 110 - Product::count());
        if ($remainingNeeded > 0) {
            $synthNormal = Product::factory(intval($remainingNeeded * 0.7))->normal()->create([
                'category_id' => $categoryModels['Analgésicos y Antiinflamatorios']->id,
                'provider_id' => $providerModels[0]->id,
            ]);

            $synthControlled = Product::factory(intval($remainingNeeded * 0.3))->controlled()->create([
                'category_id' => $categoryModels['Psicotrópicos y Controlados']->id,
                'provider_id' => $providerModels[1]->id,
            ]);

            $syntheticProducts = $synthNormal->concat($synthControlled);

            foreach ($syntheticProducts as $i => $sProduct) {
                $stock = ($i >= count($syntheticProducts) - 3) ? 0 : (20 + (($i * 5) % 80));
                $cost = round(((float) $sProduct->price) * 0.70, 2);

                Batch::create([
                    'product_id'      => $sProduct->id,
                    'branch_id'       => $centralBranch->id,
                    'batch_number'    => 'SYN-2026-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'stock'           => $stock,
                    'cost_price'      => $cost,
                    'selling_price'   => (float) $sProduct->price,
                    'expiration_date' => now()->addMonths(18)->toDateString(),
                    'rack'            => 'Estante Gen',
                    'shelf'           => 'Nivel 1',
                ]);
            }
        }
    }
}
