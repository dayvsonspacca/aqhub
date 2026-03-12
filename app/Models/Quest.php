<?php

namespace App\Models;

use App\Casts\Quest\QuestNameCast;
use AqwSocketClient\Objects\Names\QuestName;
use Carbon\CarbonInterface;
use Database\Factories\QuestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property-read int $aqw_id
 * @property-read QuestName $name
 * @property-read string $description
 * @property-read ?string $completion_text
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class Quest extends Model
{
    /** @use HasFactory<QuestFactory> */
    use HasFactory;

    protected $fillable = [
        'aqw_id',
        'name',
        'description',
        'completion_text',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'aqw_id' => 'integer',
            'name' => QuestNameCast::class,
            'description' => 'string',
            'completion_text' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function tags(): HasMany
    {
        return $this->hasMany(QuestTag::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(QuestRequirement::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(QuestReward::class);
    }

    public function turnInItems(): HasMany
    {
        return $this->hasMany(QuestTurnInItem::class);
    }

    public function maps(): BelongsToMany
    {
        return $this->belongsToMany(Map::class, 'quest_map_assignments');
    }
}
