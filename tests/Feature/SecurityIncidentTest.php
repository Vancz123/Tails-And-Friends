<?php

namespace Tests\Feature;

use App\Models\SecurityIncident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityIncidentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guests_are_redirected_to_login_when_deleting_a_security_log(): void
    {
        $owner = User::factory()->create();
        $incident = SecurityIncident::create([
            'user_id' => $owner->id,
            'category' => 'Acesso não autorizado',
            'summary' => 'Evento de teste.',
            'attempt' => 1,
            'blocked' => false,
        ]);

        $this->delete(route('security.incidents.destroy', $incident))
            ->assertRedirect(route('login'));

        $this->assertModelExists($incident);
    }

    public function test_guests_are_redirected_to_login_when_deleting_all_security_logs(): void
    {
        $owner = User::factory()->create();
        $incident = SecurityIncident::create([
            'user_id' => $owner->id,
            'category' => 'Acesso não autorizado',
            'summary' => 'Evento de teste.',
            'attempt' => 1,
            'blocked' => false,
        ]);

        $this->delete(route('security.incidents.destroy-all'))
            ->assertRedirect(route('login'));

        $this->assertModelExists($incident);
    }

    public function test_regular_users_are_forbidden_from_deleting_a_security_log(): void
    {
        $user = User::factory()->create();
        $incident = SecurityIncident::create([
            'user_id' => $user->id,
            'category' => 'Acesso não autorizado',
            'summary' => 'Evento de teste.',
            'attempt' => 1,
            'blocked' => false,
        ]);

        $this->actingAs($user)
            ->delete(route('security.incidents.destroy', $incident))
            ->assertForbidden();

        $this->assertModelExists($incident);
    }

    public function test_regular_users_are_forbidden_from_deleting_all_security_logs(): void
    {
        $user = User::factory()->create();
        $incident = SecurityIncident::create([
            'user_id' => $user->id,
            'category' => 'Acesso não autorizado',
            'summary' => 'Evento de teste.',
            'attempt' => 1,
            'blocked' => false,
        ]);

        $this->actingAs($user)
            ->delete(route('security.incidents.destroy-all'))
            ->assertForbidden();

        $this->assertModelExists($incident);
    }

    public function test_administrators_can_delete_one_security_log_from_the_security_page(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $incident = SecurityIncident::create([
            'user_id' => $admin->id,
            'category' => 'Acesso não autorizado',
            'summary' => 'Evento selecionado.',
            'attempt' => 1,
            'blocked' => false,
        ]);
        $otherIncident = SecurityIncident::create([
            'user_id' => $admin->id,
            'category' => 'Limite de pedidos',
            'summary' => 'Outro evento.',
            'attempt' => 2,
            'blocked' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('security.index'))
            ->assertOk()
            ->assertSee(route('security.incidents.destroy', $incident), false)
            ->assertSee('Apagar registo de segurança #'.$incident->id);

        $this->delete(route('security.incidents.destroy', $incident))
            ->assertRedirect(route('security.index'))
            ->assertSessionHas('status', 'Registo de segurança removido.');

        $this->assertModelMissing($incident);
        $this->assertModelExists($otherIncident);
    }

    public function test_administrators_can_delete_all_security_logs_including_records_beyond_the_page_limit(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        for ($attempt = 1; $attempt <= 31; $attempt++) {
            SecurityIncident::create([
                'user_id' => $admin->id,
                'category' => 'Acesso não autorizado',
                'summary' => 'Evento de teste.',
                'attempt' => $attempt,
                'blocked' => false,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('security.index'))
            ->assertOk()
            ->assertSee(route('security.incidents.destroy-all'), false)
            ->assertSee('Apagar todos')
            ->assertSee('class="button button-primary button-small"', false);

        $this->delete(route('security.incidents.destroy-all'))
            ->assertRedirect(route('security.index'))
            ->assertSessionHas('status', 'Número de registos de segurança removidos: 31.');

        $this->assertDatabaseCount('security_incidents', 0);
    }
}
