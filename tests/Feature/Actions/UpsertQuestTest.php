<?php

declare(strict_types=1);

namespace Tests\Feature\Actions;

use App\Actions\UpsertQuest;
use App\Models\Faction;
use App\Models\Item;
use App\Models\Quest;
use AqwSocketClient\Events\QuestLoadedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Messages\JsonMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UpsertQuestTest extends TestCase
{
    use RefreshDatabase;

    private function questFromFixture(string $fixture): \AqwSocketClient\Objects\Quest\Quest
    {
        /** @var QuestLoadedEvent $event */
        $event = QuestLoadedEvent::from(JsonMessage::from($fixture));

        return $event->quest;
    }

    #[Test]
    public function it_creates_a_quest(): void
    {
        $quest = $this->questFromFixture(MessageGenerator::questLoaded());

        $model = (new UpsertQuest)->handle($quest);

        $this->assertInstanceOf(Quest::class, $model);
        $this->assertDatabaseHas('quests', ['aqw_id' => 868, 'name' => 'Nulgath (Rare)']);
    }

    #[Test]
    public function it_does_not_duplicate_quests(): void
    {
        $quest = $this->questFromFixture(MessageGenerator::questLoaded());
        $action = new UpsertQuest;

        $first = $action->handle($quest);
        $second = $action->handle($quest);

        $this->assertSame($first->id, $second->id);
        $this->assertDatabaseCount('quests', 1);
    }

    #[Test]
    public function it_persists_tags(): void
    {
        $quest = $this->questFromFixture(MessageGenerator::questLoadedWithTagsAndRequirements());

        (new UpsertQuest)->handle($quest);

        $this->assertDatabaseHas('quest_tags', ['tag' => 'OneTime']);
        $this->assertDatabaseHas('quest_tags', ['tag' => 'MemberOnly']);
    }

    #[Test]
    public function it_persists_rewards(): void
    {
        $quest = $this->questFromFixture(MessageGenerator::questLoaded());

        (new UpsertQuest)->handle($quest);

        $this->assertDatabaseHas('quest_rewards', ['type' => 'experience', 'amount' => 300]);
        $this->assertDatabaseHas('quest_rewards', ['type' => 'gold', 'amount' => 13000]);
        $this->assertDatabaseHas('quest_rewards', ['type' => 'item']);
    }

    #[Test]
    public function it_creates_item_stubs_for_item_rewards(): void
    {
        $quest = $this->questFromFixture(MessageGenerator::questLoaded());

        (new UpsertQuest)->handle($quest);

        $this->assertDatabaseHas('items', ['aqw_id' => 4861]);
    }

    #[Test]
    public function it_persists_reputation_reward_and_creates_faction(): void
    {
        $quest = $this->questFromFixture(MessageGenerator::questLoaded());

        (new UpsertQuest)->handle($quest);

        $this->assertDatabaseHas('quest_rewards', ['type' => 'reputation', 'amount' => 300]);
        $this->assertDatabaseHas('factions', ['aqw_id' => 4, 'name' => 'Evil']);
    }

    #[Test]
    public function it_persists_turn_in_items(): void
    {
        $quest = $this->questFromFixture(MessageGenerator::questLoaded());

        (new UpsertQuest)->handle($quest);

        $this->assertDatabaseHas('items', ['aqw_id' => 15385]);
        $this->assertDatabaseHas('quest_turn_in_items', ['quantity' => 5]);
    }

    #[Test]
    public function it_persists_requirements(): void
    {
        $quest = $this->questFromFixture(MessageGenerator::questLoadedWithTagsAndRequirements());

        (new UpsertQuest)->handle($quest);

        $this->assertDatabaseHas('quest_requirements', ['type' => 'level', 'required_level' => 30]);
        $this->assertDatabaseHas('quest_requirements', ['type' => 'reputation']);
        $this->assertDatabaseHas('quest_requirements', ['type' => 'class_rank']);
    }

    #[Test]
    public function it_reuses_existing_faction_when_already_in_db(): void
    {
        Faction::factory()->create(['aqw_id' => 4, 'name' => 'Evil']);

        $quest = $this->questFromFixture(MessageGenerator::questLoaded());

        (new UpsertQuest)->handle($quest);

        $this->assertDatabaseCount('factions', 1);
    }

    #[Test]
    public function it_reuses_existing_item_stubs(): void
    {
        Item::factory()->create(['aqw_id' => 4861]);

        $quest = $this->questFromFixture(MessageGenerator::questLoaded());

        (new UpsertQuest)->handle($quest);

        $this->assertDatabaseCount('items', 2); // 4861 (reward) already existed + 15385 (turn-in) created
    }
}
