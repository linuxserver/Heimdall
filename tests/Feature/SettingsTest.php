<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_displays_the_settings_page(): void
    {
        $this->seed();

        $response = $this->get('/settings');

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'Version',
            'Language',
            'Support',
            'Donate',
            'Background Image',
            'Trianglify',
            'Trianglify Random Seed',
            'Homepage Search',
            'Default Search Provider',
            'Link opens in',
            'Custom CSS',
            'Custom JavaScript',
        ]);
    }
}
