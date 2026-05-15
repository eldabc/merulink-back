<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    protected $fillable = [
        'code',
        'description',
        'active_period',
        'rest_period',
        'total_period',
        'check_in_time',
        'check_out_time',
        'allow_check_out',
        're_scanned',
        'available',
        'night_shift',
        'observations',
        'department_id'
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
