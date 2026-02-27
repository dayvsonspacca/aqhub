<?php

namespace Tests\Feature\Actions;

use App\Actions\CreateMonster;
use App\ValueObjects\Level;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateMonsterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_an_monster()
    {
        $createMonster = new CreateMonster;

        $createMonster('Goblin', Level::from(100), 1000, 'Draconian5.swf', 'Draconian5');

        $this->assertDatabaseHas('monsters', [
            'name' => 'Goblin',
            'level' => 100,
            'health' => 1000,
            'asset_name' => 'Draconian5.swf',
            'asset_link' => 'Draconian5',
        ]);
    }
}
