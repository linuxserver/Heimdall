<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ItemExportTest extends TestCase
{

    use RefreshDatabase;

    public function test_returns_empty_jsonarray_when_there_are_no_items_in_the_db(): void
    {
        $response = $this->get('api/item');

        $response->assertJsonCount(0);
    }

    public function test_returns_exactly_the_defined_fields(): void
    {
        $exampleItem = [
            "appdescription" => "Description",
            "appid" => "123",
            "colour" => "#000",
            "description" => "Description",
            "pinned" => 1,
            "title" => "Item Title",
            "url" => "http://gorczany.com/nihil-rerum-distinctio-voluptate-assumenda-accusantium-exercitationem"
        ];
        // The DB column is "order"; it is exported under the "pinned_order" key.
        Item::factory()
            ->create($exampleItem + ["order" => 0]);

        $response = $this->get('api/item');

        $response->assertExactJson([$exampleItem + ["pinned_order" => 0, "tags" => []]]);
    }

    public function test_exports_pinned_status_for_items(): void
    {
        $pinnedItem = Item::factory()->create([
            'title' => 'Pinned App', 
            'pinned' => 1, 
        ]);
        $unpinnedItem = Item::factory()->create([
            'title' => 'Unpinned App',
            'pinned' => 0,
        ]);

        $response = $this->get('api/item');

        $response->assertJsonCount(2);
        $response->assertJsonPath('0.pinned', 1);
        $response->assertJsonPath('1.pinned', 0);
    }

    public function test_exports_pinned_order_for_items(): void
    {
        Item::factory()->create(['title' => 'First',  'order' => 1]);
        Item::factory()->create(['title' => 'Second', 'order' => 2]);

        $response = $this->get('api/item');

        $response->assertJsonCount(2);
        $response->assertJsonPath('0.pinned_order', 1);
        $response->assertJsonPath('1.pinned_order', 2);
    }

    public function test_exports_assigned_tag_titles_excluding_the_root_tag(): void
    {
        // Mirror the root/default dashboard row that production seeds (id 0),
        // so the item_tag pivot's foreign key to items.id is satisfied on a
        // fresh in-memory database.
        DB::table('items')->insert([
            'id' => 0,
            'title' => 'app.dashboard',
            'url' => '',
            'type' => 1,
            'user_id' => 0,
            'pinned' => 0,
        ]);

        $item = Item::factory()
            ->create([
                'title' => 'Tagged Item',
            ]);
        $tag = Item::factory()
            ->create([
                'type' => 1,
                'title' => 'Media',
            ]);

        // Assign both the root/default dashboard (id 0) and the Media tag.
        $item->parents()->sync([0, $tag->id]);

        $response = $this->get('api/item');

        $response->assertJsonCount(1);
        $response->assertJsonPath('0.tags', ['Media']);
    }

    public function test_returns_all_items(): void
    {
        Item::factory()
            ->count(3)
            ->create();

        $response = $this->get('api/item');

        $response->assertJsonCount(3);
    }

    public function test_does_not_return_deleted_item(): void
    {
        Item::factory()
            ->create([
                'deleted_at' => Date::create('1970')
            ]);
        Item::factory()
            ->create();

        $response = $this->get('api/item');

        $response->assertJsonCount(1);
    }

    public function test_does_not_return_tags(): void
    {
        Item::factory()
            ->create([
                'type' => 1
            ]);
        Item::factory()
            ->create();

        $response = $this->get('api/item');

        $response->assertJsonCount(1);
    }
}
