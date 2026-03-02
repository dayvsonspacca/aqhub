<?php

declare(strict_types=1);

namespace Tests\Unit\AqwSocketClient\Scripts;

use App\AqwSocketClient\Scripts\FindMapMonstersScript;
use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Commands\JoinAreaCommand;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\AlreadyInAreaEvent;
use AqwSocketClient\Events\AreaLockedEvent;
use AqwSocketClient\Events\AreaMemberOnlyEvent;
use AqwSocketClient\Events\AreaNotAvaliableEvent;
use AqwSocketClient\Events\MonstersDetectedEvent;
use AqwSocketClient\Events\PlayerDetectedEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Monster;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Server;
use AqwSocketClient\Sockets\FakeSocket;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FindMapMonstersScriptTest extends TestCase
{
    private FindMapMonstersScript $script;

    private SocketClient $client;

    private FakeSocket $socket;

    private PlayerName $player;

    private AreaName $area;

    protected function setUp(): void
    {
        $this->player = new PlayerName('Hilise');
        $this->area = new AreaName('lair');
        $this->script = new FindMapMonstersScript($this->player, $this->area);
        $this->socket = new FakeSocket;
        $this->client = new SocketClient(Server::espada(), $this->socket);
    }

    #[Test]
    public function it_marks_as_failed_when_cant_join_area_related_events(): void
    {
        $this->client->connect();

        $this->socket->queueResponse(MessageGenerator::loadInventory());
        $this->socket->queueResponse(MessageGenerator::areaLocked());
        $this->client->run($this->script);

        $this->assertSame(ScriptResult::Failed, $this->script->result());
    }

    #[Test]
    public function it_expects_6_events(): void
    {
        $this->assertCount(6, $this->script->handles());

        $this->assertContains(PlayerInventoryLoadedEvent::class, $this->script->handles());
        $this->assertContains(MonstersDetectedEvent::class, $this->script->handles());
        $this->assertContains(AreaLockedEvent::class, $this->script->handles());
        $this->assertContains(AreaMemberOnlyEvent::class, $this->script->handles());
        $this->assertContains(AreaNotAvaliableEvent::class, $this->script->handles());
        $this->assertContains(AlreadyInAreaEvent::class, $this->script->handles());
    }

    #[Test]
    public function it_send_join_area_command_on_player_inventory_load(): void
    {
        $this->client->connect();

        $this->socket->queueResponse(MessageGenerator::loadInventory());
        $this->socket->queueResponse(MessageGenerator::monstersDetected());
        $this->client->run($this->script);

        $this->assertContains(new JoinAreaCommand($this->player, $this->area, new RoomIdentifier(55555))->pack()->unpacketify(), $this->socket->sentData());
        $this->assertSame($this->script->result(), ScriptResult::Success);
    }

    #[Test]
    public function it_returns_monsters_on_completion(): void
    {
        $this->client->connect();

        $this->socket->queueResponse(MessageGenerator::loadInventory());
        $this->socket->queueResponse(MessageGenerator::monstersDetected());
        $this->client->run($this->script);

        $monster = $this->script->monsters()[0];

        $this->assertInstanceOf(Monster::class, $monster);
        $this->assertSame($this->script->result(), ScriptResult::Success);
    }

    #[Test]
    public function it_can_expire(): void
    {
        $this->client->connect();
        $this->script->expiresAt(new DateTimeImmutable('-10 seconds'));

        $this->client->run($this->script);

        $this->assertSame($this->script->result(), ScriptResult::Expired);
    }

    #[Test]
    public function it_defaults_to_non_commands_when_no_related_event(): void
    {
        $this->assertEmpty($this->script->handle(new PlayerDetectedEvent($this->player)));
    }
}
