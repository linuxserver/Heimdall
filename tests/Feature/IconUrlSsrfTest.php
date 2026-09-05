<?php

namespace Tests\Feature;

use App\Helpers\SafeUrlFetcher;
use App\Item;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The add/edit item form accepts an icon URL that Heimdall downloads
 * server-side. That fetch goes through the same SSRF guard as website
 * lookups, so internal addresses are refused unless ALLOW_INTERNAL_REQUESTS
 * is set.
 */
class IconUrlSsrfTest extends TestCase
{
    use RefreshDatabase;

    // A 1x1 transparent PNG.
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    private function bindFetcher(MockHandler $mock): MockHandler
    {
        $this->app->instance(SafeUrlFetcher::class, new SafeUrlFetcher(HandlerStack::create($mock)));

        return $mock;
    }

    private function itemPayload(array $overrides = []): array
    {
        return array_merge([
            'pinned' => 1,
            'appid' => 'null',
            'website' => null,
            'title' => 'Icon Item',
            'colour' => '#000000',
            'url' => 'http://example.com',
            'tags' => [0],
        ], $overrides);
    }

    public function test_public_icon_url_is_downloaded_and_stored(): void
    {
        $this->seed();
        Storage::fake('public');
        $this->bindFetcher(new MockHandler([new Response(200, [], base64_decode(self::PNG))]));

        $response = $this->post('/items', $this->itemPayload(['icon' => 'http://93.184.216.34/favicon.png']));

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $item = Item::where('title', 'Icon Item')->firstOrFail();
        $this->assertStringStartsWith('icons/', $item->icon);
        Storage::disk('public')->assertExists($item->icon);
    }

    public static function blockedIconUrls(): array
    {
        return [
            'loopback' => ['http://127.0.0.1/icon.png'],
            'link-local metadata' => ['http://169.254.169.254/latest/meta-data/icon.png'],
            'rfc1918' => ['http://192.168.1.10/favicon.png'],
            'ipv6 loopback' => ['http://[::1]/icon.png'],
        ];
    }

    #[DataProvider('blockedIconUrls')]
    public function test_internal_icon_url_is_rejected(string $url): void
    {
        $this->seed();
        $mock = $this->bindFetcher(new MockHandler([new Response(200, [], base64_decode(self::PNG))]));

        $response = $this->post('/items', $this->itemPayload(['icon' => $url]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('file');
        $this->assertCount(1, $mock, 'no request should have been sent');
        $this->assertDatabaseMissing('items', ['title' => 'Icon Item']);
    }

    public function test_icon_url_redirecting_to_internal_address_is_rejected(): void
    {
        $this->seed();
        $mock = $this->bindFetcher(new MockHandler([
            new Response(302, ['Location' => 'http://10.0.0.5/secret.png']),
            new Response(200, [], base64_decode(self::PNG)),
        ]));

        $response = $this->post('/items', $this->itemPayload(['icon' => 'http://93.184.216.34/favicon.png']));

        $response->assertSessionHasErrors('file');
        $this->assertCount(1, $mock, 'the redirect target must not be requested');
        $this->assertDatabaseMissing('items', ['title' => 'Icon Item']);
    }

    public function test_icon_url_without_extension_is_rejected(): void
    {
        $this->seed();
        $mock = $this->bindFetcher(new MockHandler([new Response(200, [], 'x')]));

        $this->post('/items', $this->itemPayload(['icon' => 'http://93.184.216.34/favicon']))
            ->assertSessionHasErrors('file');
        $this->assertCount(1, $mock);
    }

    public function test_non_image_response_is_rejected(): void
    {
        $this->seed();
        $this->bindFetcher(new MockHandler([new Response(200, [], '<html>not an image</html>')]));

        $this->post('/items', $this->itemPayload(['icon' => 'http://93.184.216.34/favicon.png']))
            ->assertSessionHasErrors('file');
    }

    public function test_failed_download_is_rejected(): void
    {
        $this->seed();
        $this->bindFetcher(new MockHandler([new Response(404)]));

        $this->post('/items', $this->itemPayload(['icon' => 'http://93.184.216.34/favicon.png']))
            ->assertSessionHasErrors('file');
    }

    public function test_internal_icon_url_is_allowed_when_internal_requests_are_enabled(): void
    {
        $this->seed();
        Storage::fake('public');
        config(['app.allow_internal_requests' => true]);
        $this->bindFetcher(new MockHandler([new Response(200, [], base64_decode(self::PNG))]));

        $response = $this->post('/items', $this->itemPayload(['icon' => 'http://192.168.1.10:8080/favicon.png']));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('items', ['title' => 'Icon Item']);
    }
}
