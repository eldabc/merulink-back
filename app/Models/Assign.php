<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assign extends Model
{
    protected $fillable = [
        'assign_code',
        'assign_date',
        'locker_id',
        'padlock_id',
        'employee_id',
    ];

    public function locker(): BelongsTo
    {
        return $this->belongsTo(Locker::class);
    }

    public function padlock(): BelongsTo
    {
        return $this->belongsTo(Padlock::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
