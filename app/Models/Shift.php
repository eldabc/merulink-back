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
        'rest_period_time',
        'rest_period_unit_time',
        'active_period_time',
        'active_period_unit_time',
        'total_period_time',
        'total_period_unit_time',
        'allow_exit',
        'allow_re_scanned',
        'available',
        'available_from',
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