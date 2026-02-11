<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Enemy;
use App\ValueObjects\Level;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class CreateEnemy
{
    public function __invoke(
        string $name,
        Level $level,
        int $health,
        int $difficulty,
        string $assetName,
        ?CarbonInterface $createdAt = null,
    ): void {
        DB::transaction(function () use ($name, $level, $health, $difficulty, $assetName, $createdAt) {
            Enemy::create([
                'name' => $name,
                'level' => $level,
                'health' => $health,
                'difficulty' => $difficulty,
                'asset_name' => $assetName,
                'created_at' => $createdAt,
            ]);
        });
    }
}
