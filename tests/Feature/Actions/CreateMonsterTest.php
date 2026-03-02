<?php

namespace Tests\Feature\Actions;

use App\Actions\CreateMonster;
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
    public function it_creates_an_monster()
    {
        $action = new CreateMonster;
        $name = new MonsterName('Red Dragon');
        $level = new MonsterLevel(100);
        $health = new Health(1000);
        $metadata = new GameFileMetadata('Draconian5', 'Draconian5.swf');

        $action->handle($name, $level, $health, $metadata);

        $this->assertDatabaseHas('monsters', [
            'name' => $name,
            'level' => $level,
            'health' => $health,
            'asset_name' => $metadata->file,
            'asset_link' => $metadata->link,
        ]);
    }
}
