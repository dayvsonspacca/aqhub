<?php

declare(strict_types=1);

namespace App\Services\Parsers;

use Dom\HTMLDocument;
use Dom\Node;
use Illuminate\Support\Collection;

final class EnemyPassivesParser
{
    /** @return Collection<int, string> */
    public function parse(string $html): Collection
    {
        $dom = HTMLDocument::createFromString($html, LIBXML_NOERROR);

        $abilities = $this->extractAbilitiesFromElement($dom->body->firstChild);

        return $abilities;
    }

    /** @return Collection<int, string> */
    private function extractAbilitiesFromElement(Node $element): Collection
    {
        $abilities = collect();
        $foundAbilities = false;

        foreach ($element->childNodes as $child) {
            if (in_array($child->nodeName, ['P', 'STRONG'])) {
                $textContent = $child->textContent;
                if (str_contains($textContent, 'Abilities:') || str_contains($textContent, 'Ability:')) {
                    $foundAbilities = true;

                    if (str_contains($textContent, 'Ability:') && $child->nodeName === 'P') {
                        $ability = $this->extractAbilityFromParagraph($child);
                        if ($ability !== null) {
                            $abilities->push($ability);
                            break;
                        }
                    }

                    continue;
                }
            }

            if ($foundAbilities && $child->nodeName === 'UL') {
                foreach ($child->childNodes as $li) {
                    if ($li->nodeName === 'LI') {
                        $text = mb_trim($li->textContent);
                        if (! empty($text)) {
                            $abilities->push($text);
                        }
                    }
                }
                break;
            }
        }

        return $abilities;
    }

    private function extractAbilityFromParagraph(Node $paragraph): ?string
    {
        foreach ($paragraph->childNodes as $node) {
            if ($node->nodeName === 'STRONG' && str_contains($node->textContent, 'Ability:')) {
                $nextNode = $node->nextSibling;
                if ($nextNode !== null && $nextNode->nodeName === '#text') {
                    $text = mb_trim($nextNode->textContent);
                    if (! empty($text)) {
                        return $text;
                    }
                }
            }
        }

        return null;
    }
}
