<?php

declare(strict_types=1);

namespace App\Casts;

use App\ValueObjects\Level;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class LevelCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Level
    {
        if ($value === null) {
            return null;
        }

        return Level::from((int) $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        if ($value instanceof Level) {
            return $value->value;
        }

        return Level::from($value)->value;
    }
}
