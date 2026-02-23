<?php

declare(strict_types=1);

namespace App\Services\Parsers;

use Dom\HTMLDocument;
use Illuminate\Support\Collection;
use RuntimeException;

final class MonsterPassivesParser extends AbstractMonsterParser
{   
    private static array $knownAbilities = [
        'Cannot be stunned.',
    ];

    /** @return Collection<int, string> */
    public function parse(string $html): Collection
    {
        $dom = HTMLDocument::createFromString($html, LIBXML_NOERROR);

        $hasMonstersVersions = $this->hasVersions($dom);
        if ($hasMonstersVersions) {
            throw new RuntimeException('Monster has versions, cannot parse passives');
        }

        $stringContent = $dom->body->textContent;
        $containsPassives = str_contains($stringContent, 'Abilities:') || str_contains($stringContent, 'Ability:');

        if (! $containsPassives) {
            return collect();
        }

        $abilities = collect(self::$knownAbilities)
            ->filter(fn (string $ability) => str_contains($stringContent, $ability))
            ->values();

        return $abilities;
    }
}
