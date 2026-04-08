<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = [
        'title',
        'start',
        'end',
        'all_day',
        'extended_props',
        'category_id',
        'location_id',
    ];

    protected $casts = [
        'extended_props' => 'array',
        'all_day' => 'boolean',
    ];

    public function eventCategory(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
