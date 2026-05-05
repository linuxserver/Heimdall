<?php

namespace App\Services;

interface OidcUserRepoContract
{
    public function findByUsername(string $username): ?object;
    public function create(string $username, string $email): object;
}
