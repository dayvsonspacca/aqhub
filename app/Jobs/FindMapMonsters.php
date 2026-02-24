<?php

namespace App\Jobs;

use App\Actions\CreateMonster;
use App\AqwSocketClient\Listeners\FindMonstersListener;
use App\AqwSocketClient\Listeners\LoggerListener;
use App\AqwSocketClient\Translators\FindMonstersTranslator;
use App\Models\Map;
use App\ValueObjects\Level;
use AqwSocketClient\Client;
use AqwSocketClient\Configuration;
use AqwSocketClient\Interpreters\AreaInterpreter;
use AqwSocketClient\Interpreters\PlayerInterpreter;
use AqwSocketClient\Listeners\GlobalPlayerListener;
use AqwSocketClient\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use React\EventLoop\Loop;
use RuntimeException;

final class FindMapMonsters implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    public $timeout = 60;

    public $failOnTimeout = true;

    public function __construct(
        public readonly Map $map
    ) {}

    public function handle(CreateMonster $createMonster): void
    {
        $join = $this->map->join_name;

        $username = config('services.aqw.username');
        $password = config('services.aqw.password');

        $token = $this->getAuthToken($username, $password);

        $globalPlayerListener = new GlobalPlayerListener;
        $monsterListener = new FindMonstersListener($join);

        $configuration = new Configuration(
            username: $username,
            password: $password,
            token: $token,
            translators: [new FindMonstersTranslator($globalPlayerListener, $join)],
            interpreters: [new AreaInterpreter, new PlayerInterpreter],
            listeners: [new LoggerListener, $globalPlayerListener, $monsterListener],
            logMessages: true
        );

        $aqwClient = new Client(
            Server::espada(),
            $configuration
        );

        $aqwClient->connect();

        Loop::run();

        $monsters = $monsterListener->targetMapJoinedEvent->monsters;

        foreach ($monsters as $monster) {
            $createMonster($monster['name'], Level::from($monster['level']), $monster['hp'], $monster['asset_name']);
        }
    }

    private function getAuthToken(string $username, string $password): string
    {
        $response = Http::post('https://game.aq.com/game/api/login/now', [
            'user' => $username,
            'pass' => $password,
            'option' => 1,
        ]);

        $token = $response->json('login.sToken');

        throw_unless($token, RuntimeException::class, "Failed to retrieve account auth token for user: {$username}");

        return $token;
    }
}
