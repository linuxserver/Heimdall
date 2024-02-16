<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_displays_the_item_create_page(): void
    {
        $this->seed();

        $response = $this->get('/items/create');

        $response->assertStatus(200);
    }

    public function test_display_the_home_dashboard_tag(): void
    {
        $this->seed();

        $response = $this->get('/items/create');

        $response->assertSee('Home dashboard');
    }

    public function test_creates_a_new_item(): void
    {
        $this->seed();
        $item = [
            'pinned' => 1,
            'appid' => 'null',
            'website' => null,
            'title' => 'Item A',
            'colour' => '#00f',
            'url' => 'http://10.0.1.1',
            'tags' => [0],
        ];

        $response = $this->post('/items', $item);

        $response->assertStatus(302);
        $response->assertSee('Redirecting to');
    }

    public function test_redirects_to_dash_when_adding_a_new_item(): void
    {
        $this->seed();
        $item = [
            'pinned' => 1,
            'appid' => 'null',
            'website' => null,
            'title' => 'Item A',
            'colour' => '#00f',
            'url' => 'http://10.0.1.1',
            'tags' => [0],
        ];

        $response = $this->post('/items', $item);

        $response->assertStatus(302);
        $response->assertSee('Redirecting to http://localhost');
    }
}
