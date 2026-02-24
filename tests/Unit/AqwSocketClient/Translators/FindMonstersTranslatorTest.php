<?php

declare(strict_types=1);

namespace Tests\Unit\AqwSocketClient\Translators;

use App\AqwSocketClient\Translators\FindMonstersTranslator;
use AqwSocketClient\Commands\JoinMapCommand;
use AqwSocketClient\Commands\LoadPlayerInventoryCommand;
use AqwSocketClient\Commands\LogoutCommand;
use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Listeners\GlobalPlayerListener;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FindMonstersTranslatorTest extends TestCase
{
    private GlobalPlayerListener $globalPlayerListener;

    private FindMonstersTranslator $translator;

    private string $joinName = 'lair';

    protected function setUp(): void
    {
        parent::setUp();

        $this->globalPlayerListener = new GlobalPlayerListener;
        $this->globalPlayerListener->areaId = 99999;
        $this->globalPlayerListener->socketId = 100;

        $this->translator = new FindMonstersTranslator(
            globalPlayerListener: $this->globalPlayerListener,
            join_name: $this->joinName,
        );
    }

    #[Test]
    public function it_returns_load_player_inventory_command_when_joined_battleon(): void
    {
        $event = $this->makeAreaJoinedEvent('battleon');

        $command = $this->translator->translate($event);

        $this->assertInstanceOf(LoadPlayerInventoryCommand::class, $command);
    }

    #[Test]
    public function load_player_inventory_command_uses_listener_area_id_and_socket_id(): void
    {
        $this->globalPlayerListener->areaId = 10000;
        $this->globalPlayerListener->socketId = 1001;

        $event = $this->makeAreaJoinedEvent('battleon');

        $command = $this->translator->translate($event);

        $this->assertInstanceOf(LoadPlayerInventoryCommand::class, $command);
        $this->assertSame(10000, $command->areaId);
        $this->assertSame(1001, $command->socketId);
    }

    #[Test]
    public function it_returns_logout_command_when_joined_the_target_map(): void
    {
        $event = $this->makeAreaJoinedEvent($this->joinName);

        $command = $this->translator->translate($event);

        $this->assertInstanceOf(LogoutCommand::class, $command);
    }

    #[Test]
    public function it_returns_null_when_joined_an_unrelated_map(): void
    {
        $event = $this->makeAreaJoinedEvent('some-other-map');

        $command = $this->translator->translate($event);

        $this->assertNull($command);
    }

    #[Test]
    public function it_returns_null_when_joined_map_name_is_empty_string(): void
    {
        $event = $this->makeAreaJoinedEvent('');

        $command = $this->translator->translate($event);

        $this->assertNull($command);
    }

    #[Test]
    public function it_returns_join_map_command_when_player_inventory_is_loaded(): void
    {
        config(['services.aqw.username' => 'test-user']);

        $event = new PlayerInventoryLoadedEvent([]);

        $command = $this->translator->translate($event);

        $this->assertInstanceOf(JoinMapCommand::class, $command);
    }

    #[Test]
    public function join_map_command_uses_configured_username_and_join_name(): void
    {
        config(['services.aqw.username' => 'hero123']);

        $event = new PlayerInventoryLoadedEvent([]);

        /** @var JoinMapCommand $command */
        $command = $this->translator->translate($event);

        $this->assertInstanceOf(JoinMapCommand::class, $command);
        $this->assertSame('hero123', $command->username);
        $this->assertSame($this->joinName, $command->mapName);
        $this->assertSame(99999, $command->room);
    }

    #[Test]
    public function it_returns_null_for_unknown_event_types(): void
    {
        $unknownEvent = new class implements EventInterface {};

        $command = $this->translator->translate($unknownEvent);

        $this->assertNull($command);
    }

    #[Test]
    public function battleon_takes_priority_when_join_name_is_also_battleon(): void
    {
        $translator = new FindMonstersTranslator(
            globalPlayerListener: $this->globalPlayerListener,
            join_name: 'battleon',
        );

        $event = $this->makeAreaJoinedEvent('battleon');

        $command = $translator->translate($event);

        $this->assertInstanceOf(LoadPlayerInventoryCommand::class, $command);
    }

    private function makeAreaJoinedEvent(string $mapName): AreaJoinedEvent
    {
        $event = new AreaJoinedEvent($mapName, 0, 0, [], []);

        return $event;
    }
}
