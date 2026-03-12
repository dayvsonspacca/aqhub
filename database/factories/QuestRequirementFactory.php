<?php

namespace Database\Factories;

use App\Models\Quest;
use App\Models\QuestRequirement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestRequirement>
 */
class QuestRequirementFactory extends Factory
{
    protected $model = QuestRequirement::class;

    public function definition(): array
    {
        return [
            'quest_id' => Quest::factory(),
            'type' => 'level',
            'required_level' => fake()->numberBetween(1, 100),
        ];
    }

    public function levelRequirement(int $level): static
    {
        return $this->state(fn () => [
            'type' => 'level',
            'required_level' => $level,
        ]);
    }
}
