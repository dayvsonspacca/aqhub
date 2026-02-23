<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Parsers;

use App\Services\Parsers\MonsterPassivesParser;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class MonsterPassivesParserTest extends TestCase
{
    #[Test]
    public function it_returns_an_empty_collection_if_no_passives_are_present(): void
    {
        $html = file_get_contents(__DIR__ . '/Fixtures/Passives/No Passives - AQW.html');
        $parser = new MonsterPassivesParser;
        $abilities = $parser->parse($html);
        $this->assertCount(0, $abilities);
    }

    #[Test]
    public function it_cant_parse_passives_when_page_has_monster_versions(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Monster has versions, cannot parse passives');

        $html = file_get_contents(__DIR__ . '/Fixtures/Attacks/Water Draconian - AQW.html');

        $parser = new MonsterPassivesParser;
        $parser->parse($html);
    }

    #[Test]
    public function it_can_parse_known_passives(): void
    {
        $html = file_get_contents(__DIR__ . '/Fixtures/Passives/Prince Drakath - AQW.html');

        $parser = new MonsterPassivesParser;
        $abilities = $parser->parse($html);

        $this->assertCount(1, $abilities);
        $this->assertEquals('Cannot be stunned.', $abilities->first());
    }
}
