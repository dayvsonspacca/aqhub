<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Jobs\FindMapMonstersJob;
use App\Models\Region;
use App\Services\Http\AqwGameApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SyncAqwRegionsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<int, array<string, mixed>> */
    private function travelMapPayload(): array
    {
        return [
            [
                'regionName' => 'Greenguard',
                'regionID' => 1,
                'maps' => [
                    [
                        'mapID' => '100',
                        'mapTitle' => 'Battleon',
                        'mapName' => 'battleon',
                        'description' => 'The main hub area.',
                        'bUpgrade' => '0',
                        'minLevel' => '1',
                        'maxLevel' => '10',
                    ],
                    [
                        'mapID' => '101',
                        'mapTitle' => 'Greenguard Forest',
                        'mapName' => 'greenguardwest',
                        'description' => 'A forest full of danger.',
                        'bUpgrade' => '0',
                        'minLevel' => '5',
                        'maxLevel' => '15',
                    ],
                ],
            ],
        ];
    }

    private function fakeTravelMap(mixed $payload = null): void
    {
        Http::fake([
            AqwGameApiService::URL . '/game/api/data/travelmap*' => Http::response(
                $payload ?? $this->travelMapPayload()
            ),
        ]);
    }

    #[Test]
    public function it_creates_regions_from_travel_map(): void
    {
        Queue::fake();
        $this->fakeTravelMap();

        $this->artisan('aqw:sync-regions')->assertSuccessful();

        $this->assertDatabaseHas('regions', [
            'name' => 'Greenguard',
            'aqw_id' => 1,
        ]);
    }

    #[Test]
    public function it_creates_maps_for_each_region(): void
    {
        Queue::fake();
        $this->fakeTravelMap();

        $this->artisan('aqw:sync-regions')->assertSuccessful();

        $this->assertDatabaseCount('maps', 2);
        $this->assertDatabaseHas('maps', ['aqw_id' => 100, 'name' => 'Battleon']);
        $this->assertDatabaseHas('maps', ['aqw_id' => 101, 'name' => 'Greenguard Forest']);
    }

    #[Test]
    public function it_associates_maps_to_their_region(): void
    {
        Queue::fake();
        $this->fakeTravelMap();

        $this->artisan('aqw:sync-regions')->assertSuccessful();

        $region = Region::query()->where('aqw_id', 1)->first();

        $this->assertNotNull($region);
        $this->assertCount(2, $region->maps);
    }

    #[Test]
    public function it_handles_regions_with_no_maps(): void
    {
        Queue::fake();
        $this->fakeTravelMap([
            [
                'regionName' => 'Empty Region',
                'regionID' => 99,
                'maps' => [],
            ],
        ]);

        $this->artisan('aqw:sync-regions')->assertSuccessful();

        $this->assertDatabaseHas('regions', ['name' => 'Empty Region', 'aqw_id' => 99]);
        $this->assertDatabaseCount('maps', 0);
    }

    #[Test]
    public function it_does_not_duplicate_existing_regions(): void
    {
        Queue::fake();
        $this->fakeTravelMap();

        $this->artisan('aqw:sync-regions')->assertSuccessful();
        $this->artisan('aqw:sync-regions')->assertSuccessful();

        $this->assertDatabaseCount('regions', 1);
    }

    #[Test]
    public function it_does_not_duplicate_existing_maps(): void
    {
        Queue::fake();
        $this->fakeTravelMap();

        $this->artisan('aqw:sync-regions')->assertSuccessful();
        $this->artisan('aqw:sync-regions')->assertSuccessful();

        $this->assertDatabaseCount('maps', 2);
    }

    #[Test]
    public function it_dispatches_find_map_monsters_job_for_each_map(): void
    {
        Queue::fake();
        $this->fakeTravelMap();

        $this->artisan('aqw:sync-regions')->assertSuccessful();

        Queue::assertPushed(FindMapMonstersJob::class, 2);
    }
}
