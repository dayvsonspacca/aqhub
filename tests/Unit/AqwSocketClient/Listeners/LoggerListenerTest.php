<?php

declare(strict_types=1);

namespace Tests\Unit\AqwSocketClient\Listeners;

use App\AqwSocketClient\Listeners\LoggerListener;
use AqwSocketClient\Interfaces\EventInterface;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LoggerListenerTest extends TestCase
{
    private LoggerListener $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = new LoggerListener;
    }

    #[Test]
    public function it_logs_the_received_event_with_instance(): void
    {
        Log::spy();

        $event = new class implements EventInterface {};

        $this->listener->listen($event);

        Log::shouldHaveReceived('debug')
            ->once()
            ->with('Received event ' . $event::class, [
                'event' => $event,
            ]);
    }
}
