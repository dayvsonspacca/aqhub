<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Region>
 */
class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'name' => Str::title(fake()->unique()->word()),
            'aqw_id' => fake()->unique()->numberBetween(1, 5000),
            'created_at' => fake()->dateTimeBetween('-2 years', '-1 month'),
            'updated_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
