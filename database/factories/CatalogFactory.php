<?php

namespace Database\Factories;

use App\Models\Catalog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Catalog>
 */
class CatalogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(Catalog::types());
        $name = fake()->unique()->company();
        $subtypes = Catalog::subTypeOptions()[$type] ?? [];

        return [
            'name' => $name,
            'slug' => Catalog::uniqueSlugFromName($name),
            'type' => $type,
            'sub_type' => $subtypes !== [] ? fake()->randomElement($subtypes) : 'Umum',
            'description' => fake()->paragraph(),
            'owner_name' => $type === 'umkm' ? fake()->name() : null,
            'responsible_person' => $type === 'umkm' ? null : fake()->name(),
            'address' => fake()->streetAddress().', Kelurahan Tambakrejo',
            'email' => fake()->optional()->safeEmail(),
            'whatsapp' => '08123456789',
            'phone' => fake()->optional()->numerify('031#######'),
            'instagram' => fake()->optional()->userName(),
            'tiktok' => fake()->optional()->userName(),
            'other_contact' => null,
            'photo' => null,
            'status' => 'published',
        ];
    }

    public function umkm(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'umkm',
            'sub_type' => fake()->randomElement(Catalog::subTypeOptions()['umkm'] ?? ['Umum']),
            'owner_name' => fake()->name(),
            'responsible_person' => null,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }
}
