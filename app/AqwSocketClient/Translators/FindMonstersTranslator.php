<?php

declare(strict_types=1);

namespace App\AqwSocketClient\Translators;

use AqwSocketClient\Commands\{LoadPlayerInventoryCommand, JoinMapCommand, LogoutCommand};
use AqwSocketClient\Events\{PlayerInventoryLoadedEvent, AreaJoinedEvent};
use AqwSocketClient\Interfaces\{EventInterface, CommandInterface, TranslatorInterface};
use AqwSocketClient\Listeners\GlobalPlayerListener;

final class FindMonstersTranslator implements TranslatorInterface
{
    public function __construct(
        private GlobalPlayerListener $globalPlayerListener,
        private string $join_name
    ) {}

    public function translate(EventInterface $event): ?CommandInterface
    {
        return match ($event::class) {
            AreaJoinedEvent::class => match ($event->mapName) {
                'battleon' => new LoadPlayerInventoryCommand($this->globalPlayerListener->areaId, $this->globalPlayerListener->socketId),
                $this->join_name => new LogoutCommand,
                default => null,
            },
            PlayerInventoryLoadedEvent::class => new JoinMapCommand(config('services.aqw.username'), $this->join_name, 99999),
            default => null,
        };
    }
}
