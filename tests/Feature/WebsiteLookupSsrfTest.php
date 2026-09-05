<?php

namespace Tests\Feature;

use App\Helpers\SafeUrlFetcher;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * /items/websitelookup/{url} fetches a caller-supplied URL server-side.
 * The guard must hold for the initial URL and for every redirect hop.
 */
class WebsiteLookupSsrfTest extends TestCase
{
    use RefreshDatabase;

    private function bindFetcher(MockHandler $mock): MockHandler
    {
        $this->app->instance(SafeUrlFetcher::class, new SafeUrlFetcher(HandlerStack::create($mock)));

        return $mock;
    }

    private function lookup(string $url)
    {
        return $this->get('/items/websitelookup/' . base64_encode($url));
    }

    public function test_public_url_body_is_returned(): void
    {
        $this->seed();
        $this->bindFetcher(new MockHandler([new Response(200, [], '<title>Public</title>')]));

        $this->lookup('http://93.184.216.34/')
            ->assertStatus(200)
            ->assertSee('<title>Public</title>', false);
    }

    public function test_direct_private_url_is_forbidden(): void
    {
        $this->seed();
        $mock = $this->bindFetcher(new MockHandler([new Response(200, [], 'internal')]));

        $this->lookup('http://127.0.0.1:8080/')->assertStatus(403);

        $this->assertCount(1, $mock, 'no request should have been sent');
    }

    public function test_redirect_to_private_url_is_forbidden(): void
    {
        $this->seed();
        $mock = $this->bindFetcher(new MockHandler([
            new Response(302, ['Location' => 'http://169.254.169.254/latest/meta-data/']),
            new Response(200, [], 'metadata'),
        ]));

        $response = $this->lookup('http://93.184.216.34/redirect');

        $response->assertStatus(403);
        $this->assertStringNotContainsString('metadata', $response->getContent());
        $this->assertCount(1, $mock, 'the redirect target must not be requested');
    }

    public function test_redirect_to_public_url_is_followed(): void
    {
        $this->seed();
        $this->bindFetcher(new MockHandler([
            new Response(301, ['Location' => 'https://93.184.216.34/']),
            new Response(200, [], '<title>Moved</title>'),
        ]));

        $this->lookup('http://93.184.216.34/')
            ->assertStatus(200)
            ->assertSee('<title>Moved</title>', false);
    }

    public function test_invalid_url_is_a_bad_request(): void
    {
        $this->seed();

        $this->lookup('not a url')->assertStatus(400);
    }
}
