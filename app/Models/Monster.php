<?php

namespace App\Models;

use App\Casts\HealthCast;
use App\Casts\Monster\MonsterLevelCast;
use App\Casts\Monster\MonsterNameCast;
use AqwSocketClient\Objects\Levels\MonsterLevel;
use AqwSocketClient\Objects\Monster\Health;
use AqwSocketClient\Objects\Names\MonsterName;
use Carbon\CarbonInterface;
use Database\Factories\MonsterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read int $id
 * @property-read MonsterName $name
 * @property-read MonsterLevel $level
 * @property-read Health $health
 * @property-read string $asset_name
 * @property-read string $asset_link
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class Monster extends Model
{
    /** @use HasFactory<MonsterFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'level',
        'health',
        'asset_name',
        'asset_link',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'name' => MonsterNameCast::class,
            'level' => MonsterLevelCast::class,
            'health' => HealthCast::class,
            'asset_name' => 'string',
            'asset_link' => 'string',
            'updated_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function passives(): BelongsToMany
    {
        return $this->belongsToMany(
            MonsterPassive::class,
            'monster_passive_assignments'
        );
    }

    public function maps(): BelongsToMany
    {
        return $this->belongsToMany(
            Map::class,
            'map_monster_assignments'
        );
    }
}
