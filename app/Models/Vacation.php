<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
