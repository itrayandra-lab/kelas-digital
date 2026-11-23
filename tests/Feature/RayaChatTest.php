<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RayaChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.deepseek.key' => null, 'services.groq.key' => 'test-key']);
    }

    public function test_reply_keeps_only_internal_links(): void
    {
        Http::fake(['api.groq.com/*' => Http::response(['choices' => [['message' => ['content' => 'Coba [Dasar Marketing](/course/dasar-marketing) atau [situs lain](https://evil.test).']]]])]);

        $this->withHeader('Origin', config('app.url'))
            ->postJson(route('raya.chat'), ['message' => 'Saya mau belajar marketing'])
            ->assertOk()
            ->assertJsonPath('reply', 'Coba [Dasar Marketing](/course/dasar-marketing) atau situs lain.')
            ->assertJsonPath('links.0.url', '/course/dasar-marketing');
    }

    public function test_requests_without_origin_are_refused(): void
    {
        Http::fake();

        $this->postJson(route('raya.chat'), ['message' => 'Halo'])->assertForbidden();
        Http::assertNothingSent();
    }

    public function test_provider_failure_returns_friendly_message(): void
    {
        Http::fake(['api.groq.com/*' => Http::response('down', 500)]);

        $this->withHeader('Origin', config('app.url'))
            ->postJson(route('raya.chat'), ['message' => 'Halo'])
            ->assertStatus(503)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'RAYA sedang tidak bisa menjawab'));
    }
}
