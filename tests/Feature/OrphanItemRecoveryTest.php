<?php

namespace Tests\Feature;

use App\Item;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrphanItemRecoveryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * RefreshDatabase already runs every migration up-front, so the orphans
     * are created afterwards and the data migration is invoked directly.
     */
    public function test_reassigns_orphaned_items_but_leaves_shared_and_valid_items(): void
    {
        $this->seed();

        // A surviving, valid owner.
        $validOwner = User::factory()->create();

        // Orphan: user_id references a user that does not exist.
        $orphan = Item::factory()->create([
            'title' => 'Orphaned Item',
            'user_id' => 999,
        ]);

        // Shared items (user_id = 0) must never be reassigned.
        $shared = Item::factory()->create([
            'title' => 'Shared Item',
            'user_id' => 0,
        ]);

        // A validly-owned item must be left untouched.
        $valid = Item::factory()->create([
            'title' => 'Valid Item',
            'user_id' => $validOwner->id,
        ]);

        $migration = include database_path('migrations/2026_07_09_120000_reassign_orphaned_items.php');
        $migration->up();

        // Orphan reassigned to the surviving admin (id 1).
        $this->assertSame(1, (int) Item::withoutGlobalScopes()->find($orphan->id)->user_id);
        // Shared and valid rows unchanged.
        $this->assertSame(0, (int) Item::withoutGlobalScopes()->find($shared->id)->user_id);
        $this->assertSame($validOwner->id, (int) Item::withoutGlobalScopes()->find($valid->id)->user_id);
    }
}
