<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AqwGamefilesProxyController
{
    public function monster(string $filename)
    {
        $swf = Cache::remember('swf_monster_' . $filename, now()->addDays(7), function () use ($filename) {
            $url = 'http://game.aq.com/game/gamefiles/mon/' . $filename;

            return Http::get($url)->body();
        });

        return response($swf, 200)
            ->header('Content-Type', 'application/x-shockwave-flash');
    }
}
