<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    protected $fillable = [
        'title',
        'start',
        'end',
        'all_day',
        'extended_props',
        'event_category_id',
        'location_id',
    ];

    protected $casts = [
        'extended_props' => 'array',
        'all_day' => 'boolean',
        'start' => 'datetime:Y-m-d\TH:i:s',
        'end'   => 'datetime:Y-m-d\TH:i:s',
    ];

    public function eventCategory(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function templateOrigin(): HasOne
    {
        return $this->hasOne(EventTemplate::class, 'origin_event_id');
    }

    public function templateRecord()
    {
        return $this->hasOne(EventTemplate::class, 'event_id');
    }
}
