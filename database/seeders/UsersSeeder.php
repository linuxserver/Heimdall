<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Groups
        if (!User::find(1)) {
            $user = new User;
            $user->username = 'admin';
            $user->email = 'admin@test.com';
            $user->password = null;
            $user->save();

            $user_id = $user->id;

            if ($user_id != 1) {
                Log::info("First User returned with id $user_id from db! Changing to 1.");

                DB::update('update users set id = 1 where id = ?', [$user_id]);
            }
        }
    }
}
