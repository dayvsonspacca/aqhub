<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $quest_id
 * @property-read string $tag
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class QuestTag extends Model
{
    protected $fillable = [
        'quest_id',
        'tag',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'quest_id' => 'integer',
            'tag' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }
}
