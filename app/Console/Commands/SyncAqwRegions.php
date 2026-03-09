<?php

namespace App\Console\Commands;

use App\Actions\Contracts\CreateMapContract;
use App\Models\Region;
use App\Services\Http\AqwGameApiService;
use AqwSocketClient\Objects\Names\AreaName;
use Illuminate\Console\Command;

class SyncAqwRegions extends Command
{
    protected $signature = 'aqw:sync-regions';

    protected $description = 'Uses AQW travelmap API to sync and register new regions and maps.';

    public function handle(AqwGameApiService $api, CreateMapContract $createMap)
    {
        $regions = $api->travelMap('R0039');

        foreach ($regions as $region) {
            /** @var Region $regionModel */
            $regionModel = Region::firstOrCreate([
                'name' => $region['regionName'],
                'aqw_id' => $region['regionID'],
            ]);

            foreach ($region['maps'] ?? [] as $map) {
                $map = $createMap->handle(
                    (int) $map['mapID'],
                    $map['mapTitle'],
                    new AreaName($map['mapName']),
                    $map['description'],
                    (bool) $map['bUpgrade'],
                    (int) $map['minLevel'],
                    (int) $map['maxLevel'],
                );

                $regionModel->maps()->save($map);
            }
        }
    }
}
