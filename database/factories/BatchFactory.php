<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Batch>
 */
class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        $cost = fake()->randomFloat(2, 25, 600);
        $selling = round($cost * 1.40, 2);

        return [
            'product_id'      => Product::Factory(),
            'branch_id'       => Branch::query()->inRandomOrder()->value('id') ?? 1,
            'batch_number'    => 'LOTE-' . date('Y') . '-' . strtoupper(Str::random(5)),
            'stock'           => fake()->numberBetween(10, 250),
            'cost_price'      => $cost,
            'selling_price'   => $selling,
            'expiration_date' => now()->addMonths(fake()->numberBetween(6, 36))->toDateString(),
            'rack'            => 'R-' . fake()->numberBetween(1, 10),
            'shelf'           => 'N-' . fake()->numberBetween(1, 5),
        ];
    }

    /**
     * Lote sin existencias (stock = 0) para pruebas de bloqueo de venta.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}

