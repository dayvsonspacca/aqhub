<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Parsers;

use App\Services\Parsers\EnemyPassivesParser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EnemyPassivesParserTest extends TestCase
{
    #[Test]
    public function it_can_parse_abilities_in_p_before_ul(): void
    {
        $html = file_get_contents(__DIR__ . '/Fixtures/abilities_in_p_before_ul.html');

        $parser = new EnemyPassivesParser;
        $abilities = $parser->parse($html);

        $this->assertCount(1, $abilities);
        $this->assertEquals('Cannot be stunned.', $abilities->first());
    }

    #[Test]
    public function it_can_parse_abilities_as_strong_before_ul(): void
    {
        $html = file_get_contents(__DIR__ . '/Fixtures/abilities_as_strong_before_ul.html');

        $parser = new EnemyPassivesParser;
        $abilities = $parser->parse($html);

        $this->assertCount(1, $abilities);
        $this->assertEquals('Cannot be stunned.', $abilities->first());
    }

    #[Test]
    public function it_returns_empty_collection_when_no_abilities(): void
    {
        $html = '<div id="page-content"><p>No abilities here</p></div>';

        $parser = new EnemyPassivesParser;
        $abilities = $parser->parse($html);

        $this->assertEmpty($abilities);
    }

    #[Test]
    public function it_can_parse_multiple_abilities_in_p_before_ul(): void
    {
        $html = file_get_contents(__DIR__ . '/Fixtures/abilities_in_p_before_ul_multiple.html');

        $parser = new EnemyPassivesParser;
        $abilities = $parser->parse($html);

        $this->assertCount(5, $abilities);
        $this->assertEquals('Cannot be stunned.', $abilities->first());
        $this->assertEquals('Will not respawn until Captain Mercurius is defeated.', $abilities->last());
    }

    #[Test]
    public function it_returns_empty_collection_when_no_page_content(): void
    {
        $html = '<p><strong>Abilities:</strong></p><ul><li>Cannot be stunned.</li></ul>';

        $parser = new EnemyPassivesParser;
        $abilities = $parser->parse($html);

        $this->assertEmpty($abilities);
    }

    #[Test]
    public function it_can_parse_ability_before_br_in_strong(): void
    {
        $html = file_get_contents(__DIR__ . '/Fixtures/ability_before_br_in_strong.html');

        $parser = new EnemyPassivesParser;
        $abilities = $parser->parse($html);

        $this->assertCount(1, $abilities);
        $this->assertEquals('Cannot be stunned.', $abilities->first());
    }
}
