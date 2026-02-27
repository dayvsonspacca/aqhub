<?php

declare(strict_types=1);

namespace App\AqwSocketClient\Scripts;

use AqwSocketClient\Commands\JoinMapCommand;
use AqwSocketClient\Commands\LogoutCommand;
use AqwSocketClient\Events\AlreadyInAreaEvent;
use AqwSocketClient\Events\MonstersDetectedEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Objects\Monster;
use AqwSocketClient\Scripts\AbstractScript;
use RuntimeException;

class FindMapMonstersScript extends AbstractScript
{
    /**
     * @var Monster[]
     */
    private array $monsters = [];

    public function __construct(
        private string $username,
        private string $mapName
    ) {}

    public function handles(): array
    {
        return [
            PlayerInventoryLoadedEvent::class, // STARTUP SCRIPT
            MonstersDetectedEvent::class,
            AlreadyInAreaEvent::class,
        ];
    }

    public function handle(EventInterface $event): array
    {
        if ($event instanceof AlreadyInAreaEvent) {
            throw new RuntimeException('The player is already in map: ' . $this->mapName);
        }

        if ($event instanceof MonstersDetectedEvent) {
            $this->done();
            $this->monsters = $event->monsters;

            return [new LogoutCommand];
        }

        return [new JoinMapCommand($this->username, $this->mapName, 99999)];
    }

    /**
     * @return Monster[]
     */
    public function monsters(): array
    {
        return $this->monsters;
    }
}
