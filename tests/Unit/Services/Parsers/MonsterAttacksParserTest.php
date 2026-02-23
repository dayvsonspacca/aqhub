<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Parsers;

use App\Services\Parsers\MonsterAttacksParser;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class MonsterAttacksParserTest extends TestCase
{
    #[Test]
    public function it_returns_empty_collection_when_page_doesnt_have_attacks(): void
    {
        $html = file_get_contents(__DIR__ . '/Fixtures/Attacks/No Attacks - AQW.html');
        $parser = new MonsterAttacksParser;
        $attacks = $parser->parse($html);
        $this->assertTrue($attacks->isEmpty());
    }

    #[Test]
    public function it_cant_parse_attacks_when_page_has_monster_versions(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Monster has versions, cannot parse attacks');

        $html = file_get_contents(__DIR__ . '/Fixtures/Attacks/Water Draconian - AQW.html');

        $parser = new MonsterAttacksParser;
        $parser->parse($html);
    }

    #[Test]
    public function it_can_parse_attacks(): void
    {
        $html = file_get_contents(__DIR__ . '/Fixtures/Attacks/Bronze Draconian - AQW.html');

        $parser = new MonsterAttacksParser;
        $attacks = $parser->parse($html);

        $this->assertCount(2, $attacks);
        $this->assertSame([
            [
                'name' => 'Slash',
                'min' => 29,
                'max' => 37,
            ],
            [
                'name' => 'Smash',
                'min' => 29,
                'max' => 37,
            ],
        ], $attacks->toArray());
    }
}
