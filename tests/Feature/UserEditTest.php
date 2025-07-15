<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_edit_page_loads_successfully(): void
    {
        $this->seed();

        $user = User::factory()->create();

        $response = $this->get(route('users.edit', $user->id));

        $response->assertStatus(200);
        $response->assertSee($user->username); // Verify username is displayed
        $response->assertSee(__('app.user.email')); // Verify email field is present
    }

    public function test_user_can_be_updated(): void
    {
        $this->seed();

        $user = User::factory()->create([
            'username' => 'oldusername',
            'email' => 'oldemail@example.com',
        ]);

        $data = [
            'username' => 'newusername',
            'email' => 'newemail@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'public_front' => 1, // Include public_front in the test data
        ];

        $response = $this->patch(route('users.update', $user->id), $data);

        $response->assertRedirect(route('dash'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'newusername',
            'email' => 'newemail@example.com',
        ]);
    }
}