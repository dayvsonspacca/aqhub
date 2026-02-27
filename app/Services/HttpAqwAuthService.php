<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

final class HttpAqwAuthService
{
    public static function getToken(string $username, string $password): string
    {
        $response = Http::post('https://game.aq.com/game/api/login/now', [
            'user' => $username,
            'pass' => $password,
            'option' => 1,
        ]);

        $token = $response->json('login.sToken');

        throw_unless(
            $token,
            RuntimeException::class,
            "Failed to retrieve account auth token for user: {$username}"
        );

        return $token;
    }
}
