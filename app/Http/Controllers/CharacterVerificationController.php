<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterGameVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CharacterVerificationController extends Controller
{
    public function update(Request $request, Character $character): RedirectResponse
    {
        $data = $request->validate([
            'fact' => ['required', 'string', Rule::in(['character', 'debut', 'appearance'])],
            'checked' => ['required', 'boolean'],
            'game_id' => [
                'exclude_unless:fact,appearance',
                'required_if:fact,appearance',
                'integer',
                Rule::exists('character_game', 'game_id')->where('character_id', $character->id),
            ],
        ]);

        $isChecked = (bool) $data['checked'];

        if ($data['fact'] === 'debut' && $character->debut_game_id === null) {
            throw ValidationException::withMessages([
                'fact' => 'Não é possível confirmar o jogo de estreia enquanto ele não estiver definido.',
            ]);
        }

        if ($data['fact'] === 'appearance') {
            $verificationKeys = [
                'user_id' => $request->user()->id,
                'character_id' => $character->id,
                'game_id' => $data['game_id'],
            ];

            if ($isChecked) {
                CharacterGameVerification::query()->updateOrCreate($verificationKeys, [
                    'verified_at' => now(),
                ]);
            } else {
                CharacterGameVerification::query()->where($verificationKeys)->delete();
            }
        } else {
            $verification = $character->verifications()->firstOrNew([
                'user_id' => $request->user()->id,
            ]);
            $verification->character_id = $character->id;

            if ($data['fact'] === 'character') {
                $verification->character_verified_at = $isChecked ? now() : null;
            } else {
                $verification->debut_game_id = $isChecked ? $character->debut_game_id : null;
                $verification->debut_game_verified_at = $isChecked ? now() : null;
            }

            if ($verification->character_verified_at === null && $verification->debut_game_verified_at === null) {
                $verification->delete();
            } else {
                $verification->save();
            }
        }

        return redirect()->route('characters.show', $character)->with(
            'status',
            $isChecked ? 'Confirmação registada.' : 'Confirmação removida.',
        );
    }
}
