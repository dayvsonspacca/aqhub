<?php

namespace App\AqwSocketClient\Factories;

use App\AqwSocketClient\Interfaces\AqwClientFactoryInterface;
use AqwSocketClient\Client;
use AqwSocketClient\Configuration;
use AqwSocketClient\Server;

final class DefaultAqwClientFactory implements AqwClientFactoryInterface
{
    public function create(Configuration $configuration): Client
    {
        return new Client(Server::espada(), $configuration);
    }
}
