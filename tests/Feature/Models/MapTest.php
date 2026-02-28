<?php

namespace Tests\Feature\Models;

use App\Models\Map;
use Carbon\CarbonInterface;
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
        $fillable = ['name', 'join_name', 'created_at'];

        $map = new Map;
        $this->assertEquals($fillable, $map->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $map = Map::factory()->create();

        $this->assertIsInt($map->id);
        $this->assertIsString($map->name);
        $this->assertNull($map->registered_at);
        $this->assertInstanceOf(CarbonInterface::class, $map->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $map->updated_at);
    }

    #[Test]
    public function it_can_be_created_with_valid_data(): void
    {
        $data = [
            'name' => 'Battleon',
            'join_name' => 'battleon',
        ];

        Map::create($data);

        $this->assertDatabaseHas('maps', [
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
}
