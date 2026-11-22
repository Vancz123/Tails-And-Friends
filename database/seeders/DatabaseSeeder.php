<?php

namespace Database\Seeders;

use App\Models\Character;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => bcrypt('admin123'), 'role' => 'admin']
        );

        $characters = collect([
            ['name' => 'Miles "Tails" Prower', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/2/26/TSR_Tails.png', 'description' => 'Um jovem mecânico brilhante e o amigo leal do Sonic.'],
            ['name' => 'Sonic the Hedgehog', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/2/2d/TSR_Sonic.png', 'description' => 'O ouriço mais rápido do mundo.'],
            ['name' => 'Knuckles the Echidna', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/0/04/TSR_Knuckles.png', 'description' => 'O guardião da Esmeralda Mestre.'],
            ['name' => 'Amy Rose', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/7/71/TSR_Amy.png', 'description' => 'Uma aventureira energética que defende sempre os seus amigos.'],
            ['name' => 'Cream the Rabbit', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/a/a3/Cream_and_Cheese_artwork.png', 'description' => 'Uma coelha de bom coração que viaja com o seu Chao, Cheese.'],
            ['name' => 'Shadow the Hedgehog', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/b/b5/TSR_Shadow.png', 'description' => 'A forma de vida definitiva, misteriosa e poderosa, criada pelo Prof. Gerald.'],
            ['name' => 'Dr. Eggman', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/8/88/TSR_Eggman.png', 'description' => 'Um cientista génio e megalomaníaco que quer dominar o mundo com os seus robôs.'],
            ['name' => 'Rouge the Bat', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/9/91/TSR_Rouge.png', 'description' => 'Uma caçadora de tesouros astuta e agente secreta obcecada por joias.'],
            ['name' => 'Silver the Hedgehog', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/8/86/TSR_Silver.png', 'description' => 'Um ouriço telecinético vindo do futuro determinado a salvar o seu tempo.'],
            ['name' => 'Blaze the Cat', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/6/64/TSR_Blaze.png', 'description' => 'A princesa de outra dimensão com o poder de controlar o fogo.'],
            ['name' => 'Metal Sonic', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/1/12/TSR_Metal_Sonic.png', 'description' => 'Uma réplica robótica implacável criada pelo Dr. Eggman para rivalizar com o Sonic.'],
            ['name' => 'Big the Cat', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/2/23/TSR_Big.png', 'description' => 'Um gato gigante e tranquilo que adora pescar e procura o seu amigo Froggy.'],
            ['name' => 'Vector the Crocodile', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/c/c8/TSR_Vector.png', 'description' => 'O líder da Agência de Detetives Chaotix, barulhento e focado em dinheiro.'],
            ['name' => 'Espio the Chameleon', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/b/b7/Sonic_Forces_-_Espio_the_Chameleon.png', 'description' => 'Um ninja silencioso e disciplinado membro dos Chaotix.'],
            ['name' => 'Charmy Bee', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/9/9c/Sonic_Forces_-_Charmy_Bee.png', 'description' => 'Uma abelha hiperativa e brincalhona que faz parte da equipa Chaotix.'],
            ['name' => 'Jet the Hawk', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/8/8d/Sonic_Free_Riders_-_Jet_the_Hawk.png', 'description' => 'O líder arrogante e veloz dos Babylon Rogues em pranchas voadoras.'],
            ['name' => 'Wave the Swallow', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/4/4e/Sonic_Free_Riders_-_Wave_the_Swallow.png', 'description' => 'A engenheira talentosa e exigente dos Babylon Rogues.'],
            ['name' => 'Storm the Albatross', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/1/14/Sonic_Free_Riders_-_Storm_the_Albatross.png', 'description' => 'O membro mais forte e de pavio curto dos Babylon Rogues.'],
            ['name' => 'E-123 Omega', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/a/a2/TSR_Omega.png', 'description' => 'Um robô pesado altamente armado que se rebelou contra o Dr. Eggman.'],
            ['name' => 'Sage', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/e/e1/Sonic_Frontiers_-_Sage.png', 'description' => 'Uma inteligência artificial avançada e misteriosa ligada às Ilhas Starfall.'],
        ])->mapWithKeys(fn(array $attributes) => [
                $attributes['name'] => Character::updateOrCreate(['name' => $attributes['name']], $attributes),
            ]);

        $games = [
            ['name' => 'Sonic the Hedgehog', 'release_date' => '1991-06-23', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/a/a7/Sonic1_box_art.jpg', 'characters' => ['Sonic the Hedgehog', 'Dr. Eggman']],
            ['name' => 'Sonic the Hedgehog 2', 'release_date' => '1992-11-21', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/2/23/Sonic2_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Dr. Eggman']],
            ['name' => 'Sonic CD', 'release_date' => '1993-09-23', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/c/c8/SonicCD_box_art.jpg', 'characters' => ['Sonic the Hedgehog', 'Amy Rose', 'Metal Sonic', 'Dr. Eggman']],
            ['name' => 'Sonic the Hedgehog 3', 'release_date' => '1994-02-02', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/0/07/Sonic3_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Dr. Eggman']],
            ['name' => 'Knuckles\' Chaotix', 'release_date' => '1995-04-21', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/9/9e/Knuckles_Chaotix_box_art.jpg', 'characters' => ['Knuckles the Echidna', 'Vector the Crocodile', 'Espio the Chameleon', 'Charmy Bee', 'Metal Sonic', 'Dr. Eggman']],
            ['name' => 'Sonic Adventure', 'release_date' => '1998-12-23', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/a/ad/Sonic_Adventure_US_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose', 'Big the Cat', 'Metal Sonic', 'Dr. Eggman']],
            ['name' => 'Sonic Adventure 2', 'release_date' => '2001-06-19', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/9/9b/Sonic_Adventure_2_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose', 'Shadow the Hedgehog', 'Rouge the Bat', 'Big the Cat', 'Dr. Eggman']],
            ['name' => 'Sonic Advance 2', 'release_date' => '2002-12-19', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/5/53/Sonic_Advance_2_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose', 'Cream the Rabbit', 'Dr. Eggman']],
            ['name' => 'Sonic Heroes', 'release_date' => '2003-12-30', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/9/91/Sonic_Heroes_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose', 'Cream the Rabbit', 'Big the Cat', 'Shadow the Hedgehog', 'Rouge the Bat', 'E-123 Omega', 'Vector the Crocodile', 'Espio the Chameleon', 'Charmy Bee', 'Metal Sonic', 'Dr. Eggman']],
            ['name' => 'Sonic Rush', 'release_date' => '2005-11-15', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/6/64/Sonic_Rush_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose', 'Cream the Rabbit', 'Blaze the Cat', 'Dr. Eggman']],
            ['name' => 'Sonic Riders', 'release_date' => '2006-02-21', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/5/5d/Sonic_Riders_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose', 'Cream the Rabbit', 'Shadow the Hedgehog', 'Rouge the Bat', 'Jet the Hawk', 'Wave the Swallow', 'Storm the Albatross', 'Dr. Eggman']],
            ['name' => 'Sonic the Hedgehog (2006)', 'release_date' => '2006-11-16', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/e/e5/Sonic_2006_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose', 'Shadow the Hedgehog', 'Rouge the Bat', 'Silver the Hedgehog', 'Blaze the Cat', 'E-123 Omega', 'Dr. Eggman']],
            ['name' => 'Sonic Generations', 'release_date' => '2011-11-01', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/9/90/Sonic_Generations_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose', 'Cream the Rabbit', 'Shadow the Hedgehog', 'Rouge the Bat', 'Silver the Hedgehog', 'Blaze the Cat', 'Vector the Crocodile', 'Espio the Chameleon', 'Charmy Bee', 'Metal Sonic', 'Dr. Eggman']],
            ['name' => 'Sonic Frontiers', 'release_date' => '2022-11-08', 'image_url' => 'https://static.wikia.nocookie.net/sonic/images/e/e9/Sonic_Frontiers_box_art.jpg', 'characters' => ['Miles "Tails" Prower', 'Sonic the Hedgehog', 'Knuckles the Echidna', 'Amy Rose', 'Big the Cat', 'Sage', 'Dr. Eggman']],
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
            'Dr. Eggman' => 'Sonic the Hedgehog',
            'Miles "Tails" Prower' => 'Sonic the Hedgehog 2',
            'Amy Rose' => 'Sonic CD',
            'Metal Sonic' => 'Sonic CD',
            'Knuckles the Echidna' => 'Sonic the Hedgehog 3',
            'Vector the Crocodile' => 'Knuckles\' Chaotix',
            'Espio the Chameleon' => 'Knuckles\' Chaotix',
            'Charmy Bee' => 'Knuckles\' Chaotix',
            'Big the Cat' => 'Sonic Adventure',
            'Shadow the Hedgehog' => 'Sonic Adventure 2',
            'Rouge the Bat' => 'Sonic Adventure 2',
            'Cream the Rabbit' => 'Sonic Advance 2',
            'E-123 Omega' => 'Sonic Heroes',
            'Blaze the Cat' => 'Sonic Rush',
            'Jet the Hawk' => 'Sonic Riders',
            'Wave the Swallow' => 'Sonic Riders',
            'Storm the Albatross' => 'Sonic Riders',
            'Silver the Hedgehog' => 'Sonic the Hedgehog (2006)',
            'Sage' => 'Sonic Frontiers',
        ];

        foreach ($debutGames as $characterName => $gameName) {
            $character = $characters->get($characterName);
            $game = Game::where('name', $gameName)->firstOrFail();

            $character->games()->syncWithoutDetaching([$game->id]);
            $character->update(['debut_game_id' => $game->id]);
        }
    }
}
