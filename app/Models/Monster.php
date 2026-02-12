<?php

namespace App\Models;

use App\Casts\LevelCast;
use App\ValueObjects\Level;
use Carbon\CarbonInterface;
use Database\Factories\MonsterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read Level $level
 * @property-read int $health
 * @property-read int $difficulty
 * @property-read string $asset_name
 * @property-read CarbonInterface $registered_at
 * @property ?CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class Monster extends Model
{
    /** @use HasFactory<MonsterFactory> */
    use HasFactory;

    const CREATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'level',
        'health',
        'difficulty',
        'asset_name',
        'created_at',
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
            'name' => 'string',
            'level' => LevelCast::class,
            'health' => 'integer',
            'difficulty' => 'integer',
            'asset_name' => 'string',
            'registered_at' => 'datetime',
            'updated_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function passives(): BelongsToMany
    {
        return $this->belongsToMany(
            MonsterPassive::class,
            'Monster_passive_assignments'
        );
    }
}
