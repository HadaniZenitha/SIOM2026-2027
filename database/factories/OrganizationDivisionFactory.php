<?php

namespace Database\Factories;

use App\Models\OrganizationDivision;
use App\Models\Ormawa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizationDivision>
 */
class OrganizationDivisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ormawa_id' => Ormawa::factory(),
            'name' => fake()->unique()->words(2, true),
        ];
    }
}
