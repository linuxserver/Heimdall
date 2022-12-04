<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_displays_the_item_create_page()
    {
        $this->seed();

        $response = $this->get('/items/create');

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_display_the_home_dashboard_tag()
    {
        $this->seed();

        $response = $this->get('/items/create');

        $response->assertSee('Home dashboard');
    }
}
