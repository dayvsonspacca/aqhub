<?php

namespace Database\Factories;

use App\Models\Map;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Map>
 */
class MapFactory extends Factory
{
    protected $model = Map::class;

    public function definition(): array
    {
        $minLevel = fake()->optional()->numberBetween(1, 50);

        return [
            'aqw_id' => fake()->unique()->numberBetween(1, 999999),
            'name' => Str::title(fake()->words(2, true)),
            'join_name' => mb_strtolower(
                Str::slug(fake()->unique()->words(2, true), '')
            ),
            'description' => fake()->optional()->sentence(),
            'upgrade_only' => fake()->boolean(5),
            'recommended_min_level' => $minLevel,
            'recommended_max_level' => $minLevel
                ? fake()->numberBetween($minLevel, 100)
                : null,

            'region_id' => fake()->boolean(70)
                ? Region::query()->inRandomOrder()->value('id')
                : null,
        ];
    }
}
