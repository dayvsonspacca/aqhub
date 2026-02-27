<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\Contracts\CreateMonsterContract;
use App\Models\Monster;
use App\ValueObjects\Level;

final class CreateMonster implements CreateMonsterContract
{
    public function __invoke(
        string $name,
        Level $level,
        int $health,
        string $assetName,
        string $assetLink
    ): void {
        Monster::create([
            'name' => $name,
            'level' => $level,
            'health' => $health,
            'asset_name' => $assetName,
            'asset_link' => $assetLink,
        ]);
    }
}
