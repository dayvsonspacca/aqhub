<?php

namespace App\AqwSocketClient\Interfaces;

interface AqwAuthServiceInterface
{
    public function getToken(string $username, string $password): string;
}
