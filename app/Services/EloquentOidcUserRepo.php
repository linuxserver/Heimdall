<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EloquentOidcUserRepo implements OidcUserRepoContract
{
    public function findByUsername(string $username): ?object
    {
        return User::where('username', $username)->first();
    }

    public function create(string $username, string $email): object
    {
        $user = new User();
        $user->username = $username;
        $user->email = $email ?: ($username . '@local.invalid');
        // Random unguessable password — never used for login.
        $user->password = Hash::make(Str::random(64));
        $user->avatar = 'avatars/null.png';
        $user->save();
        return $user;
    }
}
