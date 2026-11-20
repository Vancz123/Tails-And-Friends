<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guests_are_redirected_from_the_user_management_page(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_regular_users_cannot_access_or_change_user_roles(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->put(route('admin.users.update-role', $otherUser), [
            'role' => 'admin',
        ])->assertForbidden();

        $this->assertSame('user', $otherUser->fresh()->role);
    }

    public function test_administrators_can_view_users_and_promote_or_demote_another_user(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($user->email);

        $this->put(route('admin.users.update-role', $user), [
            'role' => 'admin',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('admin', $user->fresh()->role);

        $this->put(route('admin.users.update-role', $user), [
            'role' => 'user',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('user', $user->fresh()->role);
    }

    public function test_administrators_cannot_demote_themselves(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)->put(route('admin.users.update-role', $admin), [
            'role' => 'user',
        ])->assertSessionHasErrors('role');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_role_updates_reject_unknown_roles(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $user = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.users.update-role', $user), [
            'role' => 'owner',
        ])->assertSessionHasErrors('role');

        $this->assertSame('user', $user->fresh()->role);
    }
}
