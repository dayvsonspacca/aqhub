<?php

namespace App\Jobs;

use App\Actions\Contracts\CreateMonsterContract;
use App\AqwSocketClient\Scripts\FindMapMonstersScript;
use App\Models\Map;
use App\Services\Http\AqwGameApiService;
use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Scripts\LoginScript;
use DateTimeImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FindMapMonstersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    public int $tries = 3;

    public function __construct(
        public readonly Map $map
    ) {}

    public function handle(AqwGameApiService $api, CreateMonsterContract $createMonster, SocketClient $client): void
    {

        $player = new PlayerName(config('services.aqw.username'));
        $password = config('services.aqw.password');

        Log::info('Starting monster sync', [
            'map_id' => $this->map->id,
            'map' => $this->map->join_name->value,
        ]);

        $login = new LoginScript($player, $api->token($player, $password));

        $login->expiresAt(new DateTimeImmutable('+15 seconds'));

        $client->connect();
        $client->run($login);

        if ($login->result() !== ScriptResult::Success) {
            throw new RuntimeException('Login script failed.');
        }

        $script = new FindMapMonstersScript($player, $this->map->join_name);
        $script->expiresAt(new DateTimeImmutable('+15 seconds'));

        $client->run($script);

        match ($script->result()) {
            ScriptResult::Disconnected => Log::error('FindMapMonstersScript disconnected'),
            ScriptResult::Expired => Log::error('FindMapMonstersScript expired'),
            ScriptResult::Failed => Log::error('FindMapMonstersScript failed: ' . $script->failedBy::class),
            default => null
        };

        if ($script->result() !== ScriptResult::Success) {
            throw new RuntimeException('FindMapMonstersScript failed, check logs.');
        }

        foreach ($script->monsters() as $monster) {

            $model = $createMonster->handle(
                $monster->name,
                $monster->level,
                $monster->health,
                $monster->metadata
            );

            $this->map->monsters()->syncWithoutDetaching([
                $model->id ?? null,
            ]);

            Log::debug('Monster synced', [
                'name' => $monster->name->value,
                'level' => $monster->level->value,
            ]);
        }

        $client->disconnect();

        Log::info('Monster sync finished', [
            'map_id' => $this->map->id,
            'monsters_found' => count($script->monsters()),
        ]);
    }
}
