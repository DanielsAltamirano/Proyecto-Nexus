<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $ingredients = [
            'Paracetamol', 'Ibuprofeno', 'Amoxicilina', 'Omeprazol', 'Losartán',
            'Metformina', 'Atorvastatina', 'Clonazepam', 'Alprazolam', 'Metilfenidato',
            'Zolpidem', 'Tramadol', 'Diazepam', 'Ciprofloxacino', 'Loratadina',
            'Diclofenaco', 'Naproxeno', 'Ketorolaco', 'Azitromicina', 'Pregabalina',
        ];

        $presentations = [
            'Caja con 10 tabletas', 'Caja con 20 cápsulas', 'Caja con 30 comprimidos',
            'Frasco con 120 ml', 'Caja con 14 tabletas de liberación prolongada',
            'Caja con 3 ampolletas de 2 ml', 'Frasco gotero 30 ml',
        ];

        $dosages = ['10mg', '20mg', '50mg', '100mg', '250mg', '500mg', '850mg', '2mg'];

        $isControlled = fake()->boolean(30);
        $ingredient = fake()->randomElement($ingredients);
        $dosage = fake()->randomElement($dosages);
        $presentation = fake()->randomElement($presentations);
        $type = fake()->randomElement(['GEN', 'PAT']);

        return [
            'sku'                   => 'NX-' . strtoupper(Str::random(4)) . '-' . fake()->unique()->numerify('#####'),
            'name'                  => "{$ingredient} {$dosage} ({$type}) - " . strtoupper(Str::random(4)),
            'price'                 => fake()->randomFloat(2, 35, 950),
            'type'                  => $type,
            'description'           => "Medicamento " . ($isControlled ? 'Controlado (Fracción II/III)' : 'de venta general') . " - {$presentation}",
            'active_ingredient'     => "{$ingredient} {$dosage}",
            'presentation'          => $presentation,
            'prescription_required' => $isControlled ? true : fake()->boolean(40),
            'is_controlled'         => $isControlled,
            'category_id'           => Category::query()->inRandomOrder()->value('id') ?? 1,
            'provider_id'           => Provider::query()->inRandomOrder()->value('id') ?? 1,
        ];
    }

    /**
     * Estado para medicamento normal (no controlado).
     */
    public function normal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_controlled'         => false,
            'prescription_required' => false,
            'description'           => 'Medicamento normal de libre venta',
        ]);
    }

    /**
     * Estado para medicamento controlado (exige cédula médica).
     */
    public function controlled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_controlled'         => true,
            'prescription_required' => true,
            'description'           => 'Medicamento Controlado sujeto a validación de cédula médica',
        ]);
    }
}

