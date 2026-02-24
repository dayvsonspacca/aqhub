<?php

namespace Tests\Feature\Filament\Resources\Maps\Pages;

use App\Filament\Resources\Maps\Pages\ListMaps;
use App\Jobs\FindMapMonsters;
use App\Models\Map;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ListMapsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_load_the_page(): void
    {
        Livewire::test(ListMaps::class)->assertOk();
    }

    #[Test]
    public function it_displays_map_table(): void
    {
        $maps = Map::factory()->count(4)->create();

        Livewire::test(ListMaps::class)
            ->assertCanSeeTableRecords($maps);
    }

    #[Test]
    public function it_can_search_maps_by_name(): void
    {
        $maps = Map::factory()->count(2)->create();

        Livewire::test(ListMaps::class)
            ->assertCanSeeTableRecords($maps)
            ->searchTable($maps->first()->name)
            ->assertCanSeeTableRecords($maps->take(1))
            ->assertCanNotSeeTableRecords($maps->skip(1));
    }

    #[Test]
    public function it_can_search_maps_by_join_name(): void
    {
        $maps = Map::factory()->count(2)->create();

        Livewire::test(ListMaps::class)
            ->assertCanSeeTableRecords($maps)
            ->searchTable($maps->first()->join_name)
            ->assertCanSeeTableRecords($maps->take(1))
            ->assertCanNotSeeTableRecords($maps->skip(1));
    }

    #[Test]
    public function it_have_find_monsters_action(): void
    {
        $map = Map::factory()->create();

        $findMonstersAction = TestAction::make('find_map_monsters')->table($map);

        Livewire::test(ListMaps::class)
            ->assertActionExists($findMonstersAction);
    }

    #[Test]
    public function should_call_a_find_monsters_job(): void
    {
        Queue::fake();

        $map = Map::factory()->create();

        $findMonstersAction = TestAction::make('find_map_monsters')->table($map);

        Livewire::test(ListMaps::class)
            ->callAction($findMonstersAction)
            ->assertNotified('The map monsters are being found. Please check the logs for more details.');

        Queue::assertPushed(FindMapMonsters::class, function (FindMapMonsters $job) use ($map) {
            return $job->map->is($map);
        });
    }
}
