<?php

namespace Tests\Feature;

use App\Item;
use App\ItemTag;
use App\Setting;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GET /search/items feeds the quick launcher (Cmd/Ctrl+K).
 */
class SearchItemsTest extends TestCase
{
    use RefreshDatabase;

    private function addItem(array $attributes = []): Item
    {
        $item = Item::factory()->create($attributes);

        ItemTag::factory()->create([
            'item_id' => $item->id,
            'tag_id' => 0,
        ]);

        return $item;
    }

    private function itemTitles($response): array
    {
        return array_column($response->json('items'), 'title');
    }

    public function test_lists_pinned_and_unpinned_apps(): void
    {
        $this->seed();
        $this->addItem(['title' => 'Pinned app', 'pinned' => 1]);
        $this->addItem(['title' => 'Unpinned app', 'pinned' => 0]);

        $response = $this->getJson('/search/items');

        $response->assertOk();
        $this->assertEqualsCanonicalizing(['Pinned app', 'Unpinned app'], $this->itemTitles($response));
    }

    public function test_leaves_out_tags(): void
    {
        $this->seed();
        $this->addItem(['title' => 'An app']);
        Item::factory()->create(['title' => 'A tag', 'type' => 1]);

        $this->assertSame(['An app'], $this->itemTitles($this->getJson('/search/items')));
    }

    public function test_includes_tag_titles_but_not_the_home_dashboard(): void
    {
        $this->seed();
        $tag = Item::factory()->create(['title' => 'Media', 'type' => 1]);
        $item = $this->addItem(['title' => 'An app']);
        ItemTag::factory()->create(['item_id' => $item->id, 'tag_id' => $tag->id]);

        $this->assertSame(['Media'], $this->getJson('/search/items')->json('items.0.tags'));
    }

    public function test_leaves_out_other_users_apps(): void
    {
        $this->seed();
        $other = User::factory()->create();
        $this->addItem(['title' => 'Mine']);
        $this->addItem(['title' => 'Theirs', 'user_id' => $other->id]);

        $this->assertSame(['Mine'], $this->itemTitles($this->getJson('/search/items')));
    }

    public function test_requires_login_for_a_password_protected_user(): void
    {
        $this->seed();
        $this->addItem(['title' => 'Secret']);
        $user = User::find(1);
        $user->update(['password' => bcrypt('secret')]);
        // The session user was cached while booting, before the password was set
        $this->withSession(['current_user' => $user->fresh()]);

        $this->get('/search/items')->assertRedirect(route('login'));
        $this->getJson('/search/items')->assertUnauthorized();
    }

    public function test_roles_mode_only_lists_apps_for_the_callers_roles(): void
    {
        $this->seed();
        config([
            'app.auth_roles_enable' => true,
            'app.auth_roles_header' => 'X-Roles',
            'app.auth_roles_delimiter' => ',',
        ]);
        $this->addItem(['title' => 'For admins', 'role' => 'admin']);
        $this->addItem(['title' => 'For family', 'role' => 'family']);

        $response = $this->getJson('/search/items', ['X-Roles' => 'family,guest']);

        $this->assertSame(['For family'], $this->itemTitles($response));
    }

    public function test_launcher_is_on_the_dashboard_but_not_the_login_page(): void
    {
        $this->seed();

        $this->get('/')->assertSee('id="quicklauncher"', false);
        $this->get('/login')->assertOk()->assertDontSee('id="quicklauncher"', false);
    }

    public function test_public_dashboard_hides_the_launcher_until_login(): void
    {
        $this->seed();
        $user = User::find(1);
        $user->forceFill(['password' => bcrypt('secret'), 'public_front' => 1])->save();
        $this->withSession(['current_user' => $user->fresh()]);

        $this->get('/')->assertOk()->assertDontSee('id="quicklauncher"', false);

        $this->actingAs($user)->get('/')->assertSee('id="quicklauncher"', false);
    }

    public function test_roles_mode_lists_nothing_without_a_roles_header(): void
    {
        $this->seed();
        config([
            'app.auth_roles_enable' => true,
            'app.auth_roles_header' => 'X-Roles',
            'app.auth_roles_delimiter' => ',',
        ]);
        $this->addItem(['title' => 'For admins', 'role' => 'admin']);

        $this->assertSame([], $this->itemTitles($this->getJson('/search/items')));
    }

    public function test_icon_urls_match_the_tiles(): void
    {
        $this->seed();
        $this->addItem(['title' => 'With icon', 'icon' => 'supportedapps/radarr.svg']);
        $this->addItem(['title' => 'Without icon', 'icon' => null]);

        $icons = array_column($this->getJson('/search/items')->json('items'), 'icon', 'title');

        $this->assertSame(asset('/storage/icons/radarr.svg'), $icons['With icon']);
        $this->assertSame(asset('/img/heimdall-icon-small.png'), $icons['Without icon']);
    }

    public function test_link_target_other_than_current_is_passed_through(): void
    {
        $this->seed();
        Setting::where('key', 'window_target')->update(['value' => '_blank']);

        $this->assertSame('_blank', $this->getJson('/search/items')->json('target'));
    }

    public function test_link_target_current_maps_to_self(): void
    {
        $this->seed();
        Setting::where('key', 'window_target')->update(['value' => 'current']);

        $this->assertSame('_self', $this->getJson('/search/items')->json('target'));
    }

    public function test_offers_the_homepage_search_provider_as_web_fallback(): void
    {
        $this->seed();
        Setting::where('key', 'homepage_search')->update(['value' => 1]);
        Setting::where('key', 'search_provider')->update(['value' => 'google']);

        $fallback = $this->getJson('/search/items')->json('fallback');

        $this->assertSame('google', $fallback['provider']);
        $this->assertSame(url('search'), $fallback['url']);
    }

    public function test_has_no_web_search_fallback_when_the_provider_is_the_tile_filter(): void
    {
        $this->seed();
        Setting::where('key', 'homepage_search')->update(['value' => 1]);
        Setting::where('key', 'search_provider')->update(['value' => 'tiles']);

        $this->assertNull($this->getJson('/search/items')->json('fallback'));
    }

    public function test_has_no_web_search_fallback_when_homepage_search_is_off(): void
    {
        $this->seed();

        $this->assertNull($this->getJson('/search/items')->json('fallback'));
    }
}
