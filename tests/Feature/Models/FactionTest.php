<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Faction;
use AqwSocketClient\Objects\Names\FactionName;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FactionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $this->assertEquals(['aqw_id', 'name'], (new Faction)->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $faction = Faction::factory()->create();

        $this->assertIsInt($faction->id);
        $this->assertIsInt($faction->aqw_id);
        $this->assertInstanceOf(FactionName::class, $faction->name);
        $this->assertInstanceOf(CarbonInterface::class, $faction->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $faction->updated_at);
    }
}
