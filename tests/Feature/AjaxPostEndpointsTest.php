<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-end coverage for the AJAX POST endpoints that the dashboard relies
 * on (the routes excluded from CSRF verification: order / appload).
 *
 * CSRF itself is disabled while running unit tests, so these tests deliberately
 * do NOT assert "an un-tokened POST succeeds" (that would be meaningless).
 * Instead they exercise the controller + routing end-to-end with realistic
 * input and assert the real, observable behaviour, which is what would break
 * if the controller or router regressed on a framework upgrade.
 */
class AjaxPostEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_endpoint_persists_the_new_item_order(): void
    {
        $this->seed();

        $first = Item::factory()->create(['order' => 5]);
        $second = Item::factory()->create(['order' => 9]);

        // POST the ids in reverse: index 0 => $second, index 1 => $first.
        $response = $this->post('/order', [
            'order' => [$second->id, $first->id],
        ]);

        $response->assertStatus(200);

        $this->assertSame(0, (int) $second->fresh()->order);
        $this->assertSame(1, (int) $first->fresh()->order);
    }

    public function test_order_endpoint_reorders_categories_including_the_home_tag(): void
    {
        $this->seed();

        // The seeder creates the home dashboard tag at id 0; it is a valid
        // category and must not be dropped as a "falsy" id.
        $media = Item::factory()->create(['title' => 'Media', 'type' => 1, 'order' => 0]);
        $work = Item::factory()->create(['title' => 'Work', 'type' => 1, 'order' => 1]);

        $response = $this->post('/order', [
            'order' => [$work->id, '0', $media->id],
        ]);

        $response->assertStatus(200);

        $this->assertSame(0, (int) $work->fresh()->order);
        $this->assertSame(1, (int) Item::find(0)->order);
        $this->assertSame(2, (int) $media->fresh()->order);
    }

    public function test_order_endpoint_skips_ids_that_no_longer_exist(): void
    {
        $this->seed();

        $first = Item::factory()->create(['order' => 5]);
        $second = Item::factory()->create(['order' => 9]);

        $response = $this->post('/order', [
            'order' => [$second->id, 999999, $first->id],
        ]);

        $response->assertStatus(200);

        // Positions are taken from the posted list, so the missing id leaves
        // a gap rather than shifting the items after it.
        $this->assertSame(0, (int) $second->fresh()->order);
        $this->assertSame(2, (int) $first->fresh()->order);
    }

    public function test_appload_returns_null_for_the_none_selection(): void
    {
        $this->seed();

        $response = $this->post('/appload', ['app' => 'null']);

        $response->assertStatus(200);
        $this->assertSame('', $response->getContent());
    }

    public function test_appload_surfaces_a_not_found_error_for_an_unknown_app(): void
    {
        $this->seed();

        $response = $this->post('/appload', ['app' => 'this-app-does-not-exist']);

        // For an unknown app the controller returns a genuine 404 JSON
        // response. appload() is declared to return
        // JsonResponse|string|null, so the JsonResponse is served as-is
        // (correct status + JSON body) rather than being coerced through
        // Response::__toString() into a raw HTTP message served as a 200.
        $response->assertStatus(404);
        $response->assertExactJson(['error' => 'Application not found.']);
    }
}
