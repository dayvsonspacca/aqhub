<?php

namespace Tests\Feature\Actions;

use App\Actions\CreateMonster;
use App\ValueObjects\Level;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Illuminate\Support\now;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateMonsterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_an_monster()
    {
        $createMonster = new CreateMonster;
        $now = now();

        $createMonster('Goblin', Level::from(100), 1000, 'Draconian5.swf');

        $this->assertDatabaseHas('monsters', [
            'name' => 'Goblin',
            'level' => 100,
            'health' => 1000,
            'asset_name' => 'Draconian5.swf',
        ]);
    }
}
