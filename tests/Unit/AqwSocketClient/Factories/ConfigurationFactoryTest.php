<?php

declare(strict_types=1);

namespace Tests\Unit\AqwSocketClient\Factories;

use App\AqwSocketClient\Factories\ConfigurationFactory;
use App\AqwSocketClient\Listeners\FindMonstersListener;
use App\AqwSocketClient\Listeners\LoggerListener;
use App\AqwSocketClient\Translators\FindMonstersTranslator;
use AqwSocketClient\Interpreters\AreaInterpreter;
use AqwSocketClient\Interpreters\PlayerInterpreter;
use AqwSocketClient\Listeners\GlobalPlayerListener;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ConfigurationFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_configuration_for_find_map_monsters(): void
    {
        $username = 'testuser';
        $password = 'testpass';
        $token = 'testtoken';
        $join = 'battleon';

        $configuration = new ConfigurationFactory()->forFindMapMonsters($username, $password, $token, $join);

        $this->assertEquals($username, $configuration->username);
        $this->assertEquals($password, $configuration->password);
        $this->assertEquals($token, $configuration->token);
        $this->assertTrue($configuration->logMessages);

        $this->assertContains(FindMonstersTranslator::class, array_map(fn ($translator) => get_class($translator), $configuration->translators));
        $this->assertContains(AreaInterpreter::class, array_map(fn ($interpreter) => get_class($interpreter), $configuration->interpreters));
        $this->assertContains(PlayerInterpreter::class, array_map(fn ($interpreter) => get_class($interpreter), $configuration->interpreters));
        $this->assertContains(LoggerListener::class, array_map(fn ($listener) => get_class($listener), $configuration->listeners));
        $this->assertContains(GlobalPlayerListener::class, array_map(fn ($listener) => get_class($listener), $configuration->listeners));
        $this->assertContains(FindMonstersListener::class, array_map(fn ($listener) => get_class($listener), $configuration->listeners));
    }
}
