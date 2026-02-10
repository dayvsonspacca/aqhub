<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Enemy;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class CreateEnemy
{
    public function __invoke(
        string $name,
        int $level,
        int $health,
        int $difficulty,
        ?CarbonInterface $createdAt = null,
    ): void {
        DB::transaction(function () use ($name, $level, $health, $difficulty, $createdAt) {
            Enemy::create([
                'name' => $name,
                'level' => $level,
                'health' => $health,
                'difficulty' => $difficulty,
                'created_at' => $createdAt,
            ]);
        });
    }
}
