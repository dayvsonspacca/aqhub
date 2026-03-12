<?php

declare(strict_types=1);

namespace Tests\Unit\AqwSocketClient\Scripts;

use App\AqwSocketClient\Scripts\FindQuestScript;
use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Commands\LoadQuestCommand;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Events\QuestLoadedEvent;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Objects\Quest\Quest;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Server;
use AqwSocketClient\Sockets\FakeSocket;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FindQuestScriptTest extends TestCase
{
    private FindQuestScript $script;

    private SocketClient $client;

    private FakeSocket $socket;

    private QuestIdentifier $questId;

    private AreaIdentifier $areaId;

    protected function setUp(): void
    {
        $this->questId = new QuestIdentifier(868);
        $this->areaId = new AreaIdentifier(1);
        $this->script = new FindQuestScript($this->questId, $this->areaId);
        $this->socket = new FakeSocket;
        $this->client = new SocketClient(Server::espada(), $this->socket);
    }

    #[Test]
    public function it_expects_one_event(): void
    {
        $this->assertCount(1, $this->script->handles());
        $this->assertContains(QuestLoadedEvent::class, $this->script->handles());
    }

    #[Test]
    public function it_sends_load_quest_command_on_start(): void
    {
        $this->client->connect();

        $this->socket->queueResponse(MessageGenerator::questLoaded());
        $this->client->run($this->script);

        $expected = new LoadQuestCommand($this->areaId, $this->questId);
        $this->assertContains($expected->pack()->unpacketify(), $this->socket->sentData());
    }

    #[Test]
    public function it_succeeds_when_quest_is_loaded(): void
    {
        $this->client->connect();

        $this->socket->queueResponse(MessageGenerator::questLoaded());
        $this->client->run($this->script);

        $this->assertSame(ScriptResult::Success, $this->script->result());
        $this->assertInstanceOf(Quest::class, $this->script->quest());
    }

    #[Test]
    public function it_can_expire_when_quest_does_not_exist(): void
    {
        $this->client->connect();
        $this->script->expiresAt(new DateTimeImmutable('-10 seconds'));

        $this->client->run($this->script);

        $this->assertSame(ScriptResult::Expired, $this->script->result());
        $this->assertNull($this->script->quest());
    }

    #[Test]
    public function it_ignores_events_for_different_quest_ids(): void
    {
        $differentId = new QuestIdentifier(999);
        $script = new FindQuestScript($differentId, $this->areaId);

        $script->handle(
            QuestLoadedEvent::from(\AqwSocketClient\Messages\JsonMessage::from(MessageGenerator::questLoaded())),
            new ClientContext,
        );

        $this->assertNull($script->quest());
    }
}
