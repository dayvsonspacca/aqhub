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
            'description' => fake()->unique()->randomElement([
                'Reduces incoming damage by 20%',
                'Heals 5% HP every 3 seconds',
                'Cant be stunned.',
                'Reflects 10% of damage taken',
                'Increases attack speed',
                'Enrages below 30% health',
            ]),
        ];
    }
}
