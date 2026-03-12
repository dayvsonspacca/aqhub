<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\FindMapMonstersJob;
use App\Models\Map;
use App\Services\Http\AqwGameApiService;
use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Helpers\MessageGenerator;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\SocketIdentifier;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Server;
use AqwSocketClient\Sockets\FakeSocket;
use Illuminate\Contracts\Queue\ShouldQueue as ShouldQueueContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class FindMapMonstersJobTest extends TestCase
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
    public function it_implements_should_queue(): void
    {
        $map = Map::factory()->create();

        $this->assertInstanceOf(ShouldQueueContract::class, new FindMapMonstersJob($map));
    }

    #[Test]
    public function it_has_correct_timeout_and_tries(): void
    {
        $map = Map::factory()->create();
        $job = new FindMapMonstersJob($map);

        $this->assertSame(60, $job->timeout);
        $this->assertSame(3, $job->tries);
    }

    #[Test]
    public function it_is_dispatched_with_the_correct_map(): void
    {
        Queue::fake();

        $map = Map::factory()->create();

        FindMapMonstersJob::dispatch($map);

        Queue::assertPushed(FindMapMonstersJob::class, function (FindMapMonstersJob $job) use ($map) {
            return $job->map->id === $map->id;
        });
    }

    #[Test]
    public function it_is_not_dispatched_when_not_called(): void
    {
        Queue::fake();

        Queue::assertNotPushed(FindMapMonstersJob::class);
    }

    #[Test]
    public function it_saves_monsters_and_associates_them_with_the_map(): void
    {
        $socket = $this->fakeSocket();
        $this->bindFakeClient($socket);
        $this->fakeToken();

        $this->queueLoginResponses($socket);
        $socket->queueResponse(MessageGenerator::monstersDetected());

        $map = Map::factory()->create();

        app()->call([new FindMapMonstersJob($map), 'handle']);

        $this->assertDatabaseCount('monsters', 1);
        $this->assertSame(1, $map->monsters()->count());
    }

    #[Test]
    public function it_throws_when_login_script_fails(): void
    {
        $socket = $this->fakeSocket();
        $this->bindFakeClient($socket);
        $this->fakeToken();

        $socket
            ->queueResponse(MessageGenerator::domainPolicy())
            ->queueResponse('%xt%loginResponse%-1%0%');

        $map = Map::factory()->create();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Login script failed.');

        app()->call([new FindMapMonstersJob($map), 'handle']);
    }

    #[Test]
    public function it_throws_when_find_map_monsters_script_fails(): void
    {
        $socket = $this->fakeSocket();
        $this->bindFakeClient($socket);
        $this->fakeToken();

        $this->queueLoginResponses($socket);
        $socket->queueResponse(MessageGenerator::areaLocked());

        $map = Map::factory()->create();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('FindMapMonstersScript failed, check logs.');

        app()->call([new FindMapMonstersJob($map), 'handle']);
    }
}
