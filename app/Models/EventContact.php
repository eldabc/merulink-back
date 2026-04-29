<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventContact extends Model
{
    public function phones()
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }
}
