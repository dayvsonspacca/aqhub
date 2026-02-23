<?php

namespace Database\Factories;

use App\Models\MapLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MapLocation>
 */
class MapLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'join_name' => mb_strtolower($this->faker->word()),
        ];
    }
}
