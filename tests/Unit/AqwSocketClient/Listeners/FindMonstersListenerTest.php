<?php

declare(strict_types=1);

namespace Tests\Unit\AqwSocketClient\Listeners;

use App\AqwSocketClient\Listeners\FindMonstersListener;
use AqwSocketClient\Events\AreaJoinedEvent;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FindMonstersListenerTest extends TestCase
{
    private FindMonstersListener $listener;

    protected function setUp(): void
    {
        $this->listener = new FindMonstersListener('lair');
    }

    #[Test]
    public function should_not_find_expected_event(): void
    {
        $event = new AreaJoinedEvent('battelon', 0, 1, [], []);

        $this->listener->listen($event);

        $this->assertNull($this->listener->targetMapJoinedEvent);
    }

    #[Test]
    public function should_find_expected_event(): void
    {
        $event = new AreaJoinedEvent('lair', 0, 1, [], []);

        $this->listener->listen($event);

        $this->assertNotNull($this->listener->targetMapJoinedEvent);
        $this->assertSame($event, $this->listener->targetMapJoinedEvent);
    }
}
