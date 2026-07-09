<?php

namespace Tests\Feature;

use App\Item;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeleteItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_a_user_hard_deletes_only_that_users_items(): void
    {
        $this->seed();

        // Admin (id 1, passwordless) is the only user allowed to manage users.
        $this->withSession(['current_user' => User::find(1)]);

        $victim = User::factory()->create();

        // The victim's own tile and tag should both be hard-deleted.
        $victimTile = Item::factory()->create([
            'title' => 'Victim Tile',
            'type' => 0,
            'user_id' => $victim->id,
        ]);
        $victimTag = Item::factory()->create([
            'title' => 'Victim Tag',
            'type' => 1,
            'user_id' => $victim->id,
        ]);

        // A shared item and another user's item must be left untouched.
        $sharedItem = Item::factory()->create([
            'title' => 'Shared Item',
            'type' => 0,
            'user_id' => 0,
        ]);
        $adminItem = Item::factory()->create([
            'title' => 'Admin Item',
            'type' => 0,
            'user_id' => 1,
        ]);

        $response = $this->delete(route('users.destroy', $victim->id));

        $response->assertRedirect(route('dash'));

        // Victim and their items are gone entirely (force-deleted, not soft-deleted).
        $this->assertDatabaseMissing('users', ['id' => $victim->id]);
        $this->assertNull(Item::withoutGlobalScopes()->withTrashed()->find($victimTile->id));
        $this->assertNull(Item::withoutGlobalScopes()->withTrashed()->find($victimTag->id));

        // Shared and other users' items survive.
        $this->assertNotNull(Item::withoutGlobalScopes()->find($sharedItem->id));
        $this->assertNotNull(Item::withoutGlobalScopes()->find($adminItem->id));
    }

    public function test_user_id_one_cannot_be_deleted(): void
    {
        $this->seed();

        $this->withSession(['current_user' => User::find(1)]);

        $adminItem = Item::factory()->create([
            'title' => 'Admin Item',
            'type' => 0,
            'user_id' => 1,
        ]);

        $this->delete(route('users.destroy', 1));

        // The admin and their items remain.
        $this->assertDatabaseHas('users', ['id' => 1]);
        $this->assertNotNull(Item::withoutGlobalScopes()->find($adminItem->id));
    }
}
