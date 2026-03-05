<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Locker extends Model
{
    // Locker pertenece a una categoría
    public function lockerCategory(): BelongsTo
    {
        return $this->belongsTo(LockerCategory::class);
    }
}
