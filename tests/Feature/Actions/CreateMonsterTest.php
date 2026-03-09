<?php

declare(strict_types=1);

namespace Tests\Feature\Actions;

use App\Actions\CreateMonster;
use App\Models\Monster;
use AqwSocketClient\Objects\GameFileMetadata;
use AqwSocketClient\Objects\Health;
use AqwSocketClient\Objects\Levels\MonsterLevel;
use AqwSocketClient\Objects\Names\MonsterName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateMonsterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_monster(): void
    {
        $action = new CreateMonster;
        $name = new MonsterName('Red Dragon');
        $level = new MonsterLevel(100);
        $health = new Health(1000);
        $metadata = new GameFileMetadata('Draconian5', 'Draconian5.swf');

        $monster = $action->handle($name, $level, $health, $metadata);

        $this->assertInstanceOf(Monster::class, $monster);
        $this->assertDatabaseCount('monsters', 1);
        $this->assertDatabaseHas('monsters', [
            'name' => $name,
            'level' => $level,
            'health' => $health,
            'asset_name' => $metadata->file,
            'asset_link' => $metadata->link,
        ]);
    }

    #[Test]
    public function it_returns_existing_monster_without_creating_duplicate(): void
    {
        $action = new CreateMonster;
        $name = new MonsterName('Red Dragon');
        $level = new MonsterLevel(100);
        $health = new Health(1000);
        $metadata = new GameFileMetadata('Draconian5', 'Draconian5.swf');

        $first = $action->handle($name, $level, $health, $metadata);
        $second = $action->handle($name, $level, $health, $metadata);

        $this->assertSame($first->id, $second->id);
        $this->assertDatabaseCount('monsters', 1);
    }
}
