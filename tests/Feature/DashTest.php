<?php

namespace Tests\Feature;

use App\Item;
use App\ItemTag;
use App\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helpers
     */

    private function addPinnedItemWithTitleToDB($title, $tagId = 0): Item
    {
        $item = Item::factory()
            ->create([
                'title' => $title,
                'pinned' => 1,
            ]);

        ItemTag::factory()->create([
            'item_id' => $item->id,
            'tag_id' => $tagId,
        ]);

        return $item;
    }

    private function addTagWithTitleToDB($title, array $attributes = []): Item
    {
        return Item::factory()
            ->create($attributes + [
                'title' => $title,
                'type' => 1,
            ]);
    }

    public function test_loads_empty_dash(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_displays_items_on_the_dash(): void
    {
        $this->seed();

        $this->addPinnedItemWithTitleToDB('Item 1');
        $this->addPinnedItemWithTitleToDB('Item 2');
        $this->addPinnedItemWithTitleToDB('Item 3');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Item 1');
        $response->assertSee('Item 2');
        $response->assertSee('Item 3');
    }

    public function test_displays_tags_on_the_dash(): void
    {
        $this->seed();

        $this->addTagWithTitleToDB('Tag 1');
        $this->addTagWithTitleToDB('Tag 2');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Tag 1');
        $response->assertSee('Tag 2');
    }

    public function test_dash_exposes_the_configured_default_tag(): void
    {
        $this->seed();

        Setting::where('key', 'treat_tags_as')->update(['value' => 'tags']);
        Setting::where('key', 'default_tag')->update(['value' => 'home-dashboard']);

        $this->addTagWithTitleToDB('Home', ['url' => 'home-dashboard', 'pinned' => 1, 'user_id' => 0]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('data-default-tag="home-dashboard"', false);
    }

    public function test_dash_can_default_to_the_home_dashboard_tag(): void
    {
        $this->seed();

        Setting::where('key', 'treat_tags_as')->update(['value' => 'tags']);
        Setting::where('key', 'default_tag')->update(['value' => '0-dash']);

        $this->addPinnedItemWithTitleToDB('Home Item');

        $response = $this->get('/');

        $response->assertStatus(200);
        // The stored slug matches the chip and the tile class the JS filters on.
        $response->assertSee('data-default-tag="0-dash"', false);
        $response->assertSee('data-tag="tag-0-dash"', false);
        $response->assertSee('class="item-container tag-0-dash"', false);
    }

    public function test_categories_mode_renders_sortable_category_and_item_markup(): void
    {
        $this->seed();

        Setting::where('key', 'treat_tags_as')->update(['value' => 'categories']);

        $tag = $this->addTagWithTitleToDB('Media', ['url' => 'media', 'pinned' => 1, 'user_id' => 0]);
        $app = $this->addPinnedItemWithTitleToDB('Plex', $tag->id);

        $response = $this->get('/');

        $response->assertStatus(200);
        // The category block and the item inside it both carry the id that
        // Sortable posts to /order.
        $response->assertSee('class="category item-containerz" data-name="Media" data-id="' . $tag->id . '"', false);
        $response->assertSee('data-name="Plex" data-id="' . $app->id . '"', false);
        // The item is rendered inside its category block (what the nested
        // per-category sortable relies on), and the title link opts out of
        // native dragging so the category itself is the drag source.
        $html = $response->getContent();
        $categoryPos = strpos($html, 'data-id="' . $tag->id . '"');
        $itemPos = strpos($html, 'data-id="' . $app->id . '"');
        $this->assertLessThan($itemPos, $categoryPos);
        $this->assertStringContainsString('<a draggable="false" href=', $html);
    }
}
