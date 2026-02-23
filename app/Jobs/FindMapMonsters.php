<?php

namespace App\Jobs;

use App\Models\Map;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class FindMapMonsters implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Map $map
    ) {
    }

    public function handle(): void
    {
        $join = $this->map->join_name;
    }
}
