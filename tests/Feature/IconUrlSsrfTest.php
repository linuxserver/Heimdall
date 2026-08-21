<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the SSRF guard added to ItemController::store / ::update.
 *
 * The guard rejects icon URLs whose host resolves to a private or reserved
 * address, preventing Heimdall from being used as an internal request proxy.
 * Self-hosted installs that point icons at internal services can opt out via
 * ALLOW_INTERNAL_REQUESTS=true in their .env.
 */
class IconUrlSsrfTest extends TestCase
{
    use RefreshDatabase;

    protected function itemPayload(array $overrides = []): array
    {
        return array_merge([
            'pinned'  => 1,
            'appid'   => 'null',
            'website' => null,
            'title'   => 'SSRF Test Item',
            'colour'  => '#000000',
            'url'     => 'http://example.com',
            'tags'    => [0],
        ], $overrides);
    }

    // ------------------------------------------------------------------ happy path

    public function test_icon_with_public_ip_is_accepted(): void
    {
        $this->seed();

        // 8.8.8.8 is a public address - the guard should allow it.
        $response = $this->post('/items', $this->itemPayload([
            'icon' => 'http://8.8.8.8/favicon.png',
        ]));

        // Validation passes; the fetch itself may fail (no real server), but we
        // only care that the guard does NOT raise a 422 for the icon field.
        $response->assertJsonMissingValidationErrors('file');
    }

    // ------------------------------------------------------------------ blocked by default

    public function test_loopback_icon_is_rejected(): void
    {
        $this->seed();

        $response = $this->post('/items', $this->itemPayload([
            'icon' => 'http://127.0.0.1/icon.png',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_link_local_metadata_ip_is_rejected(): void
    {
        $this->seed();

        $response = $this->post('/items', $this->itemPayload([
            'icon' => 'http://169.254.169.254/latest/meta-data/icon.png',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_rfc1918_private_ip_is_rejected(): void
    {
        $this->seed();

        $response = $this->post('/items', $this->itemPayload([
            'icon' => 'http://192.168.1.10/favicon.png',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_non_http_scheme_is_rejected(): void
    {
        $this->seed();

        $response = $this->post('/items', $this->itemPayload([
            'icon' => 'ftp://example.com/icon.png',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_file_scheme_is_rejected(): void
    {
        $this->seed();

        $response = $this->post('/items', $this->itemPayload([
            'icon' => 'file:///etc/passwd',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    // ------------------------------------------------------------------ ALLOW_INTERNAL_REQUESTS opt-out

    public function test_private_ip_is_allowed_when_opt_out_env_is_set(): void
    {
        $this->seed();
        config(['app.allow_internal_requests' => true]);
        putenv('ALLOW_INTERNAL_REQUESTS=true');

        $response = $this->post('/items', $this->itemPayload([
            'icon' => 'http://192.168.1.10:8080/favicon.png',
        ]));

        // With ALLOW_INTERNAL_REQUESTS=true the guard is bypassed entirely.
        // The request may still fail at the fetch stage (no real server), but
        // it must NOT be rejected by the SSRF guard itself.
        $response->assertJsonMissingValidationErrors('file');

        putenv('ALLOW_INTERNAL_REQUESTS=false');
    }
}
