<?php

namespace Tests\Feature\Actions;

use App\Actions\CreateEnemy;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Illuminate\Support\now;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateEnemyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_an_enemy()
    {
        $createEnemy = new CreateEnemy;
        $now = now();

        $createEnemy('Goblin', 100, 1000, 5, $now);

        $this->assertDatabaseHas('enemies', [
            'name' => 'Goblin',
            'level' => 100,
            'health' => 1000,
            'difficulty' => 5,
            'created_at' => $now,
        ]);
    }
}
