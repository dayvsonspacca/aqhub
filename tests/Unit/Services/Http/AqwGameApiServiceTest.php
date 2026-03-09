<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Http;

use App\Services\Http\AqwGameApiService;
use AqwSocketClient\Objects\Names\PlayerName;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class AqwGameApiServiceTest extends TestCase
{
    private AqwGameApiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AqwGameApiService;
    }

    #[Test]
    public function it_can_return_token(): void
    {
        Http::fake([
            AqwGameApiService::URL . '/game/api/login/now' => Http::response([
                'login' => ['sToken' => 'valid-token-123'],
            ]),
        ]);

        $password = md5('test');
        $token = $this->service->token(new PlayerName('Hilise'), $password);
        $this->assertSame($token, 'valid-token-123');
    }

    #[Test]
    public function it_throws_if_token_is_invalid(): void
    {
        Http::fake([
            AqwGameApiService::URL . '/game/api/login/now' => Http::response([
                'login' => ['sToken' => null],
            ]),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to retrieve account auth token for user: Hilise');

        $password = md5('test');
        $this->service->token(new PlayerName('Hilise'), $password);
    }

    #[Test]
    public function it_can_return_travel_map(): void
    {
        $data = [
            ['strMapName' => 'battleon', 'strMapFileName' => 'battleon.swf'],
            ['strMapName' => 'yulgar', 'strMapFileName' => 'yulgar.swf'],
        ];

        Http::fake([
            AqwGameApiService::URL . '/game/api/data/travelmap*' => Http::response($data),
        ]);

        $result = $this->service->travelMap('R0039');
        $this->assertSame($data, $result);
    }

    #[Test]
    public function it_throws_if_travel_map_is_empty(): void
    {
        Http::fake([
            AqwGameApiService::URL . '/game/api/data/travelmap*' => Http::response(null),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to retrieve travel map data for version: R0039');

        $this->service->travelMap('R0039');
    }
}
