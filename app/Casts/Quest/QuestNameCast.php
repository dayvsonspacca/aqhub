<?php

declare(strict_types=1);

namespace App\Casts\Quest;

use AqwSocketClient\Objects\Names\QuestName;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<QuestName, int|QuestName|string|null>
 */
class QuestNameCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?QuestName
    {
        if ($value === null) {
            return null;
        }

        return new QuestName($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof QuestName) {
            return $value->value;
        }

        return new QuestName($value)->value;
    }
}
