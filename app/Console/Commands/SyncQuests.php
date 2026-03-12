<?php

namespace App\Console\Commands;

use App\Actions\Contracts\UpsertQuestContract;
use App\AqwSocketClient\Scripts\FindQuestScript;
use App\Services\Http\AqwGameApiService;
use AqwSocketClient\Clients\SocketClient;
use AqwSocketClient\Enums\ScriptResult;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Scripts\LoginScript;
use DateTimeImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class SyncQuests extends Command
{
    protected $signature = 'aqw:sync-quests
                            {from : First quest ID to scan}
                            {to : Last quest ID to scan}
                            {--timeout=3 : Seconds to wait for each quest response}';

    protected $description = 'Brute-forces a range of quest IDs against the AQW server to discover and persist quests.';

    public function handle(AqwGameApiService $api, SocketClient $client, UpsertQuestContract $upsertQuest): int
    {
        $from = (int) $this->argument('from');
        $to = (int) $this->argument('to');
        $timeout = (int) $this->option('timeout');

        $player = new PlayerName(config('services.aqw.username'));
        $token = $api->token($player, config('services.aqw.password'));

        $login = new LoginScript($player, $token);

        $client->connect();
        $client->run($login);

        if ($login->result() !== ScriptResult::Success) {
            $this->error('Login failed.');
            $client->disconnect();

            return Command::FAILURE;
        }

        /** @var AreaIdentifier $areaId */
        $areaId = $client->context()->get('area_id');

        $total = $to - $from + 1;
        $found = 0;
        $skipped = 0;
        $errors = 0;

        $this->info("Scanning quest IDs {$from}–{$to} ({$total} total)...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        for ($id = $from; $id <= $to; $id++) {
            $disconnected = false;

            try {
                $script = new FindQuestScript(new QuestIdentifier($id), $areaId);
                $script->expiresAt(new DateTimeImmutable("+{$timeout} seconds"));

                $client->run($script);

                if ($script->result() === ScriptResult::Disconnected) {
                    $disconnected = true;
                } elseif ($script->result() === ScriptResult::Success && $script->quest() !== null) {
                    $upsertQuest->handle($script->quest());
                    $found++;
                } else {
                    $skipped++;
                }
            } catch (Throwable $e) {
                $errors++;
                Log::channel('sync-quests')->error("Quest ID {$id} failed.", [
                    'quest_id' => $id,
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            if ($disconnected) {
                $bar->finish();
                $this->newLine();
                throw new RuntimeException('Disconnected from server at quest ID ' . $id . '.');
            }

            $bar->advance();
        }

        $bar->finish();
        $client->disconnect();

        $this->newLine();
        $this->info("Done. Found: {$found} | Skipped: {$skipped} | Errors: {$errors}");

        if ($errors > 0) {
            $this->warn('Errors logged to storage/logs/sync-quests.log');
        }

        return Command::SUCCESS;
    }
}
