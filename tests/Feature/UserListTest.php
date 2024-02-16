<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    protected function addUserWithNameToDB($name)
    {
        User::factory()
            ->create([
                'username' => $name
            ]);
    }

    public function test_displays_admin_on_user_list_page_when_default_install(): void
    {
        $this->seed();

        $response = $this->get('/users');

        $response->assertStatus(200);
        $response->assertSee('admin');
    }

    public function test_displays_users_on_user_list_page(): void
    {
        $this->seed();

        $this->addUserWithNameToDB('User 1');

        $response = $this->get('/users');

        $response->assertStatus(200);
        $response->assertSee('User 1');
    }
}
