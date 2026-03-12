<?php

declare(strict_types=1);

namespace App\AqwSocketClient\Scripts;

use AqwSocketClient\Commands\JoinAreaCommand;
use AqwSocketClient\Events\AlreadyInAreaEvent;
use AqwSocketClient\Events\AreaLockedEvent;
use AqwSocketClient\Events\AreaMemberOnlyEvent;
use AqwSocketClient\Events\AreaNotAvaliableEvent;
use AqwSocketClient\Events\MonstersDetectedEvent;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Objects\Identifiers\RoomIdentifier;
use AqwSocketClient\Objects\Monster\Monster;
use AqwSocketClient\Objects\Names\AreaName;
use AqwSocketClient\Objects\Names\PlayerName;
use AqwSocketClient\Scripts\ClientContext;
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

    public function start(ClientContext $context): ?CommandInterface
    {
        return new JoinAreaCommand($this->player, $this->area, new RoomIdentifier(55555));
    }

    public function handles(): array
    {
        return [
            MonstersDetectedEvent::class,
            AreaLockedEvent::class,
            AreaMemberOnlyEvent::class,
            AreaNotAvaliableEvent::class,
            AlreadyInAreaEvent::class,
        ];
    }

    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        if ($event instanceof AreaLockedEvent || $event instanceof AreaMemberOnlyEvent || $event instanceof AreaNotAvaliableEvent || $event instanceof AlreadyInAreaEvent) {
            $this->failed();
            $this->failedBy = $event;

            return null;
        }

        if ($event instanceof MonstersDetectedEvent) {
            $this->success();
            $this->monsters = $event->monsters;

            return null;
        }

        return null;
    }

    /**
     * @return Monster[]
     */
    public function monsters(): array
    {
        return $this->monsters;
    }
}
