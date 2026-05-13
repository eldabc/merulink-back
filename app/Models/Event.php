<?php

namespace App\Models;

use App\Enums\EventStatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Event extends Model
{
    protected $fillable = [
        'title',
        'start',
        'end',
        'all_day',
        'repeat_event',
        'repeat_interval',
        'external_source',
        'external_id',
        'extended_props',
        'event_category_id',
        'location_id',
    ];

    protected $casts = [
        'extended_props' => 'array',
        'all_day' => 'boolean',
        // 'start' => 'datetime:Y-m-d\TH:i:s',
        // 'end'   => 'datetime:Y-m-d\TH:i:s',
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

    public function contacts(): HasMany
    {
        return $this->hasMany(EventContact::class);
    }

    public function templateRecord()
    {
        return $this->hasOne(EventTemplate::class, 'event_id');
    }

    public function scopeOnlyEventOrigin($query)
    {
        return $query->doesntHave('templateRecord');
    }

    // public function scopeOnlyTemplates($query)
    // {
    //     return $query->has('templateRecord');
    // }

    public function scopeHasGeneratedTemplate($query)
    {
        return $query->has('templateOrigin');
    }

    public function parent()
    {
        return $this->belongsTo(
            Event::class,
            'parent_event_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            Event::class,
            'parent_event_id'
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Extrae el valor del JSON
                $value = $this->extended_props['status'] ?? null;
                
                // Si existe, intenta convertirlo a Enum
                return $value ? EventStatus::tryFrom($value) : null;
            },
            set: function ($value) {
                // Si le asigna un Enum o un string, guarda dentro de array
                $currentProps = $this->extended_props ?? [];
                $currentProps['status'] = $value instanceof EventStatus ? $value->value : $value;
                
                return [
                    'extended_props' => $currentProps
                ];
            }
        );
    }
    
}
