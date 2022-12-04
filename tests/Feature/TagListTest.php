<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagListTest extends TestCase
{
    use RefreshDatabase;

    private function addTagWithTitleToDB($title)
    {
        Item::factory()
            ->create([
                'title' => $title,
                'type' => 1,
            ]);
    }

    public function test_displays_the_tags_on_the_tag_list_page()
    {
        $this->addTagWithTitleToDB('Tag 1');
        $this->addTagWithTitleToDB('Tag 2');
        $this->addTagWithTitleToDB('Tag 3');

        $response = $this->get('/tags');

        $response->assertStatus(200);
        $response->assertSee('Tag 1');
        $response->assertSee('Tag 2');
        $response->assertSee('Tag 3');
    }
}
