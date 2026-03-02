<?php

namespace App\Jobs;

use App\Actions\Contracts\CreateMonsterContract;
use App\Models\Map;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class FindMapMonstersJob implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    public $timeout = 60;

    public $failOnTimeout = true;

    public function __construct(
        public readonly Map $map
    ) {}

    public function handle(CreateMonsterContract $createMonster): void {}
}
