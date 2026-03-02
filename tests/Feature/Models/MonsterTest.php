<?php

namespace Tests\Feature\Models;

use App\Models\Monster;
use AqwSocketClient\Objects\Health;
use AqwSocketClient\Objects\Levels\MonsterLevel;
use AqwSocketClient\Objects\Names\MonsterName;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MonsterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'level',
            'health',
            'asset_name',
            'asset_link',
        ];

        $monster = new Monster;
        $this->assertEquals($fillable, $monster->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $monster = Monster::factory()->create();

        $this->assertIsInt($monster->id);
        $this->assertInstanceOf(MonsterName::class, $monster->name);
        $this->assertInstanceOf(MonsterLevel::class, $monster->level);
        $this->assertInstanceOf(Health::class, $monster->health);

        $this->assertIsString($monster->asset_name);
        $this->assertIsString($monster->asset_link);

        $this->assertInstanceOf(CarbonInterface::class, $monster->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $monster->updated_at);
    }

    #[Test]
    public function it_can_be_created_with_valid_data(): void
    {
        $data = [
            'name' => new MonsterName('Doomlord'),
            'level' => new MonsterLevel(1),
            'health' => new Health(100),
            'asset_name' => 'Draconian5.swf',
            'asset_link' => 'Draconian5',
        ];

        $monster = Monster::create($data);

        $this->assertDatabaseHas('monsters', [
            'name' => 'Doomlord',
            'level' => 1,
            'health' => 100,
            'asset_name' => 'Draconian5.swf',
            'asset_link' => 'Draconian5',
        ]);

        $this->assertNotNull($monster->created_at);
    }

    #[Test]
    public function it_has_passives_relationship(): void
    {
        $monster = Monster::factory()->create();

        $this->assertTrue(method_exists($monster, 'passives'));
        $this->assertInstanceOf(BelongsToMany::class, $monster->passives());
    }

    #[Test]
    public function it_has_maps_relationship(): void
    {
        $monster = Monster::factory()->create();

        $this->assertTrue(method_exists($monster, 'maps'));
        $this->assertInstanceOf(BelongsToMany::class, $monster->maps());
    }
}
