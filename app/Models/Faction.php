<?php

namespace App\Models;

use App\Casts\Faction\FactionNameCast;
use AqwSocketClient\Objects\Names\FactionName;
use Carbon\CarbonInterface;
use Database\Factories\FactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read int $aqw_id
 * @property-read ?FactionName $name
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
class Faction extends Model
{
    /** @use HasFactory<FactionFactory> */
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
            'name' => FactionNameCast::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
