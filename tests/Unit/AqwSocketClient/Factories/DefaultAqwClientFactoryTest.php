<?php

declare(strict_types=1);

namespace Tests\Unit\AqwSocketClient\Factories;

use App\AqwSocketClient\Factories\ConfigurationFactory;
use App\AqwSocketClient\Factories\DefaultAqwClientFactory;
use AqwSocketClient\Client;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DefaultAqwClientFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_default_client(): void
    {
        $factory = new DefaultAqwClientFactory;

        $configuration = ConfigurationFactory::forFindMapMonsters('testuser', 'testpass', 'testtoken', 'battleon');
        $client = $factory->create($configuration);

        $this->assertInstanceOf(Client::class, $client);
    }
}
