<?php

namespace Tests\Feature;

use App\Item;
use App\ItemTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Reverse-proxy roles mode (AUTH_ROLES_ENABLE).
 *
 * Heimdall has no session of its own in this mode: an upstream proxy
 * authenticates the visitor and forwards their roles in a header. Everything
 * the response contains is therefore decided by that header, so anything the
 * dashboard omits has to be omitted from the surrounding chrome as well.
 */
class DashRolesTest extends TestCase
{
    use RefreshDatabase;

    private const ROLES_HEADER = 'X-Roles';

    private const ROLES_SERVER_KEY = 'HTTP_X_ROLES';

    protected function tearDown(): void
    {
        unset($_SERVER[self::ROLES_SERVER_KEY]);

        parent::tearDown();
    }

    /**
     * Helpers
     */

    /**
     * Turn on roles mode and present the request as coming from a visitor
     * holding $callerRoles.
     */
    private function actingWithRoles(string $callerRoles): void
    {
        config([
            'app.auth_roles_enable' => true,
            'app.auth_roles_header' => self::ROLES_HEADER,
            'app.auth_roles_http_header' => self::ROLES_SERVER_KEY,
            'app.auth_roles_admin' => 'admin',
            'app.auth_roles_delimiter' => ',',
        ]);

        // Under php-fpm the proxy's header lands in $_SERVER, which is where
        // the view composer reads the admin role from. Test requests do not
        // populate the superglobal, so do it here the way the webserver would.
        $_SERVER[self::ROLES_SERVER_KEY] = $callerRoles;
    }

    private function getDashAs(string $callerRoles)
    {
        $this->actingWithRoles($callerRoles);

        return $this->get('/', [self::ROLES_HEADER => $callerRoles]);
    }

    private function addItemForRole(string $title, ?string $role, int $pinned = 1): Item
    {
        $item = Item::factory()->create([
            'title' => $title,
            'role' => $role,
            'pinned' => $pinned,
        ]);

        ItemTag::factory()->create([
            'item_id' => $item->id,
            'tag_id' => 0,
        ]);

        return $item;
    }

    /**
     * The leak: items the visitor has no role for
     */

    public function test_sidenav_pin_list_only_lists_items_the_visitor_has_a_role_for(): void
    {
        $this->seed();

        $this->addItemForRole('Plex', 'media');
        $theirs = $this->addItemForRole('Unifi Controller', 'admin');

        $response = $this->getDashAs('media');

        $response->assertStatus(200);
        $response->assertSee('Plex');
        // The title of an item this visitor cannot see must not appear
        // anywhere in the response, sidenav included.
        $response->assertDontSee('Unifi Controller');
        // Nor may the response hand out a state-changing link for it.
        $response->assertDontSee(route('items.pintoggle', [$theirs->id]), false);
    }

    public function test_sidenav_pin_list_is_empty_of_other_roles_items_when_the_visitor_has_none(): void
    {
        $this->seed();

        $this->addItemForRole('Unifi Controller', 'admin');
        $this->addItemForRole('Nzbget', 'admin');

        $response = $this->getDashAs('guest');

        $response->assertStatus(200);
        $response->assertDontSee('Unifi Controller');
        $response->assertDontSee('Nzbget');
    }

    /**
     * The pin control
     */

    public function test_pin_item_control_is_hidden_from_non_admin_roles(): void
    {
        $this->seed();

        $this->addItemForRole('Plex', 'media');

        $response = $this->getDashAs('media');

        $response->assertStatus(200);
        // #config-buttons is already gated on the admin role; #add-item opens
        // the sidenav pin list, so both it and the list have to be gated with
        // it. Pinning is global state in roles mode, not a per-visitor
        // preference, so it is an admin action however few items are listed.
        $response->assertDontSee('id="config-buttons"', false);
        $response->assertDontSee('id="add-item"', false);
        $response->assertDontSee('id="pinlist"', false);
        $response->assertDontSee('items/pintoggle', false);
    }

    /**
     * Control cases: the admin role must keep everything
     */

    public function test_admin_role_still_sees_every_item_and_the_pin_control(): void
    {
        $this->seed();

        $this->addItemForRole('Plex', 'media');
        $theirs = $this->addItemForRole('Unifi Controller', 'admin');

        $response = $this->getDashAs('admin');

        $response->assertStatus(200);
        $response->assertSee('Unifi Controller');
        $response->assertSee('id="config-buttons"', false);
        $response->assertSee('id="add-item"', false);
        $response->assertSee('id="pinlist"', false);
        $response->assertSee(route('items.pintoggle', [$theirs->id]), false);
    }

    public function test_admin_keeps_the_pin_control_for_items_of_other_roles(): void
    {
        $this->seed();

        $other = $this->addItemForRole('Plex', 'media');

        $response = $this->getDashAs('admin');

        $response->assertStatus(200);
        // The pin list is how items get pinned and unpinned, so an admin needs
        // every item in it and not just the ones carrying the admin role.
        // Role-filtering the list itself would also close the leak, and would
        // quietly take away the admin's ability to unpin anything else.
        $response->assertSee('Plex');
        $response->assertSee(route('items.pintoggle', [$other->id]), false);
    }

    public function test_a_visitor_holding_several_roles_sees_all_of_them(): void
    {
        $this->seed();

        $this->addItemForRole('Plex', 'media');
        $this->addItemForRole('Nzbget', 'downloads');
        $this->addItemForRole('Unifi Controller', 'admin');

        $response = $this->getDashAs('media,downloads');

        $response->assertStatus(200);
        $response->assertSee('Plex');
        $response->assertSee('Nzbget');
        $response->assertDontSee('Unifi Controller');
    }

    /**
     * Control case: roles mode off changes nothing
     */

    public function test_with_roles_disabled_every_item_is_still_listed(): void
    {
        $this->seed();

        $this->addItemForRole('Plex', 'media');
        $this->addItemForRole('Unifi Controller', 'admin');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Plex');
        $response->assertSee('Unifi Controller');
        $response->assertSee('id="add-item"', false);
        $response->assertSee('id="pinlist"', false);
    }
}
