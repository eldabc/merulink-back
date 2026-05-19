<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    protected $fillable = [
        'code',
        'description',
        'night_shift',
        'type_shift',
        'check_in_time',
        'check_out_time',
        'time_rest_period',
        'duration_unit_rest_period',
        'time_active_period',
        'duration_unit_active_period',
        'time_total_period',
        'duration_unit_total_period',
        'allow_exit',
        'allow_re_scanned',
        'available',
        'observation',
        'department_id'
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}