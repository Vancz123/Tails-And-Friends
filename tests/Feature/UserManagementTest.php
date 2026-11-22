<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\CharacterVerification;
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

    public function test_administrators_can_delete_another_user(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee(route('admin.users.destroy', $user), false);

        $this->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_administrators_cannot_delete_their_own_account_from_the_user_list(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_regular_users_cannot_delete_other_users(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.users.destroy', $otherUser))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $otherUser->id]);
    }

    public function test_deleting_a_user_removes_their_verifications(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $user = User::factory()->create();
        $character = Character::create(['name' => 'Tails']);
        CharacterVerification::create([
            'user_id' => $user->id,
            'character_id' => $character->id,
            'character_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseCount('character_verifications', 0);
    }
}
