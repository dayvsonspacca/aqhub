<?php

declare(strict_types=1);

namespace App\AqwSocketClient\Listeners;

use AqwSocketClient\Events\AreaJoinedEvent;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ListenerInterface;

final class FindMonstersListener implements ListenerInterface
{
    public ?AreaJoinedEvent $targetMapJoinedEvent = null;

    public function __construct(
        private string $join_name
    ) {}

    public function listen(EventInterface $event)
    {
        if ($event instanceof AreaJoinedEvent && $event->mapName === $this->join_name) {
            $this->targetMapJoinedEvent = $event;
        }
    }
}
