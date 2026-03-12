<?php

declare(strict_types=1);

namespace App\Casts\Faction;

use AqwSocketClient\Objects\Names\FactionName;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<FactionName, int|FactionName|string|null>
 */
class FactionNameCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?FactionName
    {
        if ($value === null) {
            return null;
        }

        return new FactionName($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof FactionName) {
            return $value->value;
        }

        return new FactionName($value)->value;
    }
}
