<?php

declare(strict_types=1);

namespace App\Casts\Monster;

use AqwSocketClient\Objects\Names\MonsterName;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<MonsterName, int|MonsterName|string|null>
 */
class MonsterNameCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MonsterName
    {
        if ($value === null) {
            return null;
        }

        return new MonsterName($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof MonsterName) {
            return $value->value;
        }

        return new MonsterName($value)->value;
    }
}
