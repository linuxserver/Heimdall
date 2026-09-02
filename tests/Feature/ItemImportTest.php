<?php

namespace Tests\Feature;

use App\Item;
use App\ItemTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function importPayload(array $overrides = []): array
    {
        return array_merge([
            'pinned' => 1,
            'appid' => 'null',
            'website' => null,
            'title' => 'Item A',
            'colour' => '#00f',
            'url' => 'http://10.0.1.1',
            'tags' => [0],
        ], $overrides);
    }

    public function test_import_creates_and_assigns_a_tag_from_its_title(): void
    {
        $this->seed();

        $response = $this->postJson('api/item', $this->importPayload([
            'title' => 'Item A',
            'tags' => ['Media'],
        ]));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);

        $tag = Item::where('type', 1)->where('title', 'Media')->first();
        $this->assertNotNull($tag);
        $this->assertSame(1, (int) $tag->type);

        $item = Item::where('type', 0)->where('title', 'Item A')->first();
        $this->assertNotNull($item);

        $this->assertTrue(
            ItemTag::where('item_id', $item->id)->where('tag_id', $tag->id)->exists()
        );
    }

    public function test_import_reuses_an_existing_tag_for_the_same_title(): void
    {
        $this->seed();

        $this->postJson('api/item', $this->importPayload([
            'title' => 'Item A',
            'tags' => ['Media'],
        ]))->assertStatus(200);

        $this->postJson('api/item', $this->importPayload([
            'title' => 'Item B',
            'tags' => ['Media'],
        ]))->assertStatus(200);

        $this->assertSame(
            1,
            Item::where('type', 1)->where('title', 'Media')->count()
        );

        $tag = Item::where('type', 1)->where('title', 'Media')->first();
        $itemA = Item::where('type', 0)->where('title', 'Item A')->first();
        $itemB = Item::where('type', 0)->where('title', 'Item B')->first();

        $this->assertTrue(
            ItemTag::where('item_id', $itemA->id)->where('tag_id', $tag->id)->exists()
        );
        $this->assertTrue(
            ItemTag::where('item_id', $itemB->id)->where('tag_id', $tag->id)->exists()
        );
    }

    public function test_import_with_root_tag_only_creates_no_tags(): void
    {
        $this->seed();

        $response = $this->postJson('api/item', $this->importPayload([
            'title' => 'Item A',
            'tags' => [0],
        ]));

        $response->assertStatus(200);

        // No stray tag items should have been created beyond the seeded
        // root/default dashboard tag (id 0).
        $this->assertSame(0, Item::where('type', 1)->where('id', '>', 0)->count());

        // The item should be assigned to the root/default dashboard (tag id 0).
        $item = Item::where('type', 0)->where('title', 'Item A')->first();
        $this->assertNotNull($item);
        $this->assertTrue(
            ItemTag::where('item_id', $item->id)->where('tag_id', 0)->exists()
        );
    }

    public function test_import_saves_unpinned_item(): void
    {
        $this->seed();

        $response = $this->postJson('api/item', $this->importPayload([
            'title' => 'Unpinned App',
            'pinned' => 0,
        ]));

        $response->assertStatus(200);

        $item = Item::where('type', 0)->where('title', 'Unpinned App')->first();
        $this->assertNotNull($item);
        $this->assertEquals(0, $item->pinned);
    }

    public function test_import_saves_pinned_order(): void
    {
        $this->seed();

        $response = $this->postJson('api/item', $this->importPayload([
            'title'  => 'Ordered App',
            'order'  => 5,
        ]));

        $response->assertStatus(200);

        $item = Item::where('type', 0)->where('title', 'Ordered App')->first();
        $this->assertNotNull($item);
        $this->assertEquals(5, $item->order);
    }

    public function test_import_defaults_order_to_zero_when_not_provided(): void
    {
        $this->seed();

        $response = $this->postJson('api/item', $this->importPayload([
            'title' => 'No Order App',
        ]));

        $response->assertStatus(200);

        $item = Item::where('type', 0)->where('title', 'No Order App')->first();
        $this->assertNotNull($item);
        $this->assertEquals(0, $item->order);
    }

    public function test_export_import_round_trip_preserves_pinned_and_order(): void
    {
        $this->seed();

        // Create items with specific pinned/order values
        $this->postJson('api/item', $this->importPayload([
            'title'  => 'App One',
            'pinned' => 1,
            'order'  => 3,
        ]))->assertStatus(200);

        $this->postJson('api/item', $this->importPayload([
            'title'  => 'App Two',
            'pinned' => 0,
            'order'  => 7,
        ]))->assertStatus(200);

        // Export
        $export = $this->get('api/item');
        $export->assertJsonCount(2);

        $exported = $export->json();

        // Verify the exported JSON has the right keys/values
        $appOne = collect($exported)->firstWhere('title', 'App One');
        $appTwo = collect($exported)->firstWhere('title', 'App Two');

        $this->assertEquals(1, $appOne['pinned']);
        $this->assertEquals(3, $appOne['pinned_order']);
        $this->assertEquals(0, $appTwo['pinned']);
        $this->assertEquals(7, $appTwo['pinned_order']);
    }
}
