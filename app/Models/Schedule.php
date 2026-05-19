<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
