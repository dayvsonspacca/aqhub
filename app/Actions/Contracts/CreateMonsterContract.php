<?php

namespace App\Actions\Contracts;

use App\ValueObjects\Level;

interface CreateMonsterContract
{
    public function __invoke(string $name, Level $level, int $hp, string $assetName, string $assetLink): void;
}
