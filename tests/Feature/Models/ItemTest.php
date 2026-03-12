<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Item;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ItemTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $this->assertEquals(['aqw_id', 'name'], (new Item)->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $item = Item::factory()->create();

        $this->assertIsInt($item->id);
        $this->assertIsInt($item->aqw_id);
        $this->assertInstanceOf(CarbonInterface::class, $item->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $item->updated_at);
    }

    #[Test]
    public function it_can_have_a_null_name(): void
    {
        $item = Item::factory()->nameless()->create();

        $this->assertNull($item->name);
    }
}
