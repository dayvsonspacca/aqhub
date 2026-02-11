<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\EnemyPassiveFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read int $id
 * @property-read string $description
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class EnemyPassive extends Model
{
    /** @use HasFactory<EnemyPassiveFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'description',
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
            'description' => 'string',
            'updated_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function enemies(): BelongsToMany
    {
        return $this->belongsToMany(
            Enemy::class,
            'enemy_passive_assignments'
        );
    }
}
