<?php

namespace App\Actions;

use App\Actions\Contracts\UpsertQuestContract;
use App\Models\CharacterClass;
use App\Models\Faction;
use App\Models\Item;
use App\Models\Quest as QuestModel;
use AqwSocketClient\Objects\Quest\ClassRankRequirement;
use AqwSocketClient\Objects\Quest\ExperienceReward;
use AqwSocketClient\Objects\Quest\GoldReward;
use AqwSocketClient\Objects\Quest\ItemReward;
use AqwSocketClient\Objects\Quest\LevelRequirement;
use AqwSocketClient\Objects\Quest\Quest;
use AqwSocketClient\Objects\Quest\ReputationRequirement;
use AqwSocketClient\Objects\Quest\ReputationReward;
use Illuminate\Support\Facades\DB;

final class UpsertQuest implements UpsertQuestContract
{
    public function handle(Quest $quest): QuestModel
    {
        return DB::transaction(function () use ($quest): QuestModel {
            /** @var QuestModel $model */
            $model = QuestModel::firstOrCreate(
                ['aqw_id' => $quest->identifier->value],
                [
                    'name' => $quest->name->value,
                    'description' => $quest->description->text,
                    'completion_text' => $quest->description->completionText,
                ],
            );

            if (! $model->wasRecentlyCreated) {
                return $model;
            }

            $this->syncTags($model, $quest);
            $this->syncRewards($model, $quest);
            $this->syncRequirements($model, $quest);
            $this->syncTurnInItems($model, $quest);

            return $model;
        });
    }

    private function syncTags(QuestModel $model, Quest $quest): void
    {
        foreach ($quest->tags as $tag) {
            $model->tags()->create(['tag' => $tag->name]);
        }
    }

    private function syncRewards(QuestModel $model, Quest $quest): void
    {
        foreach ($quest->rewards as $reward) {
            if ($reward instanceof ExperienceReward) {
                $model->rewards()->create(['type' => 'experience', 'amount' => $reward->amount]);
            } elseif ($reward instanceof GoldReward) {
                $model->rewards()->create(['type' => 'gold', 'amount' => $reward->amount]);
            } elseif ($reward instanceof ReputationReward) {
                $faction = Faction::firstOrCreate(
                    ['aqw_id' => $reward->faction->identifier->value],
                    ['name' => $reward->faction->name->value],
                );

                $model->rewards()->create([
                    'type' => 'reputation',
                    'amount' => $reward->amount,
                    'faction_id' => $faction->id,
                ]);
            } elseif ($reward instanceof ItemReward) {
                $item = Item::firstOrCreate(['aqw_id' => $reward->itemIdentifier->value]);

                $model->rewards()->create([
                    'type' => 'item',
                    'item_id' => $item->id,
                    'rate' => $reward->rate,
                    'quantity' => $reward->quantity,
                ]);
            }
        }
    }

    private function syncRequirements(QuestModel $model, Quest $quest): void
    {
        foreach ($quest->requirements as $requirement) {
            if ($requirement instanceof LevelRequirement) {
                $model->requirements()->create([
                    'type' => 'level',
                    'required_level' => $requirement->level->value,
                ]);
            } elseif ($requirement instanceof ReputationRequirement) {
                $faction = Faction::firstOrCreate(['aqw_id' => $requirement->factionIdentifier->value]);

                $model->requirements()->create([
                    'type' => 'reputation',
                    'faction_id' => $faction->id,
                    'required_rank' => $requirement->rank->value,
                ]);
            } elseif ($requirement instanceof ClassRankRequirement) {
                $class = CharacterClass::firstOrCreate(['aqw_id' => $requirement->classIdentifier->value]);

                $model->requirements()->create([
                    'type' => 'class_rank',
                    'class_id' => $class->id,
                    'required_class_rank' => $requirement->rank->value,
                ]);
            }
        }
    }

    private function syncTurnInItems(QuestModel $model, Quest $quest): void
    {
        foreach ($quest->turnInItems as $turnInItem) {
            $item = Item::firstOrCreate(['aqw_id' => $turnInItem->itemIdentifier->value]);

            $model->turnInItems()->create([
                'item_id' => $item->id,
                'quantity' => $turnInItem->quantity,
            ]);
        }
    }
}
