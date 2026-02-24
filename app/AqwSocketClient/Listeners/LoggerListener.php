<?php

declare(strict_types=1);

namespace App\AqwSocketClient\Listeners;

use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Interfaces\ListenerInterface;

final class LoggerListener implements ListenerInterface
{
    public function listen(EventInterface $event)
    {
        logger('Received event ' . $event::class, [
            'event' => $event,
        ]);
    }
}
