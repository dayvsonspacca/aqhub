<?php

namespace App\Jobs;

use App\Actions\Contracts\CreateMonsterContract;
use App\AqwSocketClient\Scripts\FindMapMonstersScript;
use App\Models\Map;
use App\Services\HttpAqwAuthService;
use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Scripts\LoginScript;
use AqwSocketClient\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class FindMapMonstersJob implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    public $timeout = 60;

    public $failOnTimeout = true;

    public function __construct(
        public readonly Map $map
    ) {}

    public function handle(CreateMonsterContract $createMonster): void
    {
        $username = config('services.aqw.username');
        $password = config('services.aqw.password');

        $token = HttpAqwAuthService::getToken($username, $password);

        $script = new FindMapMonstersScript($username, $this->map->join_name);

        $client = new SocketClient(Server::espada());

        $client->connect();

        $client->run(new LoginScript($username, $token));
        $client->run($script);

        $client->disconnect();

        $monsters = $script->monsters();

        foreach ($monsters as $monster) {
            $model = $createMonster->handle($monster->name, $monster->level, $monster->health, $monster->metadata);
            $this->map->monsters()->attach($model);
        }
    }
}
