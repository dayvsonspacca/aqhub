<?php

namespace Tests\Feature\Models;

use App\Models\Monster;
use App\Models\MonsterPassive;
use App\ValueObjects\Level;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MonsterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = ['name', 'level', 'health', 'difficulty', 'asset_name', 'created_at'];

        $monster = new Monster;
        $this->assertEquals($fillable, $monster->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $monster = Monster::factory()->create();

        $this->assertIsInt($monster->id);
        $this->assertIsString($monster->name);
        $this->assertInstanceOf(Level::class, $monster->level);
        $this->assertIsInt($monster->health);
        $this->assertIsInt($monster->difficulty);
        $this->assertIsString($monster->asset_name);
        $this->assertInstanceOf(CarbonInterface::class, $monster->registered_at);
        $this->assertInstanceOf(CarbonInterface::class, $monster->updated_at);
        $this->assertNull($monster->created_at);
    }

    #[Test]
    public function it_can_be_created_with_valid_data(): void
    {
        $userData = [
            'name' => 'Doomlord',
            'level' => Level::from(1),
            'health' => 100,
            'difficulty' => 1,
            'asset_name' => 'Draconian5.swf',
        ];

        Monster::create($userData);

        $this->assertDatabaseHas('monsters', [
            'name' => 'Doomlord',
            'level' => 1,
            'health' => 100,
            'difficulty' => 1,
            'asset_name' => 'Draconian5.swf',
            'created_at' => null,
        ]);
    }

    #[Test]
    public function it_has_correct_timestamps(): void
    {
        $monster = Monster::factory()->create();

        $this->assertNotNull($monster->registered_at);
        $this->assertNull($monster->created_at);
        $this->assertNotNull($monster->updated_at);
    }

    #[Test]
    public function it_can_have_created_at_timestamp(): void
    {
        $monster = Monster::factory()->created()->create();

        $this->assertNotNull($monster->created_at);
    }

    #[Test]
    public function it_has_passives_relationship(): void
    {
        $monster = Monster::factory()->create();

        $this->assertTrue(method_exists($monster, 'passives'));
        $this->assertInstanceOf(BelongsToMany::class, $monster->passives());
    }

    #[Test]
    public function it_can_have_passives(): void
    {
        $monster = Monster::factory()->create();
        $passive = MonsterPassive::factory()->create();

        $this->assertEmpty($monster->passives);

        $monster->passives()->attach($passive);
        $monster->load('passives');

        $this->assertTrue($monster->passives->contains($passive));
    }
}
