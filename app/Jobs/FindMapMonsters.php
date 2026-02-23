<?php

namespace App\Jobs;

use App\AqwSocketClient\FindMonstersTranslator;
use App\Models\Map;
use AqwSocketClient\Client;
use AqwSocketClient\Configuration;
use AqwSocketClient\Interpreters\PlayerRelatedInterpreter;
use AqwSocketClient\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class FindMapMonsters implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Map $map
    ) {}

    public function handle(): void
    {
        $join = $this->map->join_name;

        $username = env('AQW_USERNAME');
        $password = env('AQW_PASSWORD');

        $token = $this->getAuthToken($username, $password);

        $aqwClient = new Client(
            Server::espada(),
            new Configuration(
                username: $username,
                password: $password,
                token: $token,
                translators: [new FindMonstersTranslator($join)],
                interpreters: [new PlayerRelatedInterpreter],
                logMessages: true
            )
        );

        $aqwClient->connect();
    }

    private function getAuthToken(string $username, string $password): string
    {
        $response = Http::post('https://game.aq.com/game/api/login/now', [
            'user'   => $username,
            'pass'   => $password,
            'option' => 1,
        ]);

        $token = $response->json('login.sToken');

        throw_unless($token, RuntimeException::class, "Failed to retrieve account auth token for user: {$username}");

        return $token;
    }
}
