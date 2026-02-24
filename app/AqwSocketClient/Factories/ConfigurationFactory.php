<?php

declare(strict_types=1);

namespace App\AqwSocketClient\Factories;

use App\AqwSocketClient\Listeners\FindMonstersListener;
use App\AqwSocketClient\Listeners\LoggerListener;
use App\AqwSocketClient\Translators\FindMonstersTranslator;
use AqwSocketClient\Configuration;
use AqwSocketClient\Interpreters\AreaInterpreter;
use AqwSocketClient\Interpreters\PlayerInterpreter;
use AqwSocketClient\Listeners\GlobalPlayerListener;

final class ConfigurationFactory
{
    public function forFindMapMonsters(string $username, string $password, string $token, string $join): Configuration
    {
        $globalPlayerListener = new GlobalPlayerListener;
        $monsterListener = new FindMonstersListener($join);

        return new Configuration(
            username: $username,
            password: $password,
            token: $token,
            translators: [new FindMonstersTranslator($globalPlayerListener, $join)],
            interpreters: [new AreaInterpreter, new PlayerInterpreter],
            listeners: [new LoggerListener, $globalPlayerListener, $monsterListener],
            logMessages: true
        );
    }
}
