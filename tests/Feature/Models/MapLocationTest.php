<?php

namespace Tests\Feature\Models;

use App\Models\MapLocation;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MapLocationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = ['name', 'join_name', 'created_at'];

        $mapLocation = new MapLocation;
        $this->assertEquals($fillable, $mapLocation->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $mapLocation = MapLocation::factory()->create();

        $this->assertIsInt($mapLocation->id);
        $this->assertIsString($mapLocation->name);
        $this->assertNull($mapLocation->registered_at);
        $this->assertInstanceOf(CarbonInterface::class, $mapLocation->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $mapLocation->updated_at);
    }

    #[Test]
    public function it_can_be_created_with_valid_data(): void
    {
        $data = [
            'name' => 'Battleon',
            'join_name' => 'battleon',
        ];

        MapLocation::create($data);

        $this->assertDatabaseHas('map_locations', [
            'name' => 'Battleon',
            'join_name' => 'battleon',
        ]);
    }
}
