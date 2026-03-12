<?php

declare(strict_types=1);

namespace App\AqwSocketClient\Scripts;

use AqwSocketClient\Commands\LoadQuestCommand;
use AqwSocketClient\Events\QuestLoadedEvent;
use AqwSocketClient\Interfaces\CommandInterface;
use AqwSocketClient\Interfaces\EventInterface;
use AqwSocketClient\Objects\Identifiers\AreaIdentifier;
use AqwSocketClient\Objects\Identifiers\QuestIdentifier;
use AqwSocketClient\Objects\Quest\Quest;
use AqwSocketClient\Scripts\ClientContext;
use AqwSocketClient\Scripts\ExpirableScript;

class FindQuestScript extends ExpirableScript
{
    private ?Quest $quest = null;

    public function __construct(
        private readonly QuestIdentifier $questIdentifier,
        private readonly AreaIdentifier $areaIdentifier,
    ) {}

    public function start(ClientContext $context): ?CommandInterface
    {
        return new LoadQuestCommand($this->areaIdentifier, $this->questIdentifier);
    }

    public function handles(): array
    {
        return [QuestLoadedEvent::class];
    }

    public function handle(EventInterface $event, ClientContext $context): ?CommandInterface
    {
        if ($event instanceof QuestLoadedEvent && $event->quest->identifier->value === $this->questIdentifier->value) {
            $this->quest = $event->quest;
            $this->success();
        }

        return null;
    }

    public function quest(): ?Quest
    {
        return $this->quest;
    }
}
