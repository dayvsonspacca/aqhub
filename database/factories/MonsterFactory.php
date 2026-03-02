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
    protected $model = Monster::class;

    public function definition(): array
    {
        $baseName = fake()->randomElement([
            'Draconian',
            'Skeleton',
            'Chaos Beast',
            'Shadow Fiend',
            'Void Reaper',
            'Fire Elemental',
            'Doom Knight',
        ]);

        $variant = fake()->optional(0.6)->numberBetween(1, 5);

        $fullName = $variant
            ? "{$baseName} {$variant}"
            : $baseName;

        $assetIndex = fake()->numberBetween(1, 5);

        return [
            'name' => $fullName,
            'level' => fake()->numberBetween(1, 100),
            'health' => fake()->numberBetween(100, 15000),

            'asset_name' => "Draconian{$assetIndex}.swf",
            'asset_link' => "Draconian{$assetIndex}",

            'created_at' => fake()->dateTimeBetween('-1 year', '-1 month'),
            'updated_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Monster $monster) {

            $passives = MonsterPassive::factory()
                ->count(fake()->numberBetween(0, 3))
                ->create();

            $monster->passives()->attach($passives->pluck('id'));
        });
    }

    public function boss(): static
    {
        return $this->state(fn () => [
            'level' => fake()->numberBetween(80, 120),
            'health' => fake()->numberBetween(20000, 150000),
        ]);
    }
}
