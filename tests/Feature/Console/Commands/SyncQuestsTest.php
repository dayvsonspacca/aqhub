<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Services\Http\AqwGameApiService;
use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Server;
use AqwSocketClient\Sockets\FakeSocket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SyncQuestsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.aqw.username' => 'TestPlayer',
            'services.aqw.password' => md5('test'),
        ]);
    }

    private function fakeSocket(): FakeSocket
    {
        return new FakeSocket;
    }

    private function bindFakeClient(FakeSocket $socket): void
    {
        $this->app->bind(SocketClient::class, fn () => new SocketClient(Server::espada(), $socket));
    }

    private function fakeToken(): void
    {
        Http::fake([
            AqwGameApiService::URL . '/game/api/login/now' => Http::response([
                'login' => ['sToken' => 'valid-token'],
            ]),
        ]);
    }

    private function queueLoginResponses(FakeSocket $socket): void
    {
        $player = new PlayerName(config('services.aqw.username'));

        $socket
            ->queueResponse(MessageGenerator::domainPolicy())
            ->queueResponse(MessageGenerator::loginReponded($player, new SocketIdentifier(123)))
            ->queueResponse(MessageGenerator::moveToArea(new AreaName('battleon'), new AreaIdentifier(1)))
            ->queueResponse(MessageGenerator::loadInventory());
    }

    #[Test]
    public function it_persists_quests_found_in_range(): void
    {
        $socket = $this->fakeSocket();
        $this->bindFakeClient($socket);
        $this->fakeToken();

        $this->queueLoginResponses($socket);
        $socket->queueResponse(MessageGenerator::questLoaded()); // quest ID 868 found

        $this->artisan('aqw:sync-quests', ['from' => 868, 'to' => 868])
            ->assertSuccessful();

        $this->assertDatabaseHas('quests', ['aqw_id' => 868]);
    }

    #[Test]
    public function it_skips_ids_with_no_response(): void
    {
        $socket = $this->fakeSocket();
        $this->bindFakeClient($socket);
        $this->fakeToken();

        $this->queueLoginResponses($socket);
        // No quest response queued — script will expire

        $this->artisan('aqw:sync-quests', ['from' => 1, 'to' => 1, '--timeout' => 1])
            ->assertSuccessful();

        $this->assertDatabaseCount('quests', 0);
    }

    #[Test]
    public function it_does_not_duplicate_quests_on_repeat_runs(): void
    {
        $socket = $this->fakeSocket();
        $this->bindFakeClient($socket);
        $this->fakeToken();

        $this->queueLoginResponses($socket);
        $socket->queueResponse(MessageGenerator::questLoaded());

        $this->artisan('aqw:sync-quests', ['from' => 868, 'to' => 868])->assertSuccessful();

        // Rebind a fresh socket for the second run
        $socket2 = $this->fakeSocket();
        $this->bindFakeClient($socket2);
        $this->queueLoginResponses($socket2);
        $socket2->queueResponse(MessageGenerator::questLoaded());

        $this->artisan('aqw:sync-quests', ['from' => 868, 'to' => 868])->assertSuccessful();

        $this->assertDatabaseCount('quests', 1);
    }

    #[Test]
    public function it_returns_failure_when_login_fails(): void
    {
        $socket = $this->fakeSocket();
        $this->bindFakeClient($socket);
        $this->fakeToken();

        $socket
            ->queueResponse(MessageGenerator::domainPolicy())
            ->queueResponse('%xt%loginResponse%-1%0%');

        $this->artisan('aqw:sync-quests', ['from' => 1, 'to' => 10])
            ->assertFailed();
    }
}
