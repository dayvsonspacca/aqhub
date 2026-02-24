<?php

namespace App\AqwSocketClient\Interfaces;

use AqwSocketClient\Client;
use AqwSocketClient\Configuration;

interface AqwClientFactoryInterface
{
    public function create(Configuration $configuration): Client;
}
