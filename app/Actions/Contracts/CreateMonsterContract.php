<?php

namespace App\Actions\Contracts;

use App\Models\Monster;
use AqwSocketClient\Objects\GameFileMetadata;
use AqwSocketClient\Objects\Levels\MonsterLevel;
use AqwSocketClient\Objects\Monster\Health;
use AqwSocketClient\Objects\Names\MonsterName;

interface CreateMonsterContract
{
    public function handle(MonsterName $name, MonsterLevel $level, Health $health, GameFileMetadata $metadata): Monster;
}
