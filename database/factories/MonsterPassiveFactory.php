<?php

namespace Database\Factories;

use App\Models\MonsterPassive;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonsterPassive>
 */
class MonsterPassiveFactory extends Factory
{
    protected $model = MonsterPassive::class;

    public function definition(): array
    {
        return [
            'description' => fake()->sentence(5),
        ];
    }
}
