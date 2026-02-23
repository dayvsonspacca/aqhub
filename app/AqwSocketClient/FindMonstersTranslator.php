<?php

declare(strict_types=1);

namespace App\AqwSocketClient;

use AqwSocketClient\Commands\JoinMapCommand;
use AqwSocketClient\Events\JoinedAreaEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\TranslatorInterface;
use RuntimeException;

final class FindMonstersTranslator implements TranslatorInterface
{
    public function __construct(
        private string $join_name
    ) {
    }

    public function translate(EventInterface $event): CommandInterface|false
    {
        dump($event);

        if ($event instanceof JoinedAreaEvent && $event->mapName === 'battleon') {
            return new JoinMapCommand(env('AQW_USERNAME'), $this->join_name, 99999);
        }

        if ($event instanceof JoinedAreaEvent && $event->mapName === $this->join_name) {
            throw new RuntimeException("Successfully joined map: {$this->join_name}");
        }

        return false;
    }
}