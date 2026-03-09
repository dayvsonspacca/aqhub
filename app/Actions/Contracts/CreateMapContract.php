<?php

namespace App\Actions\Contracts;

use App\Models\Map;
use AqwSocketClient\Objects\Names\AreaName;

interface CreateMapContract
{
    public function handle(
        int $aqwId,
        string $name,
        AreaName $join_name,
        ?string $description,
        bool $upgrade_only,
        ?int $recommended_min_level,
        ?int $recommended_max_level
    ): Map;
}
