<?php

namespace Database\Factories;

use App\Models\MonsterPassive;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonsterPassive>
 */
class MonsterPassiveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->sentence(),
        ];
    }
}
