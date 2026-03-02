<?php

declare(strict_types=1);

namespace App\Services\Http;

use AqwSocketClient\Objects\Names\PlayerName;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use SensitiveParameter;

final class AqwGameApiService
{
    public const string URL = 'https://game.aq.com';

    public function token(PlayerName $player, #[SensitiveParameter] string $password): string
    {
        $response = Http::post(self::URL . '/game/api/login/now', [
            'user' => $player->value,
            'pass' => $password,
            'option' => 1,
        ]);

        $token = $response->json('login.sToken');

        throw_unless(
            $token,
            RuntimeException::class,
            "Failed to retrieve account auth token for user: {$player}"
        );

        return $token;
    }

    public function travelMap(string $version): array
    {
        $response = Http::get(self::URL . '/game/api/data/travelmap', [
            'v' => $version,
        ]);

        $json = $response->json();

        throw_unless(
            filled($json),
            RuntimeException::class,
            "Failed to retrieve travel map data for version: {$version}"
        );

        return $json;
    }
}
