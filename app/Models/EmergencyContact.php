<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyContact extends Model
{
    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
