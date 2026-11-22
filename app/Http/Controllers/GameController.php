<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksAdminAccess;
use App\Models\Character;
use App\Models\Game;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class GameController extends Controller
{
    use ChecksAdminAccess;

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'digits:4', 'between:1991,2100'],
            'sort' => ['nullable', Rule::in(['name', 'release_date', 'created_at'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);
        $search = trim($filters['q'] ?? '');
        $year = $filters['year'] ?? '';
        $sort = $filters['sort'] ?? 'release_date';
        $direction = $filters['direction'] ?? ($sort === 'release_date' ? 'desc' : 'asc');

        $gameQuery = Game::query()
            ->withCount([
                'characters',
                'characters as debut_characters_count' => function (Builder $query) {
                    $query->whereRaw('characters.debut_game_id = games.id');
                },
                'characters as appearing_characters_count' => function (Builder $query) {
                    $query->where(function (Builder $q) {
                        $q->whereNull('characters.debut_game_id')
                            ->orWhereRaw('characters.debut_game_id <> games.id');
                    });
                },
            ])
            ->when($search !== '', fn(Builder $query) => $query
                ->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('characters', fn(Builder $characters) => $characters
                            ->where('name', 'like', "%{$search}%"));
                }))
            ->when($year !== '', fn(Builder $query) => $query->whereYear('release_date', $year));

        if ($sort === 'created_at') {
            $gameQuery->orderBy('created_at', $direction)->orderBy('name');
        } elseif ($sort === 'name') {
            $gameQuery->orderBy('name', $direction);
        } else {
            $gameQuery->orderBy('release_date', $direction)->orderBy('name');
        }

        $games = $gameQuery->paginate(15)->withQueryString();

        return view('games.index', compact('games', 'search', 'year', 'sort', 'direction'));
    }

    public function show(Game $game): View
    {
        $debutCharacters = Character::query()
            ->where('debut_game_id', $game->id)
            ->orderBy('name')
            ->get();

        $appearingCharacters = $game->characters()
            ->where(function (Builder $query) use ($game): void {
                $query->whereNull('characters.debut_game_id')
                    ->orWhere('characters.debut_game_id', '<>', $game->id);
            })
            ->orderBy('characters.name')
            ->get();

        return view('games.show', compact('game', 'debutCharacters', 'appearingCharacters'));
    }

    public function create()
    {
        $this->requireAdministrator();
        $characters = Character::orderBy('name')->get();

        return view('games.form', ['game' => new Game, 'characters' => $characters]);
    }

public function store(Request $request)
    {
        $this->requireAdministrator();
        $data = $this->validatedData($request);
        $characterIds = $data['character_ids'] ?? [];
        $image = $data['image'] ?? null;
        unset($data['character_ids']);
        unset($data['image']);

        if ($image) {
            $data['image_path'] = $this->storeImage($image);
            $data['image_url'] = null;
        }

        $game = Game::create($data);
        $game->characters()->sync($characterIds);

        return redirect()->route('games.index')->with('status', 'Jogo adicionado com sucesso.');
    }

    public function edit(Game $game)
    {
        if (! $this->canManageContent()) {
            return view('games.check', ['game' => $this->withAppearanceState($game)]);
        }

        $characters = Character::orderBy('name')->get();
        $game->load('characters');

        return view('games.form', compact('game', 'characters'));
    }

    /**
     * Carrega as personagens do jogo com as confirmações de presença do utilizador atual.
     */
    private function withAppearanceState(Game $game): Game
    {
        $userId = auth()->id();

        $game->load([
            'characters' => fn ($query) => $query
                ->orderBy('characters.name')
                ->withCount([
                    'gameVerifications as appearance_verifications_count' => fn (Builder $query) => $query
                        ->where('game_id', $game->id),
                ])
                ->withExists([
                    'gameVerifications as appearance_verified_by_user' => fn (Builder $query) => $query
                        ->where('game_id', $game->id)
                        ->where('user_id', $userId),
                ]),
        ]);

        return $game;
    }

    public function update(Request $request, Game $game)
    {
        $data = $this->validatedData($request, $game);
        $characterIds = $data['character_ids'] ?? [];
        $image = $data['image'] ?? null;
        unset($data['character_ids']);
        unset($data['image']);

        if ($image) {
            $data['image_path'] = $this->storeImage($image);
            $data['image_url'] = null;
        }

        $oldImagePath = $image ? $game->image_path : null;
        $game->update($data);
        $game->characters()->sync($characterIds);

        if ($oldImagePath) {
            $this->deleteImage($oldImagePath);
        }

        return redirect()->route('games.index')->with('status', 'Jogo atualizado com sucesso.');
    }

public function destroy(Game $game)
    {
        $this->requireAdministrator();

        $imagePath = $game->image_path;
        $game->delete();

        if ($imagePath) {
            $this->deleteImage($imagePath);
        }

        return redirect()->route('games.index')->with('status', 'Jogo removido com sucesso.');
    }

    /**
     * @return array{name: string, image_url?: ?string, release_date: string, description?: ?string, character_ids?: array<int, int>, image?: UploadedFile}
     */
    private function validatedData(Request $request, ?Game $game = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('games', 'name')->ignore($game?->id)],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'release_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'character_ids' => ['sometimes', 'array'],
            'character_ids.*' => ['integer', 'exists:characters,id'],
        ]);
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('games', 'public');

        if (!$path) {
            throw new RuntimeException('Não foi possível guardar a imagem do jogo.');
        }

        return $path;
    }

    private function deleteImage(string $path): void
    {
        $disk = Storage::disk('public');

        if ($disk->exists($path) && !$disk->delete($path)) {
            throw new RuntimeException('Não foi possível remover a imagem antiga do jogo.');
        }
    }

    private function requireAdministrator(): void
    {
        abort_unless(auth()->user()?->isAdministrator(), 403);
    }
}
