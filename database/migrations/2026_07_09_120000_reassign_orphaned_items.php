<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Reassign items whose user_id references a user that no longer exists to a
     * surviving user, so previously orphaned tiles and tags become visible again.
     * user_id = 0 means "shared with all users" and is never treated as orphaned.
     *
     * The DB query builder is used directly (not the Item model) so the global
     * scope and soft-delete constraints don't interfere.
     */
    public function up(): void
    {
        // Prefer user id 1 if it still exists, otherwise the lowest existing id.
        $target = DB::table('users')->where('id', 1)->value('id')
            ?? DB::table('users')->min('id');

        // No users left: nothing to reassign to.
        if ($target === null) {
            return;
        }

        DB::table('items')
            ->where('user_id', '!=', 0)
            ->whereNotIn('user_id', function ($query) {
                $query->select('id')->from('users');
            })
            ->update(['user_id' => $target]);
    }

    /**
     * Reverse the migrations.
     *
     * This is a data migration; the original ownership cannot be recovered.
     */
    public function down(): void
    {
        //
    }
};
