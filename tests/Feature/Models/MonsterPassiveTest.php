<?php

namespace Tests\Feature\Models;

use App\Models\Monster;
use App\Models\MonsterPassive;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MonsterPassiveTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = ['description'];

        $MonsterPassive = new MonsterPassive;
        $this->assertEquals($fillable, $MonsterPassive->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $MonsterPassive = MonsterPassive::factory()->create();

        $this->assertIsInt($MonsterPassive->id);
        $this->assertIsString($MonsterPassive->description);
        $this->assertInstanceOf(CarbonInterface::class, $MonsterPassive->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $MonsterPassive->updated_at);
    }

    #[Test]
    public function it_can_be_created_with_valid_data(): void
    {
        $data = [
            'description' => 'Cant be stunned',
        ];

        MonsterPassive::create($data);

        $this->assertDatabaseHas('Monster_passives', [
            'description' => 'Cant be stunned',
        ]);
    }

    #[Test]
    public function it_belongs_to_many_enemies(): void
    {
        $MonsterPassive = MonsterPassive::factory()->create();
        $monster = Monster::factory()->create();

        $MonsterPassive->monsters()->attach($monster);

        $this->assertTrue($MonsterPassive->monsters->contains($monster));
    }
}
