<?php

namespace Tests\Feature;

use App\Item;
use App\ItemTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helpers
     */

    private function addPinnedItemWithTitleToDB($title)
    {
        $item = Item::factory()
            ->create([
                'title' => $title,
                'pinned' => 1,
            ]);

        ItemTag::factory()->create([
            'item_id' => $item->id,
            'tag_id' => 0,
        ]);
    }

    private function addTagWithTitleToDB($title)
    {
        Item::factory()
            ->create([
                'title' => $title,
                'type' => 1,
            ]);
    }

    /**
     * Test Cases
     */

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
}
