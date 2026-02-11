<?php

namespace Database\Factories;

use App\Models\EnemyPassive;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnemyPassive>
 */
class EnemyPassiveFactory extends Factory
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
