<?php

namespace Tests\Feature\Models;

use App\Models\Region;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RegionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'aqw_id',
        ];

        $region = new Region;

        $this->assertEquals($fillable, $region->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $region = Region::factory()->create();

        $this->assertIsInt($region->id);
        $this->assertIsInt($region->aqw_id);
        $this->assertIsString($region->name);

        $this->assertInstanceOf(CarbonInterface::class, $region->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $region->updated_at);
    }

    #[Test]
    public function it_can_be_created_with_valid_data(): void
    {
        $data = [
            'name' => 'Battleon',
            'aqw_id' => 1,
        ];

        $region = Region::create($data);

        $this->assertDatabaseHas('regions', [
            'name' => 'Battleon',
            'aqw_id' => 1,
        ]);

        $this->assertNotNull($region->created_at);
    }

    #[Test]
    public function it_has_maps_relationship(): void
    {
        $region = Region::factory()->create();

        $this->assertTrue(method_exists($region, 'maps'));
        $this->assertInstanceOf(HasMany::class, $region->maps());
    }
}
