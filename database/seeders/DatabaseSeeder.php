<?php

namespace Database\Seeders;

use App\Models\Character;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $characters = collect([
            ['name' => 'Miles "Tails" Prower', 'image_url' => 'https://placehold.co/800x600/172554/fde68a?text=Tails', 'description' => 'A brilliant young mechanic and Sonic\'s loyal friend.'],
            ['name' => 'Sonic the Hedgehog', 'image_url' => 'https://placehold.co/800x600/172554/93c5fd?text=Sonic', 'description' => 'The fastest hedgehog in the world.'],
            ['name' => 'Knuckles the Echidna', 'image_url' => 'https://placehold.co/800x600/7f1d1d/fca5a5?text=Knuckles', 'description' => 'Guardian of the Master Emerald.'],
            ['name' => 'Amy Rose', 'image_url' => 'https://placehold.co/800x600/831843/fbcfe8?text=Amy+Rose', 'description' => 'An energetic adventurer who always stands up for her friends.'],
            ['name' => 'Cream the Rabbit', 'image_url' => 'https://placehold.co/800x600/713f12/fef08a?text=Cream', 'description' => 'A kind-hearted rabbit who travels with her Chao, Cheese.'],
        ])->mapWithKeys(fn (array $attributes) => [
            $attributes['name'] => Character::updateOrCreate(['name' => $attributes['name']], $attributes),
        ]);

        $games = [
            ['name' => 'Sonic the Hedgehog', 'release_date' => '1991-06-23', 'image_url' => 'https://placehold.co/800x600/172554/93c5fd?text=Sonic+1', 'characters' => ['Sonic the Hedgehog']],
            ['name' => 'Sonic the Hedgehog 2', 'release_date' => '1992-11-21', 'image_url' => 'https://placehold.co/800x600/172554/fde68a?text=Sonic+2', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog']],
            ['name' => 'Sonic CD', 'release_date' => '1993-09-23', 'image_url' => 'https://placehold.co/800x600/831843/fbcfe8?text=Sonic+CD', 'characters' => ['Sonic the Hedgehog', 'Amy Rose']],
            ['name' => 'Sonic the Hedgehog 3', 'release_date' => '1994-02-02', 'image_url' => 'https://placehold.co/800x600/7f1d1d/fca5a5?text=Sonic+3', 'characters' => ['Sonic the Hedgehog', 'Knuckles the Echidna']],
            ['name' => 'Sonic Advance 2', 'release_date' => '2002-12-19', 'image_url' => 'https://placehold.co/800x600/713f12/fef08a?text=Sonic+Advance+2', 'characters' => ['Sonic the Hedgehog', 'Cream the Rabbit']],
            ['name' => 'Sonic Adventure 2', 'release_date' => '2001-06-19', 'image_url' => 'https://placehold.co/800x600/0f172a/bae6fd?text=Sonic+Adventure+2', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna']],
            ['name' => 'Sonic Heroes', 'release_date' => '2003-12-30', 'image_url' => 'https://placehold.co/800x600/1e1b4b/c4b5fd?text=Sonic+Heroes', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose']],
            ['name' => 'Sonic Generations', 'release_date' => '2011-11-01', 'image_url' => 'https://placehold.co/800x600/172554/93c5fd?text=Sonic+Generations', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose']],
            ['name' => 'Sonic Frontiers', 'release_date' => '2022-11-08', 'image_url' => 'https://placehold.co/800x600/052e16/86efac?text=Sonic+Frontiers', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose']],
        ];

        foreach ($games as $attributes) {
            $game = Game::updateOrCreate(
                ['name' => $attributes['name']],
                collect($attributes)->except('characters')->all(),
            );
            $game->characters()->sync($characters->only($attributes['characters'])->pluck('id'));
        }

        $debutGames = [
            'Sonic the Hedgehog' => 'Sonic the Hedgehog',
            'Miles "Tails" Prower' => 'Sonic the Hedgehog 2',
            'Amy Rose' => 'Sonic CD',
            'Knuckles the Echidna' => 'Sonic the Hedgehog 3',
            'Cream the Rabbit' => 'Sonic Advance 2',
        ];

        foreach ($debutGames as $characterName => $gameName) {
            $character = $characters->get($characterName);
            $game = Game::where('name', $gameName)->firstOrFail();

            $character->games()->syncWithoutDetaching([$game->id]);
            $character->update(['debut_game_id' => $game->id]);
        }
    }
}
