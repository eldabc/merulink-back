<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Schedule extends Model
{
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function schedulePlanning(): BelongsTo 
    {
        return $this->belongsTo(SchedulePlanning::class);
    }

    // public function scheduleSnapshot(): HasOne 
    // {
    //     return $this->hasOne(ScheduleSnapshot::class);
    // }
}
