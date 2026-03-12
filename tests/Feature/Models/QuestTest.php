<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Quest;
use AqwSocketClient\Objects\Names\QuestName;
use Carbon\CarbonInterface;
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
}
