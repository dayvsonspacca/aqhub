<?php

namespace App\Jobs;

use App\Actions\Contracts\CreateMonsterContract;
use App\AqwSocketClient\Factories\ConfigurationFactory;
use App\AqwSocketClient\Interfaces\AqwAuthServiceInterface;
use App\AqwSocketClient\Listeners\FindMonstersListener;
use App\Models\Map;
use App\ValueObjects\Level;
use AqwSocketClient\Client;
use AqwSocketClient\Interfaces\ListenerInterface;
use AqwSocketClient\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use React\EventLoop\Loop;

final class FindMapMonstersJob implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    public $timeout = 60;

    public $failOnTimeout = true;

    public function __construct(
        public readonly Map $map
    ) {}

    public function handle(
        CreateMonsterContract $createMonster,
        AqwAuthServiceInterface $authService,
        ConfigurationFactory $configurationFactory
    ): void {
        $join = $this->map->join_name;

        $username = config('services.aqw.username');
        $password = config('services.aqw.password');

        $token = $authService->getToken($username, $password);

        $configuration = $configurationFactory->forFindMapMonsters($username, $password, $token, $join);

        $client = new Client(Server::espada(), $configuration);
        $client->connect();

        Loop::run();

        /** @var FindMonstersListener $monsterListener */
        $monsterListener = collect($configuration->listeners)->firstWhere(fn (ListenerInterface $listener) => $listener instanceof FindMonstersListener);

        foreach ($monsterListener->targetMapJoinedEvent->monsters as $monster) {
            $createMonster($monster['name'], Level::from($monster['level']), $monster['hp'], $monster['asset_name']);
        }
    }
}
