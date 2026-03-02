<?php

namespace Database\Seeders;

use App\Models\Map;
use App\Models\Monster;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Region::factory()
            ->count(50)
            ->create();

        $monsters = Monster::factory()
            ->count(2000)
            ->create();

        $maps = Map::factory()
            ->count(500)
            ->create();

        foreach ($maps as $map) {

            $randomMonsters = $monsters
                ->random(rand(5, 15))
                ->pluck('id');

            $map->monsters()->attach($randomMonsters);
        }
    }
}
