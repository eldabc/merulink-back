<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Schedule extends Model
{
    protected $fillable = [
        'date',
        'employee_id',
        'shift_id',
        'letter_shift',
        'color',
        'code',
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
        'schedule_planning_id',
    ];

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

}
