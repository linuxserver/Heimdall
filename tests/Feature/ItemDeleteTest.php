<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_an_item(): void
    {
        $this->seed();
        $item = Item::factory()
            ->create([
                'title' => 'Item A',
            ]);

        $response = $this->post('/items/'.$item->id, ['_method' => 'DELETE']);

        $response->assertStatus(302);
    }

    public function test_redirects_to_item_list_page_when_deleting_an_item(): void
    {
        $this->seed();
        $item = Item::factory()
            ->create([
                'title' => 'Item A',
            ]);

        $response = $this->post('/items/'.$item->id, ['_method' => 'DELETE']);

        $response->assertStatus(302);
        $response->assertSee('Redirecting to http://localhost/items');
    }
}
