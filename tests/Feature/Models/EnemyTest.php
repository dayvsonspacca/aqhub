<?php

namespace Tests\Unit\Models;

use App\Models\Enemy;
use App\Models\EnemyPassive;
use App\ValueObjects\Level;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EnemyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = ['name', 'level', 'health', 'difficulty', 'created_at'];

        $enemy = new Enemy;
        $this->assertEquals($fillable, $enemy->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $enemy = Enemy::factory()->create();

        $this->assertIsInt($enemy->id);
        $this->assertIsString($enemy->name);
        $this->assertInstanceOf(Level::class, $enemy->level);
        $this->assertIsInt($enemy->health);
        $this->assertIsInt($enemy->difficulty);
        $this->assertInstanceOf(CarbonInterface::class, $enemy->registered_at);
        $this->assertInstanceOf(CarbonInterface::class, $enemy->updated_at);
        $this->assertNull($enemy->created_at);
    }

    #[Test]
    public function it_can_be_created_with_valid_data(): void
    {
        $userData = [
            'name' => 'Doomlord',
            'level' => Level::from(1),
            'health' => 100,
            'difficulty' => 1,
        ];

        Enemy::create($userData);

        $this->assertDatabaseHas('enemies', [
            'name' => 'Doomlord',
            'level' => 1,
            'health' => 100,
            'difficulty' => 1,
            'created_at' => null,
        ]);
    }

    #[Test]
    public function it_has_correct_timestamps(): void
    {
        $enemy = Enemy::factory()->create();

        $this->assertNotNull($enemy->registered_at);
        $this->assertNull($enemy->created_at);
        $this->assertNotNull($enemy->updated_at);
    }

    #[Test]
    public function it_can_have_created_at_timestamp(): void
    {
        $enemy = Enemy::factory()->created()->create();

        $this->assertNotNull($enemy->created_at);
    }

    #[Test]
    public function it_has_passives_relationship(): void
    {
        $enemy = Enemy::factory()->create();

        $this->assertTrue(method_exists($enemy, 'passives'));
        $this->assertInstanceOf(BelongsToMany::class, $enemy->passives());
    }

    #[Test]
    public function it_can_have_passives(): void
    {
        $enemy = Enemy::factory()->create();
        $passive = EnemyPassive::factory()->create();

        $this->assertEmpty($enemy->passives);

        $enemy->passives()->attach($passive);
        $enemy->load('passives');

        $this->assertTrue($enemy->passives->contains($passive));
    }
}
