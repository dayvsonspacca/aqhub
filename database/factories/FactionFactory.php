<?php

namespace Database\Factories;

use App\Models\Faction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faction>
 */
class FactionFactory extends Factory
{
    protected $model = Faction::class;

    public function definition(): array
    {
        return [
            'aqw_id' => fake()->unique()->numberBetween(1, 999999),
            'name' => fake()->words(2, true),
        ];
    }
}
