<?php

namespace Database\Factories;

use App\Models\Enemy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enemy>
 */
class EnemyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Goblin',
                'Orc',
                'Troll',
                'Dragon',
                'Skeleton',
                'Zombie',
                'Vampire',
                'Werewolf',
            ]),
            'level' => fake()->numberBetween(1, 100),
            'health' => fake()->numberBetween(50, 5000),
            'difficulty' => fake()->numberBetween(1, 10),
            'created_at' => null,
            'registered_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function created(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
