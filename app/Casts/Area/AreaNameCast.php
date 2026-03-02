<?php

declare(strict_types=1);

namespace App\Casts\Area;

use AqwSocketClient\Objects\Names\AreaName;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<AreaName, int|AreaName|string|null>
 */
class AreaNameCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?AreaName
    {
        if ($value === null) {
            return null;
        }

        return new AreaName($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof AreaName) {
            return $value->value;
        }

        return new AreaName($value)->value;
    }
}
