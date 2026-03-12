<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\CharacterClass;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CharacterClassTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $this->assertEquals(['aqw_id', 'name'], (new CharacterClass)->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $class = CharacterClass::factory()->create();

        $this->assertIsInt($class->id);
        $this->assertIsInt($class->aqw_id);
        $this->assertInstanceOf(CarbonInterface::class, $class->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $class->updated_at);
    }
}
