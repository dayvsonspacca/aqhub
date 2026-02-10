<?php

namespace App\Models;

use App\Casts\LevelCast;
use App\ValueObjects\Level;
use Carbon\CarbonInterface;
use Database\Factories\EnemyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read Level $level
 * @property-read int $health
 * @property-read int $difficulty
 * @property-read CarbonInterface $registered_at
 * @property ?CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class Enemy extends Model
{
    /** @use HasFactory<EnemyFactory> */
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
            'registered_at' => 'datetime',
            'updated_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
}
