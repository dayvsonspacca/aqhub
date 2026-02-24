<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Monster;
use App\ValueObjects\Level;
use Illuminate\Support\Facades\DB;

final class CreateMonster
{
    public function __invoke(
        string $name,
        Level $level,
        int $health,
        string $assetName
    ): void {
        DB::transaction(function () use ($name, $level, $health, $assetName) {
            Monster::create([
                'name' => $name,
                'level' => $level,
                'health' => $health,
                'asset_name' => $assetName,
            ]);
        });
    }
}
