<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\QuestRewardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $quest_id
 * @property-read string $type
 * @property-read ?int $amount
 * @property-read ?int $item_id
 * @property-read ?int $rate
 * @property-read ?int $quantity
 * @property-read ?int $faction_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class QuestReward extends Model
{
    /** @use HasFactory<QuestRewardFactory> */
    use HasFactory;

    protected $fillable = [
        'quest_id',
        'type',
        'amount',
        'item_id',
        'rate',
        'quantity',
        'faction_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'quest_id' => 'integer',
            'type' => 'string',
            'amount' => 'integer',
            'item_id' => 'integer',
            'rate' => 'integer',
            'quantity' => 'integer',
            'faction_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function faction(): BelongsTo
    {
        return $this->belongsTo(Faction::class);
    }
}
