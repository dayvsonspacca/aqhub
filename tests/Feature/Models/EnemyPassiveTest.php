<?php

namespace Tests\Unit\Models;

use App\Models\Enemy;
use App\Models\EnemyPassive;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EnemyPassiveTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = ['description'];

        $enemyPassive = new EnemyPassive;
        $this->assertEquals($fillable, $enemyPassive->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $enemyPassive = EnemyPassive::factory()->create();

        $this->assertIsInt($enemyPassive->id);
        $this->assertIsString($enemyPassive->description);
        $this->assertInstanceOf(CarbonInterface::class, $enemyPassive->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $enemyPassive->updated_at);
    }

    #[Test]
    public function it_can_be_created_with_valid_data(): void
    {
        $data = [
            'description' => 'Cant be stunned',
        ];

        EnemyPassive::create($data);

        $this->assertDatabaseHas('enemy_passives', [
            'description' => 'Cant be stunned',
        ]);
    }

    #[Test]
    public function it_belongs_to_many_enemies(): void
    {
        $enemyPassive = EnemyPassive::factory()->create();
        $enemy = Enemy::factory()->create();

        $enemyPassive->enemies()->attach($enemy);

        $this->assertTrue($enemyPassive->enemies->contains($enemy));
    }
}
