<?php

namespace Database\Factories;

use App\Models\Quest;
use App\Models\QuestReward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestReward>
 */
class QuestRewardFactory extends Factory
{
    protected $model = QuestReward::class;

    public function definition(): array
    {
        return [
            'quest_id' => Quest::factory(),
            'type' => 'experience',
            'amount' => fake()->numberBetween(100, 50000),
        ];
    }

    public function goldReward(int $amount): static
    {
        return $this->state(fn () => [
            'type' => 'gold',
            'amount' => $amount,
        ]);
    }
}
