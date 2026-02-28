<?php

declare(strict_types=1);

namespace App\Casts;

use AqwSocketClient\Objects\Health;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<Health, int|Health|string|null>
 */
class HealthCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Health
    {
        if ($value === null) {
            return null;
        }

        return new Health((int) $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        if ($value instanceof Health) {
            return $value->value;
        }

        return new Health($value)->value;
    }
}
