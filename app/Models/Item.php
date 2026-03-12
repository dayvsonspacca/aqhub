<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read int $aqw_id
 * @property-read ?string $name
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    protected $fillable = [
        'aqw_id',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'aqw_id' => 'integer',
            'name' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
