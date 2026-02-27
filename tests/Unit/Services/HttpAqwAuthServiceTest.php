<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\HttpAqwAuthService;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class HttpAqwAuthServiceTest extends TestCase
{
    private HttpAqwAuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HttpAqwAuthService;
    }

    #[Test]
    public function it_returns_token_on_successful_response(): void
    {
        Http::fake([
            'https://game.aq.com/game/api/login/now' => Http::response([
                'login' => ['sToken' => 'valid-token-123'],
            ]),
        ]);

        $token = $this->service->getToken('user', 'pass');

        $this->assertSame('valid-token-123', $token);
    }

    #[Test]
    public function it_sends_correct_payload(): void
    {
        Http::fake([
            'https://game.aq.com/game/api/login/now' => Http::response([
                'login' => ['sToken' => 'valid-token-123'],
            ]),
        ]);

        $this->service->getToken('myuser', 'mypass');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://game.aq.com/game/api/login/now'
                && $request['user'] === 'myuser'
                && $request['pass'] === 'mypass'
                && $request['option'] === 1;
        });
    }

    #[Test]
    public function it_throws_if_token_is_null(): void
    {
        Http::fake([
            'https://game.aq.com/game/api/login/now' => Http::response([
                'login' => ['sToken' => null],
            ]),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to retrieve account auth token for user: myuser');

        $this->service->getToken('myuser', 'mypass');
    }

    #[Test]
    public function it_throws_if_token_key_is_missing(): void
    {
        Http::fake([
            'https://game.aq.com/game/api/login/now' => Http::response([
                'login' => [],
            ]),
        ]);

        $this->expectException(RuntimeException::class);

        $this->service->getToken('myuser', 'mypass');
    }

    #[Test]
    public function it_throws_if_response_is_empty(): void
    {
        Http::fake([
            'https://game.aq.com/game/api/login/now' => Http::response([]),
        ]);

        $this->expectException(RuntimeException::class);

        $this->service->getToken('myuser', 'mypass');
    }
}
