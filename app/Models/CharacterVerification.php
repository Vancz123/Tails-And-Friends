<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'character_id',
    'debut_game_id',
    'character_verified_at',
    'debut_game_verified_at',
])]
class CharacterVerification extends Model
{
    protected function casts(): array
    {
        return [
            'character_verified_at' => 'datetime',
            'debut_game_verified_at' => 'datetime',
        ];
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function debutGame(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'debut_game_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
