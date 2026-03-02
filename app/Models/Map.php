<?php

namespace App\Models;

use App\Casts\Area\AreaNameCast;
use AqwSocketClient\Objects\Names\AreaName;
use Carbon\CarbonInterface;
use Database\Factories\MapFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read int $id
 * @property-read int $aqw_id
 * @property-read string $name
 * @property-read AreaName $join_name
 * @property-read ?string $description
 * @property-read bool $upgrade_only
 * @property-read ?int $recommended_min_level
 * @property-read ?int $recommended_max_level
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class Map extends Model
{
    /** @use HasFactory<MapFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'aqw_id',
        'name',
        'join_name',
        'description',
        'upgrade_only',
        'recommended_min_level',
        'recommended_max_level',
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
            'aqw_id' => 'integer',
            'name' => 'string',
            'join_name' => AreaNameCast::class,
            'description' => 'string',
            'upgrade_only' => 'boolean',
            'recommended_min_level' => 'int',
            'recommended_max_level' => 'int',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function monsters(): BelongsToMany
    {
        return $this->belongsToMany(
            Monster::class,
            'map_monster_assignments'
        );
    }
}
