<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'image_path', 'image_url', 'description', 'debut_game_id'])]
class Character extends Model
{
    protected function image(): Attribute
    {
        return Attribute::get(fn(): ?string => $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : $this->image_url);
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class)->withTimestamps();
    }

    public function debutGame(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'debut_game_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(CharacterVerification::class);
    }

    public function gameVerifications(): HasMany
    {
        return $this->hasMany(CharacterGameVerification::class);
    }
}
