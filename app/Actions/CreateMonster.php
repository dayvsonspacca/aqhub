<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\Contracts\CreateMonsterContract;
use App\Models\Monster;
use AqwSocketClient\Objects\Names\MonsterName;
use AqwSocketClient\Objects\Levels\MonsterLevel;
use AqwSocketClient\Objects\Health;
use AqwSocketClient\Objects\GameFileMetadata;

final class CreateMonster implements CreateMonsterContract
{
    public function handle(MonsterName $name, MonsterLevel $level, Health $health, GameFileMetadata $metadata): Monster
    {
        return Monster::create([
            'name' => $name,
            'level'  => $level,
            'health' => $health,
            'asset_name' => $metadata->file,
            'asset_link' => $metadata->link
        ]);
    }
}
