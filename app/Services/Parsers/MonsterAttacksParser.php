<?php

declare(strict_types=1);

namespace App\Services\Parsers;

use Dom\HTMLDocument;
use Illuminate\Support\Collection;
use RuntimeException;

final class MonsterAttacksParser extends AbstractMonsterParser
{
    /** @return Collection<int, array{name: string, min: int, max: int}> */
    public function parse(string $html): Collection
    {
        $dom = HTMLDocument::createFromString($html, LIBXML_NOERROR);
        
        $hasMonstersVersions = $this->hasVersions($dom);
        if ($hasMonstersVersions) {
            throw new RuntimeException('Monster has versions, cannot parse attacks');
        }
        
        $stringContent = $dom->body->textContent;
        $containsAttacks = str_contains($stringContent, 'Attacks:') || str_contains($stringContent, 'Attack:');

        if (! $containsAttacks) {
            return collect();
        }

        preg_match_all('/^(\w+):\s*(\d+)-(\d+)$/m', $stringContent, $matches, PREG_SET_ORDER);

        return collect($matches)->map(fn (array $match) => [
            'name' => $match[1],
            'min' => (int) $match[2],
            'max' => (int) $match[3],
        ]);
    }
}
