<?php

declare(strict_types=1);

namespace Tests\Unit\AqwSocketClient\Scripts;

use App\AqwSocketClient\Scripts\FindMapMonstersScript;
use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Events\AlreadyInAreaEvent;
use AqwSocketClient\Events\MonstersDetectedEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Server;
use AqwSocketClient\Sockets\FakeSocket;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class FindMapMonstersScriptTest extends TestCase
{
    private FindMapMonstersScript $script;

    private SocketClient $client;

    private FakeSocket $socket;

    protected function setUp(): void
    {
        $this->script = new FindMapMonstersScript('Hilise', 'lair');
        $this->socket = new FakeSocket;
        $this->client = new SocketClient(Server::espada(), $this->socket);
    }

    #[Test]
    public function it_creates_the_script(): void
    {
        $this->assertInstanceOf(FindMapMonstersScript::class, $this->script);
        $this->assertContains(PlayerInventoryLoadedEvent::class, $this->script->handles());
        $this->assertContains(MonstersDetectedEvent::class, $this->script->handles());
        $this->assertContains(AlreadyInAreaEvent::class, $this->script->handles());
    }

    #[Test]
    public function it_can_run_the_script(): void
    {
        $this->client->connect();
        $this->socket->queueResponse(MessageGenerator::loadInventory());
        $this->socket->queueResponse(MessageGenerator::monstersDetected());

        $this->client->run($this->script);

        $this->assertTrue($this->script->isDone());

        $this->client->disconnect();

        $this->assertCount(1, $this->script->monsters());
    }

    #[Test]
    public function it_fail_when_player_alreay_in_area(): void
    {
        $this->expectException(RuntimeException::class);

        $this->client->connect();
        $this->socket->queueResponse(MessageGenerator::alreadyInArea());

        $this->client->run($this->script);
    }
}
