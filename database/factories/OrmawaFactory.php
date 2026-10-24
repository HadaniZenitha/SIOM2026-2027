<?php

namespace Database\Factories;

use App\Models\Ormawa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ormawa>
 */
class OrmawaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->regexify('[A-Z]{5}'),
            'name' => fake()->unique()->company(),
            'is_active' => true,
        ];
    }
}
