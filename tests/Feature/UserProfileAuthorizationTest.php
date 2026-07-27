<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_another_users_profile(): void
    {
        $userA = User::factory()->patient()->create();
        $userB = User::factory()->patient()->create();

        $this->actingAs($userA)
            ->get(route('user.edit', $userB->id))
            ->assertForbidden();
    }

    public function test_user_cannot_access_another_users_password_page(): void
    {
        $userA = User::factory()->patient()->create();
        $userB = User::factory()->patient()->create();

        $this->actingAs($userA)
            ->get(route('user.password-edit', $userB->id))
            ->assertForbidden();
    }

    public function test_user_can_access_their_own_profile(): void
    {
        $user = User::factory()->patient()->create();

        $this->actingAs($user)
            ->get(route('user.edit', $user->id))
            ->assertOk();
    }
}
