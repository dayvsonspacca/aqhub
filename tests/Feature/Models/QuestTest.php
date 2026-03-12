<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Quest;
use AqwSocketClient\Objects\Names\QuestName;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class QuestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $this->assertEquals(
            ['aqw_id', 'name', 'description', 'completion_text'],
            (new Quest)->getFillable()
        );
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $quest = Quest::factory()->create();

        $this->assertIsInt($quest->id);
        $this->assertIsInt($quest->aqw_id);
        $this->assertInstanceOf(QuestName::class, $quest->name);
        $this->assertIsString($quest->description);
        $this->assertIsString($quest->completion_text);
        $this->assertInstanceOf(CarbonInterface::class, $quest->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $quest->updated_at);
    }

    #[Test]
    public function it_has_tags_relationship(): void
    {
        $quest = Quest::factory()->create();

        $this->assertInstanceOf(HasMany::class, $quest->tags());
    }

    #[Test]
    public function it_has_requirements_relationship(): void
    {
        $quest = Quest::factory()->create();

        $this->assertInstanceOf(HasMany::class, $quest->requirements());
    }

    #[Test]
    public function it_has_rewards_relationship(): void
    {
        $quest = Quest::factory()->create();

        $this->assertInstanceOf(HasMany::class, $quest->rewards());
    }

    #[Test]
    public function it_has_turn_in_items_relationship(): void
    {
        $quest = Quest::factory()->create();

        $this->assertInstanceOf(HasMany::class, $quest->turnInItems());
    }

    #[Test]
    public function it_has_maps_relationship(): void
    {
        $quest = Quest::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $quest->maps());
    }
}
