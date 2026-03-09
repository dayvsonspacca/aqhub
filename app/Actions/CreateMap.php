<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\Contracts\CreateMapContract;
use App\Jobs\FindMapMonstersJob;
use App\Models\Map;
use AqwSocketClient\Objects\Names\AreaName;

final class CreateMap implements CreateMapContract
{
    public function handle(
        int $aqwId,
        string $name,
        AreaName $join_name,
        ?string $description,
        bool $upgrade_only,
        ?int $recommended_min_level,
        ?int $recommended_max_level
    ): Map {
        $map = Map::firstOrCreate(
            ['aqw_id' => $aqwId],
            [
                'name' => $name,
                'join_name' => $join_name,
                'description' => $description,
                'upgrade_only' => $upgrade_only,
                'recommended_min_level' => $recommended_min_level,
                'recommended_max_level' => $recommended_max_level,
            ]
        );

        FindMapMonstersJob::dispatch($map);

        return $map;
    }
}
