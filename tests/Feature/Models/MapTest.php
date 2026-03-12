<?php

namespace Tests\Feature\Models;

use App\Models\Map;
use AqwSocketClient\Objects\Names\AreaName;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MapTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = [
            'aqw_id',
            'name',
            'join_name',
            'description',
            'upgrade_only',
            'recommended_min_level',
            'recommended_max_level',
            'region_id',
        ];

        $map = new Map;
        $this->assertEquals($fillable, $map->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $map = Map::factory()->create();

        $this->assertIsInt($map->id);
        $this->assertIsInt($map->aqw_id);
        $this->assertIsString($map->name);
        $this->assertInstanceOf(AreaName::class, $map->join_name);
        $this->assertIsBool($map->upgrade_only);
        $this->assertInstanceOf(CarbonInterface::class, $map->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $map->updated_at);
    }

    #[Test]
    public function it_can_be_created_with_valid_data(): void
    {
        $data = [
            'aqw_id' => 1,
            'name' => 'Battleon',
            'join_name' => 'battleon',
            'description' => 'Main city',
            'upgrade_only' => false,
            'recommended_min_level' => 1,
            'recommended_max_level' => 10,
        ];

        Map::create($data);

        $this->assertDatabaseHas('maps', [
            'aqw_id' => 1,
            'name' => 'Battleon',
            'join_name' => 'battleon',
        ]);
    }

    #[Test]
    public function it_has_monsters_relationship(): void
    {
        $map = Map::factory()->create();

        $this->assertTrue(method_exists($map, 'monsters'));
        $this->assertInstanceOf(BelongsToMany::class, $map->monsters());
    }

    #[Test]
    public function it_has_region_relationship(): void
    {
        $map = Map::factory()->create();

        $this->assertTrue(method_exists($map, 'region'));
        $this->assertInstanceOf(BelongsTo::class, $map->region());
    }

    #[Test]
    public function it_has_quests_relationship(): void
    {
        $map = Map::factory()->create();

        $this->assertInstanceOf(BelongsToMany::class, $map->quests());
    }
}
