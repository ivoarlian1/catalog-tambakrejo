<?php

namespace Database\Factories;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'role' => 'catalog_admin',
            'catalog_id' => null,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'superadmin',
            'catalog_id' => null,
        ]);
    }

    public function catalogAdmin(?Catalog $catalog = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'catalog_admin',
            'catalog_id' => $catalog?->id,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
