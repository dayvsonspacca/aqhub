<?php

declare(strict_types=1);

namespace Tests\Feature\Actions;

use App\Actions\CreateMap;
use App\Jobs\FindMapMonstersJob;
use App\Models\Map;
use AqwSocketClient\Objects\Names\AreaName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateMapTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_map(): void
    {
        Queue::fake();

        $action = new CreateMap;
        $joinName = new AreaName('battleon');

        $map = $action->handle(
            aqwId: 1,
            name: 'Battleon',
            join_name: $joinName,
            description: 'The main hub area.',
            upgrade_only: false,
            recommended_min_level: 1,
            recommended_max_level: 10,
        );

        $this->assertInstanceOf(Map::class, $map);
        $this->assertDatabaseCount('maps', 1);
        $this->assertDatabaseHas('maps', [
            'aqw_id' => 1,
            'name' => 'Battleon',
            'join_name' => $joinName,
            'description' => 'The main hub area.',
            'upgrade_only' => false,
            'recommended_min_level' => 1,
            'recommended_max_level' => 10,
        ]);
    }

    #[Test]
    public function it_returns_existing_map_without_creating_duplicate(): void
    {
        Queue::fake();

        $action = new CreateMap;
        $joinName = new AreaName('battleon');

        $first = $action->handle(
            aqwId: 1,
            name: 'Battleon',
            join_name: $joinName,
            description: null,
            upgrade_only: false,
            recommended_min_level: null,
            recommended_max_level: null,
        );

        $second = $action->handle(
            aqwId: 1,
            name: 'Battleon Updated',
            join_name: $joinName,
            description: null,
            upgrade_only: false,
            recommended_min_level: null,
            recommended_max_level: null,
        );

        $this->assertSame($first->id, $second->id);
        $this->assertDatabaseCount('maps', 1);
    }

    #[Test]
    public function it_dispatches_find_map_monsters_job(): void
    {
        Queue::fake();

        $action = new CreateMap;

        $map = $action->handle(
            aqwId: 1,
            name: 'Battleon',
            join_name: new AreaName('battleon'),
            description: null,
            upgrade_only: false,
            recommended_min_level: null,
            recommended_max_level: null,
        );

        Queue::assertPushed(FindMapMonstersJob::class, function (FindMapMonstersJob $job) use ($map) {
            return $job->map->id === $map->id;
        });
    }
}
