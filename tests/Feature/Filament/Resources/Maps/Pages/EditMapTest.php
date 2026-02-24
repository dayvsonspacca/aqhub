<?php

namespace Tests\Feature\Filament\Resources\Maps\Pages;

use App\Filament\Resources\Maps\Pages\EditMap;
use App\Models\Map;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EditMapTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_load_the_page(): void
    {
        $map = Map::factory()->create();

        Livewire::test(EditMap::class, ['record' => $map->id])->assertOk();
    }
}
