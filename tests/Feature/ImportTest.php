<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_page_loads_successfully(): void
    {
        $response = $this->get(route('items.import'));

        $response->assertStatus(200);
        $response->assertSee('Import');
    }
}