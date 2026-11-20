<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\Game;
use App\Models\SecurityIncident;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CharacterAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_content_is_public_and_dashboard_requires_authentication(): void
    {
        $character = Character::create(['name' => 'Tails']);
        $game = Game::create(['name' => 'Sonic Adventure 2', 'release_date' => '2001-06-19']);
        $character->games()->attach($game);

        $this->get(route('characters.index'))->assertOk()->assertSee('Tails');
        $this->get(route('characters.show', $character))->assertOk()->assertSee('Sonic Adventure 2');
        $this->get(route('games.index'))->assertOk()->assertSee('Sonic Adventure 2');
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_collection_search_filters_characters_and_games(): void
    {
        $tails = Character::create(['name' => 'Tails', 'description' => 'Brilliant mechanic']);
        $knuckles = Character::create(['name' => 'Knuckles']);
        $adventure = Game::create(['name' => 'Sonic Adventure 2', 'release_date' => '2001-06-19']);
        $heroes = Game::create(['name' => 'Sonic Heroes', 'release_date' => '2003-12-30']);
        $adventure->characters()->attach($tails);
        $heroes->characters()->attach($knuckles);

        $this->get(route('characters.index', ['q' => 'Tails']))
            ->assertOk()
            ->assertSee('Tails')
            ->assertDontSee('Knuckles');

        $this->get(route('games.index', ['q' => 'Tails']))
            ->assertOk()
            ->assertSee('Sonic Adventure 2')
            ->assertDontSee('Sonic Heroes');

        $this->get(route('games.index', ['year' => '2001']))
            ->assertOk()
            ->assertSee('Sonic Adventure 2')
            ->assertDontSee('Sonic Heroes');
    }

    public function test_games_can_be_sorted_by_release_date_in_both_directions(): void
    {
        $earlyGame = Game::create(['name' => 'Early Game', 'release_date' => '1992-11-21']);
        $lateGame = Game::create(['name' => 'Late Game', 'release_date' => '2001-06-19']);

        $defaultResponse = $this->get(route('games.index'))
            ->assertOk()
            ->assertSee('Ordenar jogos')
            ->assertSee('A–Z')
            ->assertSee('Lançamento')
            ->assertSee('Adição')
            ->assertViewHas('sort', 'release_date')
            ->assertViewHas('direction', 'desc');

        $defaultResponse->assertViewHas('games', fn (LengthAwarePaginator $games): bool => $games->getCollection()->modelKeys() === [
            $lateGame->id,
            $earlyGame->id,
        ]);

        $ascendingResponse = $this->get(route('games.index', ['sort' => 'release_date', 'direction' => 'asc']))
            ->assertOk();

        $ascendingResponse->assertViewHas('games', fn (LengthAwarePaginator $games): bool => $games->getCollection()->modelKeys() === [
            $earlyGame->id,
            $lateGame->id,
        ]);
    }

    public function test_games_can_be_sorted_alphabetically_and_toggle_the_order(): void
    {
        $sonic = Game::create(['name' => 'Sonic', 'release_date' => '1991-06-23']);
        $amy = Game::create(['name' => 'Amy', 'release_date' => '1993-08-03']);

        $ascendingResponse = $this->get(route('games.index', ['sort' => 'name']))
            ->assertOk()
            ->assertSee('name="sort" value="name"', false)
            ->assertSee('name="direction" value="asc"', false)
            ->assertSee(route('games.index', [
                'q' => '',
                'year' => '',
                'sort' => 'name',
                'direction' => 'desc',
            ]))
            ->assertViewHas('sort', 'name');

        $ascendingResponse->assertViewHas('games', fn (LengthAwarePaginator $games): bool => $games->getCollection()->modelKeys() === [
            $amy->id,
            $sonic->id,
        ]);

        $descendingResponse = $this->get(route('games.index', ['sort' => 'name', 'direction' => 'desc']))
            ->assertOk()
            ->assertViewHas('direction', 'desc');

        $descendingResponse->assertViewHas('games', fn (LengthAwarePaginator $games): bool => $games->getCollection()->modelKeys() === [
            $sonic->id,
            $amy->id,
        ]);
    }

    public function test_games_can_be_sorted_by_database_addition_date(): void
    {
        $recentGame = Game::create(['name' => 'Recent Game', 'release_date' => '2001-06-19']);
        $earlyGame = Game::create(['name' => 'Early Game', 'release_date' => '1992-11-21']);
        $earlyGame->forceFill(['created_at' => '2020-01-01 00:00:00'])->saveQuietly();
        $recentGame->forceFill(['created_at' => '2021-01-01 00:00:00'])->saveQuietly();

        $ascendingResponse = $this->get(route('games.index', ['sort' => 'created_at']))
            ->assertOk()
            ->assertViewHas('sort', 'created_at');

        $ascendingResponse->assertViewHas('games', fn (LengthAwarePaginator $games): bool => $games->getCollection()->modelKeys() === [
            $earlyGame->id,
            $recentGame->id,
        ]);

        $descendingResponse = $this->get(route('games.index', ['sort' => 'created_at', 'direction' => 'desc']))
            ->assertOk();

        $descendingResponse->assertViewHas('games', fn (LengthAwarePaginator $games): bool => $games->getCollection()->modelKeys() === [
            $recentGame->id,
            $earlyGame->id,
        ]);
    }

    public function test_characters_can_be_sorted_alphabetically(): void
    {
        $sonic = Character::create(['name' => 'Sonic']);
        $amy = Character::create(['name' => 'Amy']);

        $response = $this->get(route('characters.index', ['sort' => 'name']))
            ->assertOk()
            ->assertSee('Ordenar personagens')
            ->assertSee('A–Z')
            ->assertSee('Estreia')
            ->assertSee('Adição')
            ->assertSee('name="direction" value="asc"', false)
            ->assertSee(route('characters.index', [
                'q' => '',
                'sort' => 'name',
                'direction' => 'desc',
            ]))
            ->assertViewHas('sort', 'name');

        $response->assertViewHas('characters', fn (LengthAwarePaginator $characters): bool => $characters->getCollection()->modelKeys() === [
            $amy->id,
            $sonic->id,
        ]);

        $descendingResponse = $this->get(route('characters.index', ['sort' => 'name', 'direction' => 'desc']))
            ->assertOk()
            ->assertViewHas('direction', 'desc');

        $descendingResponse->assertViewHas('characters', fn (LengthAwarePaginator $characters): bool => $characters->getCollection()->modelKeys() === [
            $sonic->id,
            $amy->id,
        ]);
    }

    public function test_characters_can_be_sorted_by_debut_game_release_date_with_missing_debuts_last(): void
    {
        $earlyGame = Game::create(['name' => 'Early Game', 'release_date' => '1992-11-21']);
        $lateGame = Game::create(['name' => 'Late Game', 'release_date' => '2001-06-19']);
        $lateDebut = Character::create(['name' => 'Amy', 'debut_game_id' => $lateGame->id]);
        $noDebut = Character::create(['name' => 'Tails']);
        $earlyDebut = Character::create(['name' => 'Sonic', 'debut_game_id' => $earlyGame->id]);

        $response = $this->get(route('characters.index', ['sort' => 'debut_release_date']))
            ->assertOk()
            ->assertViewHas('sort', 'debut_release_date');

        $response->assertViewHas('characters', fn (LengthAwarePaginator $characters): bool => $characters->getCollection()->modelKeys() === [
            $earlyDebut->id,
            $lateDebut->id,
            $noDebut->id,
        ]);

        $descendingResponse = $this->get(route('characters.index', [
            'sort' => 'debut_release_date',
            'direction' => 'desc',
        ]))->assertOk();

        $descendingResponse->assertViewHas('characters', fn (LengthAwarePaginator $characters): bool => $characters->getCollection()->modelKeys() === [
            $lateDebut->id,
            $earlyDebut->id,
            $noDebut->id,
        ]);
    }

    public function test_characters_can_be_sorted_by_database_addition_date(): void
    {
        $recentCharacter = Character::create(['name' => 'Amy']);
        $earlyCharacter = Character::create(['name' => 'Sonic']);
        $earlyCharacter->forceFill(['created_at' => '2020-01-01 00:00:00'])->saveQuietly();
        $recentCharacter->forceFill(['created_at' => '2021-01-01 00:00:00'])->saveQuietly();

        $response = $this->get(route('characters.index', ['sort' => 'created_at']))
            ->assertOk()
            ->assertViewHas('sort', 'created_at');

        $response->assertViewHas('characters', fn (LengthAwarePaginator $characters): bool => $characters->getCollection()->modelKeys() === [
            $earlyCharacter->id,
            $recentCharacter->id,
        ]);

        $descendingResponse = $this->get(route('characters.index', ['sort' => 'created_at', 'direction' => 'desc']))
            ->assertOk();

        $descendingResponse->assertViewHas('characters', fn (LengthAwarePaginator $characters): bool => $characters->getCollection()->modelKeys() === [
            $recentCharacter->id,
            $earlyCharacter->id,
        ]);
    }

    public function test_character_page_features_the_debut_game_and_lists_other_titles_by_name(): void
    {
        $character = Character::create(['name' => 'Tails']);
        $debut = Game::create([
            'name' => 'Debut Game',
            'release_date' => '1992-11-21',
            'image_url' => 'https://example.test/debut-game.jpg',
        ]);
        $other = Game::create([
            'name' => 'Another Adventure',
            'release_date' => '2001-06-19',
            'image_url' => 'https://example.test/other-game.jpg',
        ]);
        $other->characters()->attach($character);

        $this->actingAs(User::factory()->create())
            ->get(route('characters.edit', $character))
            ->assertOk()
            ->assertSee('debut_game_id')
            ->assertSee('Debut Game');

        $this->put(route('characters.update', $character), [
            'name' => $character->name,
            'debut_game_id' => $debut->id,
        ])->assertRedirect(route('characters.show', $character));

        $this->get(route('characters.show', $character))
            ->assertOk()
            ->assertSee('Debut Game')
            ->assertSee($debut->image_url)
            ->assertSee('Another Adventure')
            ->assertDontSee($other->image_url);

        $this->assertTrue($character->fresh()->games->contains($debut));
    }

    public function test_administrators_can_choose_an_existing_game_as_a_characters_debut(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $debutGame = Game::create(['name' => 'Sonic the Hedgehog 2', 'release_date' => '1992-11-21']);
        $anotherGame = Game::create(['name' => 'Sonic Adventure 2', 'release_date' => '2001-06-19']);

        $this->actingAs($admin)
            ->get(route('characters.create'))
            ->assertOk()
            ->assertSee($debutGame->name)
            ->assertSee($anotherGame->name)
            ->assertSee('value="'.$debutGame->id.'"', false);

        $this->post(route('characters.store'), [
            'name' => 'Tails',
            'debut_game_id' => $debutGame->id,
        ])->assertRedirect(route('characters.index'));

        $character = Character::where('name', 'Tails')->firstOrFail();

        $this->assertDatabaseHas('characters', [
            'id' => $character->id,
            'debut_game_id' => $debutGame->id,
        ]);
        $this->assertDatabaseHas('character_game', [
            'character_id' => $character->id,
            'game_id' => $debutGame->id,
        ]);
    }

    public function test_game_detail_separates_debut_characters_from_other_appearances(): void
    {
        $game = Game::create([
            'name' => 'Sonic Adventure 2',
            'release_date' => '2001-06-19',
            'description' => 'A two-sided adventure.',
        ]);
        $previousGame = Game::create(['name' => 'Sonic the Hedgehog 2', 'release_date' => '1992-11-21']);
        $debutCharacter = Character::create(['name' => 'Tails', 'debut_game_id' => $game->id]);
        $appearingCharacter = Character::create(['name' => 'Amy Rose']);
        $previousDebutCharacter = Character::create(['name' => 'Knuckles', 'debut_game_id' => $previousGame->id]);
        Character::create(['name' => 'Cream']);
        $game->characters()->attach([
            $debutCharacter->id,
            $appearingCharacter->id,
            $previousDebutCharacter->id,
        ]);

        $this->get(route('games.index'))
            ->assertOk()
            ->assertSee(route('games.show', $game), false)
            ->assertSee('Ver jogo');

        $response = $this->get(route('games.show', $game))
            ->assertOk()
            ->assertSee($game->name)
            ->assertSee($game->description)
            ->assertSee('role="tablist" aria-label="Personagens do jogo"', false)
            ->assertSee('Personagens presentes')
            ->assertSee('Personagens novos de estreia')
            ->assertSee('aria-selected="true"', false)
            ->assertSee('aria-selected="false"', false);

        $response->assertViewHas('debutCharacters', fn (EloquentCollection $characters): bool => $characters->modelKeys() === [$debutCharacter->id]);
        $response->assertViewHas('appearingCharacters', fn (EloquentCollection $characters): bool => $characters->modelKeys() === [
            $appearingCharacter->id,
            $previousDebutCharacter->id,
        ]);
    }

    public function test_registration_creates_a_regular_user_and_opens_the_dashboard(): void
    {
        $this->post(route('register'), [
            'name' => 'Sonic Fan',
            'email' => 'fan@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])->assertRedirect('/dashboard');

        $user = User::where('email', 'fan@example.com')->firstOrFail();
        $this->assertSame('user', $user->role);
        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertOk()->assertSee('Olá, Sonic Fan');
    }

    public function test_authenticated_users_can_edit_but_cannot_create_or_delete_content(): void
    {
        $user = User::factory()->create();
        $character = Character::create(['name' => 'Tails']);
        $game = Game::create(['name' => 'Sonic Adventure 2', 'release_date' => '2001-06-19']);

        $this->actingAs($user)
            ->get(route('characters.edit', $character))
            ->assertOk();

        $this->get(route('games.index'))
            ->assertOk()
            ->assertDontSee('Novo jogo');

        $this->get(route('games.edit', $game))->assertOk();

        $this->put(route('characters.update', $character), [
            'name' => 'Miles "Tails" Prower',
            'image_url' => null,
            'description' => 'Sonic\'s friend',
        ])
            ->assertRedirect(route('characters.show', $character));

        $this->put(route('games.update', $game), [
            'name' => 'Sonic Adventure 2',
            'release_date' => '2001-06-19',
            'character_ids' => [$character->id],
        ])->assertRedirect(route('games.index'));

        $this->post(route('characters.store'), ['name' => 'Amy Rose'])->assertForbidden();
        $this->post(route('games.store'), ['name' => 'Sonic Heroes', 'release_date' => '2003-12-30'])->assertForbidden();
        $this->delete(route('characters.destroy', $character))->assertForbidden();
        $this->delete(route('games.destroy', $game))->assertForbidden();

        $this->assertSame('Miles "Tails" Prower', $character->fresh()->name);
        $this->assertTrue($game->fresh()->characters->contains($character));
    }

    public function test_administrators_can_create_and_delete_content(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('characters.create'))
            ->assertOk()
            ->assertSee(asset('images/character-placeholder.png'), false);

        $this->get(route('games.create'))->assertOk();
        $this->get(route('games.index'))
            ->assertOk()
            ->assertSee(route('games.create'), false)
            ->assertSee('Novo jogo');

        $this->post(route('characters.store'), ['name' => 'Amy Rose'])
            ->assertRedirect(route('characters.index'));

        $character = Character::where('name', 'Amy Rose')->firstOrFail();
        $this->get(route('characters.index'))
            ->assertOk()
            ->assertSee(asset('images/character-placeholder.png'), false);
        $this->get(route('characters.show', $character))
            ->assertOk()
            ->assertSee(asset('images/character-placeholder.png'), false);

        $this->post(route('games.store'), [
            'name' => 'Sonic Heroes',
            'release_date' => '2003-12-30',
            'character_ids' => [$character->id],
        ])->assertRedirect(route('games.index'));

        $game = Game::where('name', 'Sonic Heroes')->firstOrFail();
        $this->delete(route('characters.destroy', $character))->assertRedirect(route('characters.index'));
        $this->delete(route('games.destroy', $game))->assertRedirect(route('games.index'));

        $this->assertDatabaseMissing('characters', ['id' => $character->id]);
        $this->assertDatabaseMissing('games', ['id' => $game->id]);
    }

    public function test_uploaded_images_are_stored_displayed_replaced_and_deleted(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/rS8AAAAASUVORK5CYII=');

        $this->actingAs($admin)->post(route('characters.store'), [
            'name' => 'Tails',
            'image' => UploadedFile::fake()->createWithContent('tails.png', $png),
        ])->assertRedirect(route('characters.index'));

        $character = Character::where('name', 'Tails')->firstOrFail();
        $characterImagePath = $character->image_path;
        Storage::disk('public')->assertExists($characterImagePath);
        $this->get(route('characters.index'))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url($characterImagePath), false);

        $this->post(route('games.store'), [
            'name' => 'Sonic Adventure 2',
            'release_date' => '2001-06-19',
            'character_ids' => [$character->id],
            'image' => UploadedFile::fake()->createWithContent('adventure.png', $png),
        ])->assertRedirect(route('games.index'));

        $game = Game::where('name', 'Sonic Adventure 2')->firstOrFail();
        $gameImagePath = $game->image_path;
        Storage::disk('public')->assertExists($gameImagePath);
        $this->put(route('characters.update', $character), [
            'name' => $character->name,
            'debut_game_id' => $game->id,
        ])->assertRedirect(route('characters.show', $character));

        $this->get(route('characters.show', $character))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url($gameImagePath), false);

        $this->put(route('characters.update', $character), [
            'name' => 'Miles Tails Prower',
            'image' => UploadedFile::fake()->createWithContent('tails-new.png', $png),
        ])->assertRedirect(route('characters.show', $character));

        $character->refresh();
        Storage::disk('public')->assertMissing($characterImagePath);
        Storage::disk('public')->assertExists($character->image_path);

        $this->delete(route('characters.destroy', $character))->assertRedirect(route('characters.index'));
        $this->delete(route('games.destroy', $game))->assertRedirect(route('games.index'));
        Storage::disk('public')->assertMissing($character->image_path);
        Storage::disk('public')->assertMissing($gameImagePath);
    }

    public function test_only_administrators_can_run_the_safe_security_simulation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('security.index'))
            ->assertForbidden();
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Simular Invasão do Dr. Eggman');

        $this->post(route('security.simulate'))->assertForbidden();

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Simular Invasão do Dr. Eggman');

        $this->get(route('security.index'))
            ->assertOk()
            ->assertSee('não envia pedidos externos');

        for ($run = 0; $run < 3; $run++) {
            $this->post(route('security.simulate'))->assertRedirect(route('security.index'));
        }

        $this->post(route('security.simulate'))->assertStatus(429);

        $this->assertDatabaseCount('security_incidents', 15);
        $this->assertSame(9, SecurityIncident::query()->where('blocked', false)->count());
        $this->assertSame(6, SecurityIncident::query()->where('blocked', true)->count());
    }
}
