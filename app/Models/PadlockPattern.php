<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PadlockPattern extends Model
{
    protected $casts = [
        'unlock_sequence' => 'array',
    ];

    public function padlock(): hasMany
    {
        return $this->hasMany(Padlock::class);
    }
}
