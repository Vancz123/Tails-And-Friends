<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'category', 'summary', 'attempt', 'blocked'])]
class SecurityIncident extends Model
{
    protected function casts(): array
    {
        return [
            'blocked' => 'boolean',
        ];
    }
}
