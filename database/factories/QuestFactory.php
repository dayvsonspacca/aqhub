<?php

namespace Database\Factories;

use App\Models\Quest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quest>
 */
class QuestFactory extends Factory
{
    protected $model = Quest::class;

    public function definition(): array
    {
        return [
            'aqw_id' => fake()->unique()->numberBetween(1, 999999),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'completion_text' => fake()->sentence(),
        ];
    }
}
