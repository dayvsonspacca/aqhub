<?php

namespace Database\Factories;

use App\Models\Monster;
use App\Models\MonsterPassive;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Monster>
 */
class MonsterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'level' => fake()->numberBetween(1, 100),
            'health' => fake()->numberBetween(50, 5000),
            'difficulty' => fake()->numberBetween(1, 5),
            'asset_name' => 'Draconian' . fake()->numberBetween(1, 5) . '.swf',
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

    public function configure(): static
    {
        return $this->afterCreating(function (Monster $monster) {
            $passives = MonsterPassive::factory(fake()->numberBetween(0, 4))->create();
            $monster->passives()->attach($passives);
        });
    }
}
