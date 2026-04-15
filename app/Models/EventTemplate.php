<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTemplate extends Model
{
    protected $fillable = [
        'name',
        'event_id',
        'origin_event_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function originEvent()
    {
        // Evento origen
        return $this->belongsTo(Event::class, 'origin_event_id');
    }
}
