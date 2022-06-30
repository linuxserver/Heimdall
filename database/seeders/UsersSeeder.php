<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Groups
        if (! $user = User::find(1)) {
            $user = new User;
            $user->id = 1;
            $user->username = 'admin';
            $user->email = 'admin@test.com';
            $user->password = null;
            $user->save();
        } else {
            //$user->save();
        }
    }
}
