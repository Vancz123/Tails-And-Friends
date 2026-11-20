<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\CharacterGameVerification;
use App\Models\CharacterVerification;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CharacterVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guests_are_redirected_from_character_verifications(): void
    {
        $character = Character::create(['name' => 'Tails']);

        $this->put(route('characters.verifications.update', $character), [
            'fact' => 'character',
            'checked' => 1,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('character_verifications', 0);
    }

    public function test_authenticated_users_can_verify_a_character_and_its_debut(): void
    {
        $user = User::factory()->create();
        $character = Character::create(['name' => 'Tails']);
        $debut = Game::create(['name' => 'Tails Debut', 'release_date' => '1992-11-21']);
        $character->update(['debut_game_id' => $debut->id]);
        $character->games()->attach($debut);

        $this->actingAs($user)->put(route('characters.verifications.update', $character), [
            'fact' => 'character',
            'checked' => 1,
        ])->assertRedirect(route('characters.show', $character));

        $this->put(route('characters.verifications.update', $character), [
            'fact' => 'debut',
            'checked' => 1,
        ])->assertRedirect(route('characters.show', $character));

        $this->assertDatabaseHas('character_verifications', [
            'user_id' => $user->id,
            'character_id' => $character->id,
        ]);
        $this->assertNotNull(CharacterVerification::query()
            ->whereBelongsTo($user)
            ->whereBelongsTo($character)
            ->value('character_verified_at'));
        $this->assertNotNull(CharacterVerification::query()
            ->whereBelongsTo($user)
            ->whereBelongsTo($character)
            ->value('debut_game_verified_at'));
    }

    public function test_users_can_verify_only_games_associated_with_the_character(): void
    {
        $user = User::factory()->create();
        $character = Character::create(['name' => 'Tails']);
        $game = Game::create(['name' => 'Sonic Adventure 2', 'release_date' => '2001-06-19']);
        $unrelatedGame = Game::create(['name' => 'Sonic Heroes', 'release_date' => '2003-12-30']);
        $character->games()->attach($game);

        $this->actingAs($user)->put(route('characters.verifications.update', $character), [
            'fact' => 'appearance',
            'game_id' => $game->id,
            'checked' => 1,
        ])->assertRedirect(route('characters.show', $character));

        $this->assertDatabaseHas('character_game_verifications', [
            'user_id' => $user->id,
            'character_id' => $character->id,
            'game_id' => $game->id,
        ]);

        $this->put(route('characters.verifications.update', $character), [
            'fact' => 'appearance',
            'game_id' => $unrelatedGame->id,
            'checked' => 1,
        ])->assertSessionHasErrors('game_id');

        $this->assertDatabaseMissing('character_game_verifications', [
            'user_id' => $user->id,
            'character_id' => $character->id,
            'game_id' => $unrelatedGame->id,
        ]);
    }

    public function test_users_can_remove_their_confirmation_without_removing_other_users_confirmations(): void
    {
        $character = Character::create(['name' => 'Tails']);
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        CharacterVerification::create([
            'user_id' => $firstUser->id,
            'character_id' => $character->id,
            'character_verified_at' => now(),
        ]);
        CharacterVerification::create([
            'user_id' => $secondUser->id,
            'character_id' => $character->id,
            'character_verified_at' => now(),
        ]);

        $this->actingAs($firstUser)->put(route('characters.verifications.update', $character), [
            'fact' => 'character',
            'checked' => 0,
        ])->assertRedirect(route('characters.show', $character));

        $this->assertDatabaseCount('character_verifications', 1);
        $this->assertDatabaseHas('character_verifications', [
            'user_id' => $secondUser->id,
            'character_id' => $character->id,
        ]);
    }

    public function test_character_page_shows_shared_verification_counts_and_the_current_users_checks(): void
    {
        $character = Character::create(['name' => 'Tails']);
        $game = Game::create(['name' => 'Sonic Adventure 2', 'release_date' => '2001-06-19']);
        $character->games()->attach($game);
        $user = User::factory()->create();
        CharacterVerification::create([
            'user_id' => $user->id,
            'character_id' => $character->id,
            'character_verified_at' => now(),
        ]);
        CharacterGameVerification::create([
            'user_id' => $user->id,
            'character_id' => $character->id,
            'game_id' => $game->id,
            'verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('characters.show', $character))
            ->assertOk()
            ->assertSee('1 confirmação')
            ->assertSee('Confirmado por ti');
    }

    public function test_verifying_a_debut_without_a_debut_game_is_rejected(): void
    {
        $user = User::factory()->create();
        $character = Character::create(['name' => 'Tails']);

        $this->actingAs($user)->put(route('characters.verifications.update', $character), [
            'fact' => 'debut',
            'checked' => 1,
        ])->assertSessionHasErrors('fact');

        $this->assertDatabaseCount('character_verifications', 0);
    }

    public function test_deleted_debut_games_do_not_leave_visible_verifications(): void
    {
        $character = Character::create(['name' => 'Tails']);
        $debut = Game::create(['name' => 'Tails Debut', 'release_date' => '1992-11-21']);
        $character->update(['debut_game_id' => $debut->id]);
        CharacterVerification::create([
            'user_id' => User::factory()->create()->id,
            'character_id' => $character->id,
            'debut_game_id' => $debut->id,
            'debut_game_verified_at' => now(),
        ]);

        $debut->delete();

        $this->get(route('characters.show', $character))
            ->assertOk()
            ->assertSee('0 confirmações')
            ->assertDontSee('1 confirmação');
    }
}
