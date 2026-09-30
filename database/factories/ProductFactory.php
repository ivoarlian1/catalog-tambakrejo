<?php

namespace Database\Factories;

use App\Models\Catalog;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'catalog_id' => Catalog::factory()->umkm(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(5000, 150000),
            'photo' => null,
            'is_available' => true,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }
}
