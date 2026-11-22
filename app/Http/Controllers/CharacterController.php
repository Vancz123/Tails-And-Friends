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
use RuntimeException;

class CharacterController extends Controller
{
    use ChecksAdminAccess;

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(['name', 'debut_release_date', 'created_at'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);
        $search = trim($filters['q'] ?? '');
        $sort = $filters['sort'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';

        $characterQuery = Character::query()
            ->select('characters.*')
            ->withCount('games')
            ->when($search !== '', fn(Builder $query) => $query
                ->where('characters.name', 'like', "%{$search}%")
                ->orWhere('characters.description', 'like', "%{$search}%"));

        if ($sort === 'debut_release_date') {
            $characterQuery
                ->leftJoin('games as debut_games', 'characters.debut_game_id', '=', 'debut_games.id')
                ->orderByRaw('CASE WHEN debut_games.release_date IS NULL THEN 1 ELSE 0 END')
                ->orderBy('debut_games.release_date', $direction)
                ->orderBy('characters.name');
        } elseif ($sort === 'created_at') {
            $characterQuery
                ->orderBy('characters.created_at', $direction)
                ->orderBy('characters.name');
        } else {
            $characterQuery->orderBy('characters.name', $direction);
        }

        $characters = $characterQuery->paginate(15)->withQueryString();

        return view('characters.index', compact('characters', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        $this->requireAdministrator();
        $games = Game::orderBy('name')->get();

        if (! $this->canManageContent()) {
            return view('characters.check', ['character' => new Character, 'games' => $games]);
        }

        return view('characters.form', ['character' => new Character, 'games' => $games]);
    }

public function store(Request $request)
    {
        $this->requireAdministrator();

        $data = $this->validatedData($request);
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image) {
            $data['image_path'] = $this->storeImage($image);
            $data['image_url'] = null;
        }

        $debutGameId = $data['debut_game_id'] ?? null;
        unset($data['debut_game_id']);
        $character = Character::create($data);

        if ($debutGameId) {
            $character->games()->syncWithoutDetaching([$debutGameId]);
            $character->update(['debut_game_id' => $debutGameId]);
        }

        return redirect()->route('characters.index')->with('status', 'Personagem adicionado com sucesso.');
    }

    public function show(Character $character)
    {
        $character->load('debutGame');

        return view('characters.show', ['character' => $this->withVerificationState($character)]);
    }

    /**
     * Carrega as contagens de confirmações e o estado dos checks do utilizador atual.
     */
    private function withVerificationState(Character $character): Character
    {
        $userId = auth()->id();
        $character->loadCount([
            'verifications as character_verifications_count' => fn(Builder $query) => $query
                ->whereNotNull('character_verified_at'),
            'verifications as debut_game_verifications_count' => fn(Builder $query) => $query
                ->whereNotNull('debut_game_verified_at')
                ->whereNotNull('debut_game_id')
                ->where('debut_game_id', $character->debut_game_id),
        ]);
        $character->loadExists([
            'verifications as character_verified_by_user' => fn(Builder $query) => $query
                ->where('user_id', $userId)
                ->whereNotNull('character_verified_at'),
            'verifications as debut_game_verified_by_user' => fn(Builder $query) => $query
                ->where('user_id', $userId)
                ->whereNotNull('debut_game_verified_at')
                ->whereNotNull('debut_game_id')
                ->where('debut_game_id', $character->debut_game_id),
        ]);
        $character->load([
            'games' => fn($query) => $query
                ->when($character->debut_game_id, fn($query) => $query->where('games.id', '!=', $character->debut_game_id))
                ->orderBy('name')
                ->withCount([
                    'appearanceVerifications as verification_count' => fn(Builder $query) => $query
                        ->where('character_id', $character->id)
                ])
                ->withExists([
                    'appearanceVerifications as verified_by_user' => fn(Builder $query) => $query
                        ->where('character_id', $character->id)
                        ->where('user_id', $userId)
                ])
        ]);

        return $character;
    }

    public function edit(Character $character)
    {
        $games = Game::orderBy('name')->get();
        $character->load('debutGame');

        if (! $this->canManageContent()) {
            return view('characters.check', [
                'character' => $this->withVerificationState($character),
                'games' => $games,
            ]);
        }

        return view('characters.form', compact('character', 'games'));
    }

    public function update(Request $request, Character $character)
    {
        $data = $this->validatedData($request, $character);
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image) {
            $data['image_path'] = $this->storeImage($image);
            $data['image_url'] = null;
        }

        $hasDebutGame = array_key_exists('debut_game_id', $data);
        $debutGameId = $data['debut_game_id'] ?? null;
        unset($data['debut_game_id']);
        $oldImagePath = $image ? $character->image_path : null;
        $character->update($data);

        if ($hasDebutGame) {
            if ($debutGameId) {
                $character->games()->syncWithoutDetaching([$debutGameId]);
            }

            $character->update(['debut_game_id' => $debutGameId]);
        }

        if ($oldImagePath) {
            $this->deleteImage($oldImagePath);
        }

        return redirect()->route('characters.show', $character)->with('status', 'Personagem atualizado com sucesso.');
    }

public function destroy(Character $character)
    {
        $this->requireAdministrator();

        $imagePath = $character->image_path;
        $character->delete();

        if ($imagePath) {
            $this->deleteImage($imagePath);
        }

        return redirect()->route('characters.index')->with('status', 'Personagem removido com sucesso.');
    }

    /**
     * @return array{name: string, image_url?: ?string, description?: ?string, debut_game_id?: ?int, image?: UploadedFile}
     */
    private function validatedData(Request $request, ?Character $character = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('characters', 'name')->ignore($character?->id)],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
            'debut_game_id' => ['nullable', 'integer', 'exists:games,id'],
        ]);
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('characters', 'public');

        if (!$path) {
            throw new RuntimeException('Não foi possível guardar a imagem do personagem.');
        }

        return $path;
    }

    private function deleteImage(string $path): void
    {
        $disk = Storage::disk('public');

        if ($disk->exists($path) && !$disk->delete($path)) {
            throw new RuntimeException('Não foi possível remover a imagem antiga do personagem.');
        }
    }

    private function requireAdministrator(): void
    {
        abort_unless(auth()->user()?->isAdministrator(), 403);
    }
}
