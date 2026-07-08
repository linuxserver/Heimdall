<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fixture app whose live stats rendering throws, mirroring the Komga
 * failure from issue #1558 (broken remote blade / upstream API error).
 */
class ThrowingStatApp
{
    public $config;

    public function livestats()
    {
        throw new \Exception('boom');
    }
}

/**
 * Fixture app whose live stats rendering succeeds and returns the JSON
 * string the frontend expects.
 */
class HappyStatApp
{
    public $config;

    public function livestats()
    {
        return json_encode(['status' => 'active', 'html' => '<b>ok</b>']);
    }
}

class GetStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_item_id_does_not_500(): void
    {
        $response = $this->get('get_stats/999999');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'inactive', 'html' => '']);
    }

    public function test_throwing_app_degrades_gracefully(): void
    {
        $item = Item::factory()->create([
            'class' => ThrowingStatApp::class,
        ]);

        $response = $this->get('get_stats/'.$item->id);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'inactive', 'html' => '']);
    }

    public function test_item_with_no_class_degrades_gracefully(): void
    {
        $item = Item::factory()->create([
            'class' => null,
        ]);

        $response = $this->get('get_stats/'.$item->id);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'inactive', 'html' => '']);
    }

    public function test_happy_path_returns_livestats_output_verbatim(): void
    {
        $item = Item::factory()->create([
            'class' => HappyStatApp::class,
        ]);

        $expected = json_encode(['status' => 'active', 'html' => '<b>ok</b>']);

        $response = $this->get('get_stats/'.$item->id);

        $response->assertStatus(200);
        $this->assertSame($expected, $response->getContent());
        $response->assertJson(['status' => 'active', 'html' => '<b>ok</b>']);
    }
}
