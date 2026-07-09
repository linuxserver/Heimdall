<?php

namespace Tests\Feature;

use App\Item;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemOwnershipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a passwordless user (so the "allowed" middleware lets the
     * request through) and make it the current session user.
     */
    private function actAsCurrentUser(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'password' => null,
            'public_front' => 1,
        ], $attributes));

        $this->withSession(['current_user' => $user]);

        return $user;
    }

    public function test_creating_an_item_assigns_the_creator_as_owner(): void
    {
        $this->seed();

        $creator = $this->actAsCurrentUser();

        $response = $this->post('/items', [
            'pinned' => 1,
            'appid' => 'null',
            'website' => null,
            'title' => 'Owned Item',
            'colour' => '#00f',
            'url' => 'http://10.0.1.1',
            'tags' => [0],
        ]);

        $response->assertStatus(302);

        $item = Item::withoutGlobalScopes()->where('title', 'Owned Item')->first();
        $this->assertNotNull($item);
        $this->assertSame($creator->id, (int) $item->user_id);
    }

    public function test_creating_an_item_ignores_a_crafted_user_id(): void
    {
        $this->seed();

        $creator = $this->actAsCurrentUser();
        $other = User::factory()->create();

        $response = $this->post('/items', [
            'pinned' => 1,
            'appid' => 'null',
            'title' => 'Crafted Owner Item',
            'colour' => '#00f',
            'url' => 'http://10.0.1.2',
            'user_id' => $other->id, // attempt to create on behalf of another user
            'tags' => [0],
        ]);

        $response->assertStatus(302);

        $item = Item::withoutGlobalScopes()->where('title', 'Crafted Owner Item')->first();
        $this->assertNotNull($item);
        $this->assertSame($creator->id, (int) $item->user_id);
    }

    public function test_updating_a_shared_item_does_not_change_its_owner(): void
    {
        $this->seed();

        // Attacker is a different logged-in user.
        $attacker = $this->actAsCurrentUser();

        // A shared item (user_id = 0) is visible to every user.
        $item = Item::factory()->create([
            'title' => 'Shared Item',
            'user_id' => 0,
        ]);

        $response = $this->patch('/items/'.$item->id, [
            'appid' => 'null',
            'title' => 'Shared Item Edited',
            'url' => 'http://example.test',
            'user_id' => $attacker->id, // crafted mass-assignment attempt
            'tags' => [0],
        ]);

        $response->assertRedirect(route('dash'));

        $fresh = Item::withoutGlobalScopes()->find($item->id);
        // Ownership is unchanged despite the crafted user_id field...
        $this->assertSame(0, (int) $fresh->user_id);
        // ...but the rest of the edit still applied.
        $this->assertSame('Shared Item Edited', $fresh->title);
    }

    public function test_updating_an_owned_item_does_not_change_its_owner(): void
    {
        $this->seed();

        $owner = $this->actAsCurrentUser();

        $item = Item::factory()->create([
            'title' => 'Owned Item',
            'user_id' => $owner->id,
        ]);

        $response = $this->patch('/items/'.$item->id, [
            'appid' => 'null',
            'title' => 'Owned Item Edited',
            'url' => 'http://example.test',
            'user_id' => 999, // crafted mass-assignment attempt
            'tags' => [0],
        ]);

        $response->assertRedirect(route('dash'));

        $fresh = Item::withoutGlobalScopes()->find($item->id);
        $this->assertSame($owner->id, (int) $fresh->user_id);
        $this->assertSame('Owned Item Edited', $fresh->title);
    }
}
