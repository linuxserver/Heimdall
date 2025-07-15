<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_redirects_correctly(): void
    {
        $provider = 'google'; // Example provider
        $query = 'test'; // Example search term

        $response = $this->get(route('search', ['provider' => $provider, 'q' => $query]));

        $response->assertStatus(302);

        $expectedUrl = 'https://www.google.com/search?q=' . urlencode($query);
        $response->assertRedirect($expectedUrl);
    }

    public function test_search_page_with_invalid_provider(): void
    {
        $provider = 'invalid_provider'; // Invalid provider
        $query = 'test'; // Example search term

        $response = $this->get(route('search', ['provider' => $provider, 'q' => $query]));

        $response->assertStatus(404); // Assert that the response status is 404
    }

    public function test_search_page_without_query_parameter(): void
    {
        $provider = 'google'; // Example provider

        $response = $this->get(route('search', ['provider' => $provider]));

        $response->assertStatus(400); // Assert that the response status is 400 (Bad Request)
    }

    public function test_search_page_with_empty_query(): void
    {
        $provider = 'google'; // Example provider
        $query = ''; // Empty search term

        $response = $this->get(route('search', ['provider' => $provider, 'q' => $query]));

        $response->assertStatus(400); // Assert that the response status is 400 (Bad Request)
    }
}