<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\QuestRequirementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $quest_id
 * @property-read string $type
 * @property-read ?int $required_level
 * @property-read ?int $faction_id
 * @property-read ?int $required_rank
 * @property-read ?int $class_id
 * @property-read ?int $required_class_rank
 * @property-read ?int $required_item_id
 * @property-read ?int $required_quest_id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class QuestRequirement extends Model
{
    /** @use HasFactory<QuestRequirementFactory> */
    use HasFactory;

    protected $fillable = [
        'quest_id',
        'type',
        'required_level',
        'faction_id',
        'required_rank',
        'class_id',
        'required_class_rank',
        'required_item_id',
        'required_quest_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'quest_id' => 'integer',
            'type' => 'string',
            'required_level' => 'integer',
            'faction_id' => 'integer',
            'required_rank' => 'integer',
            'class_id' => 'integer',
            'required_class_rank' => 'integer',
            'required_item_id' => 'integer',
            'required_quest_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }

    public function faction(): BelongsTo
    {
        return $this->belongsTo(Faction::class);
    }

    public function characterClass(): BelongsTo
    {
        return $this->belongsTo(CharacterClass::class, 'class_id');
    }

    public function requiredItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'required_item_id');
    }

    public function requiredQuest(): BelongsTo
    {
        return $this->belongsTo(Quest::class, 'required_quest_id');
    }
}
