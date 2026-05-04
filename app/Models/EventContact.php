<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class EventContact extends Model
{

    protected $fillable = [
        'event_id',
        'first_name',
        'last_name',
        'email',        
    ];

    public function phones()
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
