<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'aqw_id' => fake()->unique()->numberBetween(1, 999999),
            'name' => fake()->words(2, true),
        ];
    }

    public function nameless(): static
    {
        return $this->state(fn () => ['name' => null]);
    }
}
