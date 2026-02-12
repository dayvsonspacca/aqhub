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

        $createMonster('Goblin', Level::from(100), 1000, 5, 'Draconian5.swf', $now);

        $this->assertDatabaseHas('monsters', [
            'name' => 'Goblin',
            'level' => 100,
            'health' => 1000,
            'difficulty' => 5,
            'asset_name' => 'Draconian5.swf',
            'created_at' => $now,
        ]);
    }
}
