<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guests_cannot_open_or_submit_the_password_change_form(): void
    {
        $this->get(route('user-password.edit'))
            ->assertRedirect(route('login'));

        $this->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ])->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_the_password_change_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('user-password.edit'))
            ->assertOk()
            ->assertSee('Palavra-passe atual')
            ->assertSee('Nova palavra-passe')
            ->assertSee('Confirmar nova palavra-passe')
            ->assertSee(route('user-password.update'), false);
    }

    public function test_incorrect_current_password_is_rejected_without_changing_the_password(): void
    {
        $user = User::factory()->create();
        $currentHash = $user->password;

        $response = $this->from(route('user-password.edit'))
            ->actingAs($user)
            ->put(route('user-password.update'), [
                'current_password' => 'incorrect-password',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ]);

        $response->assertRedirectToRoute('user-password.edit');
        $response->assertSessionHasErrorsIn('updatePassword', ['current_password']);
        $this->assertSame($currentHash, $user->fresh()->password);
    }

    public function test_new_password_requires_matching_confirmation(): void
    {
        $user = User::factory()->create();
        $currentHash = $user->password;

        $response = $this->from(route('user-password.edit'))
            ->actingAs($user)
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'DifferentPassword789!',
            ]);

        $response->assertRedirectToRoute('user-password.edit');
        $response->assertSessionHasErrorsIn('updatePassword', ['password']);
        $this->assertSame($currentHash, $user->fresh()->password);
    }

    public function test_users_are_logged_out_after_changing_their_password(): void
    {
        $user = User::factory()->create();

        $response = $this->from(route('user-password.edit'))
            ->actingAs($user)
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ]);

        $response->assertRedirectToRoute('login');
        $response->assertSessionHas('status', 'Palavra-passe alterada. Inicia sessão novamente com a nova palavra-passe.');
        $this->assertGuest();

        $updatedPasswordHash = $user->fresh()->password;
        $this->assertSame(true, Hash::check('NewPassword456!', $updatedPasswordHash));
        $this->assertSame(false, Hash::check('password', $updatedPasswordHash));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'NewPassword456!',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }
}
