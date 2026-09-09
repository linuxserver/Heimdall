<?php

namespace Tests\Feature;

use App\Item;
use App\ItemTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Roles mode with the roles header absent.
 *
 * The header is supplied by the reverse proxy, so it is missing whenever
 * something reaches Heimdall without going through it: a healthcheck on the
 * container port, a probe on the published port, a proxy that has not been
 * configured yet, or a request the proxy passes through unauthenticated.
 * None of those should take the dashboard down.
 */
class RolesHeaderMissingTest extends TestCase
{
    use RefreshDatabase;

    private const ROLES_HEADER = 'X-Roles';

    private const ROLES_SERVER_KEY = 'HTTP_X_ROLES';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.auth_roles_enable' => true,
            'app.auth_roles_header' => self::ROLES_HEADER,
            'app.auth_roles_http_header' => self::ROLES_SERVER_KEY,
            'app.auth_roles_admin' => 'admin',
            'app.auth_roles_delimiter' => ',',
        ]);

        // Nothing set the header: this is the state the bug is about.
        unset($_SERVER[self::ROLES_SERVER_KEY]);
    }

    public function test_dash_still_renders_when_the_roles_header_is_absent(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_a_request_without_the_roles_header_gets_no_admin_controls(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200);
        // Degrading has to fail closed. An absent header is no roles at all,
        // never the admin role.
        $response->assertDontSee('id="config-buttons"', false);
    }

    public function test_a_request_without_the_roles_header_gets_no_dashboard_tiles(): void
    {
        $this->seed();

        $item = Item::factory()->create([
            'title' => 'Unifi Controller',
            'role' => 'admin',
            'pinned' => 1,
        ]);
        ItemTag::factory()->create(['item_id' => $item->id, 'tag_id' => 0]);

        $response = $this->get('/');

        $response->assertStatus(200);
        // No roles means no tiles, rather than an error page or every tile.
        $response->assertDontSee('data-name="Unifi Controller"', false);
    }

    public function test_the_admin_role_is_still_honoured_when_the_header_is_present(): void
    {
        $this->seed();

        $_SERVER[self::ROLES_SERVER_KEY] = 'admin';

        $response = $this->get('/', [self::ROLES_HEADER => 'admin']);

        $response->assertStatus(200);
        $response->assertSee('id="config-buttons"', false);

        unset($_SERVER[self::ROLES_SERVER_KEY]);
    }
}
