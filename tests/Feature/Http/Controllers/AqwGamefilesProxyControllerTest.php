<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AqwGamefilesProxyControllerTest extends TestCase
{
    #[Test]
    public function it_returns_swf_file_with_correct_content_type(): void
    {
        Http::fake([
            'game.aq.com/*' => Http::response('fake-swf-content', 200),
        ]);

        $response = $this->get('/proxy/swf/monster/Draconian1.swf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/x-shockwave-flash');
        $this->assertEquals('fake-swf-content', $response->getContent());
    }

    #[Test]
    public function it_caches_swf_response(): void
    {
        Http::fake([
            'game.aq.com/*' => Http::response('fake-swf-content', 200),
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->with('swf_monster_Draconian1.swf', \Mockery::any(), \Mockery::type('Closure'))
            ->andReturn('fake-swf-content');

        $this->get('/proxy/swf/monster/Draconian1.swf');
    }

    #[Test]
    public function it_does_not_call_external_url_when_cache_hits(): void
    {
        Cache::put('swf_monster_Draconian1.swf', 'cached-swf-content', now()->addDays(7));

        Http::fake();

        $response = $this->get('/proxy/swf/monster/Draconian1.swf');

        Http::assertNothingSent();
        $response->assertStatus(200);
        $this->assertEquals('cached-swf-content', $response->getContent());
    }
}