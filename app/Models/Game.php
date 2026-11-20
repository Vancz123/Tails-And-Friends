<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'image_path', 'image_url', 'release_date', 'description'])]
class Game extends Model
{
    protected function image(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : $this->image_url);
    }

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
        ];
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class)->withTimestamps();
    }

    public function appearanceVerifications(): HasMany
    {
        return $this->hasMany(CharacterGameVerification::class);
    }
}
