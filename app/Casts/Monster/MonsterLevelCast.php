<?php

declare(strict_types=1);

namespace App\Casts\Monster;

use AqwSocketClient\Objects\Levels\MonsterLevel;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<MonsterLevel, int|MonsterLevel|string|null>
 */
class MonsterLevelCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MonsterLevel
    {
        if ($value === null) {
            return null;
        }

        return new MonsterLevel((int) $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        if ($value instanceof MonsterLevel) {
            return $value->value;
        }

        return new MonsterLevel($value)->value;
    }
}
