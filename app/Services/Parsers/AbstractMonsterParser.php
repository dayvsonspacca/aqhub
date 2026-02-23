<?php

namespace App\Services\Parsers;

use Dom\HTMLDocument;

abstract class AbstractMonsterParser
{
    public function hasVersions(HTMLDocument $dom): bool
    {
        $navBars = $dom->body->getElementsByClassName('yui-nav');

        if ($navBars->count() === 0) {
            return false;
        }

        foreach ($navBars as $nav) {
            if (preg_match('/^Version\s+\d+$/m', $nav->textContent)) {
                return true;
            }
        }

        return false;
    }
}
