<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
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
            "title" => "Item Title",
            "url" => "http://gorczany.com/nihil-rerum-distinctio-voluptate-assumenda-accusantium-exercitationem"
        ];
        Item::factory()
            ->create($exampleItem);

        $response = $this->get('api/item');

        $response->assertExactJson([(object)$exampleItem]);
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
