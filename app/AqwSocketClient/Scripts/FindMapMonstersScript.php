<?php

declare(strict_types=1);

namespace App\AqwSocketClient\Scripts;

use AqwSocketClient\Commands\JoinAreaCommand;
use AqwSocketClient\Events\AlreadyInAreaEvent;
use AqwSocketClient\Events\AreaLockedEvent;
use AqwSocketClient\Events\AreaMemberOnlyEvent;
use AqwSocketClient\Events\AreaNotAvaliableEvent;
use AqwSocketClient\Events\MonstersDetectedEvent;
use AqwSocketClient\Events\PlayerInventoryLoadedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Monster;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Scripts\ExpirableScript;

class FindMapMonstersScript extends ExpirableScript
{
    /**
     * @var Monster[]
     */
    private array $monsters = [];

    public ?EventInterface $failedBy = null;

    public function __construct(
        private PlayerName $player,
        private AreaName $area
    ) {}

    public function handles(): array
    {
        return [
            PlayerInventoryLoadedEvent::class, // ENTRY POINT
            MonstersDetectedEvent::class,
            AreaLockedEvent::class,
            AreaMemberOnlyEvent::class,
            AreaNotAvaliableEvent::class,
            AlreadyInAreaEvent::class,
        ];
    }

    public function handle(EventInterface $event): array
    {
        if ($event instanceof AreaLockedEvent || $event instanceof AreaMemberOnlyEvent || $event instanceof AreaNotAvaliableEvent || $event instanceof AlreadyInAreaEvent) {
            $this->failed();
            $this->failedBy = $event;

            return [];
        }

        if ($event instanceof PlayerInventoryLoadedEvent) {
            return [new JoinAreaCommand($this->player, $this->area, new RoomIdentifier(55555))];
        }

        if ($event instanceof MonstersDetectedEvent) {
            $this->success();
            $this->monsters = $event->monsters;

            return [];
        }

        return [];
    }

    /**
     * @return Monster[]
     */
    public function monsters(): array
    {
        return $this->monsters;
    }
}
